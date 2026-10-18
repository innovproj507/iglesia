<?php

namespace Cms\Admin\Controllers;

use Cms\Database\Connection;
use Cms\Http\Request;
use Cms\Http\Response;
use DateTimeImmutable;

class DashboardController extends AdminController
{
    private const MONTHS = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
    private const WEEKDAYS = ['domingo' => 0, 'lunes' => 1, 'martes' => 2, 'miércoles' => 3, 'miercoles' => 3, 'jueves' => 4, 'viernes' => 5, 'sábado' => 6, 'sabado' => 6];

    public function __construct(
        \Cms\Themes\View $view,
        \Cms\Auth\AuthManager $auth,
        \Cms\Admin\AdminMenu $adminMenu,
        private Connection $connection,
    ) {
        parent::__construct($view, $auth, $adminMenu);
    }

    public function index(Request $request): Response
    {
        $widgets = [];
        do_action('admin.dashboard.widgets', $widgets);

        $now = new DateTimeImmutable();
        $monthStart = $now->format('Y-m-01 00:00:00');
        $count = fn (array $where) => $this->connection->count('posts', $where);

        $stats = [
            'sermons' => $count(['post_type' => 'sermon']),
            'sermonsMonth' => $count(['post_type' => 'sermon', 'created_at[>=]' => $monthStart]),
            'posts' => $count(['post_type' => 'post']),
            'postsMonth' => $count(['post_type' => 'post', 'created_at[>=]' => $monthStart]),
            'pages' => $count(['post_type' => 'page']),
            'media' => $this->connection->count('media'),
            'mediaMonth' => $this->connection->count('media', ['created_at[>=]' => $monthStart]),
            'users' => $this->connection->count('users'),
            'published' => $count(['status' => 'published']),
            'drafts' => $count(['status' => 'draft']),
            'scheduled' => $count(['status' => 'scheduled']),
        ];

        $schedule = $this->upcomingActivities($now);

        return $this->render('dashboard', [
            'title' => 'Dashboard',
            'stats' => $stats,
            'monthly' => $this->monthlySeries($now),
            'recent' => $this->recentContent(),
            'recentMedia' => $this->connection->medoo()->select('media', '*', ['ORDER' => ['id' => 'DESC'], 'LIMIT' => 4]),
            'users' => $this->connection->medoo()->select('users', ['id', 'name', 'email'], ['ORDER' => ['id' => 'ASC'], 'LIMIT' => 5]),
            'upcoming' => $schedule,
            'calendar' => $this->calendar($now, $schedule),
            'verse' => $this->verseOfTheDay($now),
            'now' => $now,
        ], 'dashboard');
    }

    /** Contenido publicado por mes en los últimos 6 meses. */
    private function monthlySeries(DateTimeImmutable $now): array
    {
        $from = $now->modify('first day of -5 months')->format('Y-m-01');
        $rows = $this->connection->pdo()->query(
            "SELECT DATE_FORMAT(COALESCE(published_at, created_at), '%Y-%m') AS ym, COUNT(*) AS total
             FROM posts WHERE status = 'published' AND COALESCE(published_at, created_at) >= " . $this->connection->pdo()->quote($from) . '
             GROUP BY ym'
        )->fetchAll(\PDO::FETCH_KEY_PAIR);

        $series = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = $now->modify("first day of -{$i} months");
            $series[] = [
                'label' => self::MONTHS[(int) $month->format('n') - 1],
                'value' => (int) ($rows[$month->format('Y-m')] ?? 0),
            ];
        }

        return $series;
    }

    private function recentContent(): array
    {
        return $this->connection->pdo()->query(
            'SELECT p.id, p.title, p.post_type, p.status, p.slug, p.updated_at, p.created_at, u.name AS author
             FROM posts p LEFT JOIN users u ON u.id = p.author_id
             ORDER BY p.updated_at DESC LIMIT 6'
        )->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Próxima fecha de cada actividad del horario ("Título | Horario | Día").
     */
    private function upcomingActivities(DateTimeImmutable $now): array
    {
        $items = [];

        foreach (service_schedule() as $index => $activity) {
            $days = $this->weekdaysIn(mb_strtolower($activity['day'] . ' ' . $activity['title']));
            $time = $this->startTime($activity['time']);

            if (!$days) {
                continue;
            }

            $next = null;

            foreach ($days as $weekday) {
                for ($offset = 0; $offset < 8; $offset++) {
                    $candidate = $now->setTime(0, 0)->modify("+{$offset} days");

                    if ((int) $candidate->format('w') !== $weekday) {
                        continue;
                    }

                    $candidate = $candidate->setTime($time[0], $time[1]);

                    if ($candidate >= $now && (!$next || $candidate < $next)) {
                        $next = $candidate;
                    }
                }
            }

            $items[] = $activity + ['index' => $index, 'weekdays' => $days, 'next' => $next];
        }

        usort($items, fn ($a, $b) => $a['next'] <=> $b['next']);

        return $items;
    }

    private function weekdaysIn(string $text): array
    {
        $found = [];

        foreach (self::WEEKDAYS as $name => $number) {
            if (preg_match('/\b' . $name . '/u', $text)) {
                $found[$number] = $number;
            }
        }

        return array_values($found);
    }

    /** @return array{0:int,1:int} */
    private function startTime(string $text): array
    {
        if (!preg_match('/(\d{1,2}):(\d{2})\s*(AM|PM|MD)?/i', $text, $m)) {
            return [9, 0];
        }

        $hour = (int) $m[1];
        $meridiem = strtoupper($m[3] ?? '');

        if ($meridiem === '' && preg_match('/\b(AM|PM)\b/i', $text, $last)) {
            $meridiem = strtoupper($last[1]);
        }

        if ($meridiem === 'PM' && $hour < 12) {
            $hour += 12;
        }

        return [$hour, (int) $m[2]];
    }

    private function calendar(DateTimeImmutable $now, array $schedule): array
    {
        $first = $now->modify('first day of this month')->setTime(0, 0);
        $leading = ((int) $first->format('N')) - 1; // semana inicia en lunes
        $start = $first->modify("-{$leading} days");
        $weeks = [];

        for ($w = 0; $w < 6; $w++) {
            $week = [];

            for ($d = 0; $d < 7; $d++) {
                $day = $start->modify('+' . ($w * 7 + $d) . ' days');
                $dots = [];

                foreach ($schedule as $activity) {
                    if (in_array((int) $day->format('w'), $activity['weekdays'], true)) {
                        $dots[] = $activity['index'];
                    }
                }

                $week[] = [
                    'day' => (int) $day->format('j'),
                    'inMonth' => $day->format('m') === $now->format('m'),
                    'today' => $day->format('Y-m-d') === $now->format('Y-m-d'),
                    'dots' => array_values(array_unique($dots)),
                ];
            }

            $weeks[] = $week;

            if ($start->modify('+' . (($w + 1) * 7) . ' days')->format('m') !== $now->format('m')) {
                break;
            }
        }

        $months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

        return ['label' => ucfirst($months[(int) $now->format('n') - 1]) . ' ' . $now->format('Y'), 'weeks' => $weeks];
    }

    private function verseOfTheDay(DateTimeImmutable $now): array
    {
        $verses = array_values(array_filter(option_array('hero_slides'), fn ($s) => !empty($s['text'])));

        if (!$verses) {
            return ['text' => 'Lámpara es a mis pies tu palabra, y lumbrera a mi camino.', 'ref' => 'Salmo 119:105'];
        }

        return $verses[(int) $now->format('z') % count($verses)];
    }
}
