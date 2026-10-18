<?php

namespace Database\Seeders;

use Cms\Content\PostRepository;
use Cms\Database\Connection;
use Cms\Menus\MenuRepository;
use Cms\Settings\OptionsRepository;

/**
 * Contenido de la Iglesia de Cristo Metropolitana (migrado de iglesiametropolitana.org).
 * Es idempotente: puede ejecutarse varias veces sin duplicar páginas, menús ni medios.
 */
class MetropolitanaContentSeeder
{
    private const IMG = '/uploads/metropolitana/';

    public function __construct(
        private Connection $connection,
        private OptionsRepository $options,
        private PostRepository $posts,
        private MenuRepository $menus,
    ) {
    }

    public function run(int $authorId): void
    {
        $this->seedMedia($authorId);
        $this->seedOptions();
        $this->seedPages($authorId);
        $this->seedMenus();
    }

    private function seedMedia(int $authorId): void
    {
        $files = [
            'logo-web.png' => ['image/png', 'Logo Iglesia de Cristo Metropolitana'],
            'hero-slide1-v2.jpg' => ['image/jpeg', 'Bautismo'],
            'hero-slide2-v2.jpg' => ['image/jpeg', 'Culto principal'],
            'hero-slide3-v3.jpg' => ['image/jpeg', 'Hermanas de la congregación'],
            'hero-slide4-v3.jpg' => ['image/jpeg', 'Familia de la iglesia'],
            'hero-slide5.jpg' => ['image/jpeg', 'Clase de niños'],
            'clases-biblicas.jpg' => ['image/jpeg', 'Clases bíblicas'],
            'nuevos-miembros.jpg' => ['image/jpeg', 'Nuevos miembros'],
            'proximos-eventos.jpeg' => ['image/jpeg', 'Próximos eventos'],
        ];

        foreach ($files as $name => [$mime, $alt]) {
            $path = 'uploads/metropolitana/' . $name;
            $absolute = dirname(__DIR__, 2) . '/public/' . $path;

            if (!is_file($absolute) || $this->connection->get('media', 'id', ['file_path' => $path])) {
                continue;
            }

            $this->connection->insert('media', [
                'uploader_id' => $authorId,
                'file_name' => $name,
                'file_path' => $path,
                'mime_type' => $mime,
                'size' => filesize($absolute),
                'alt_text' => $alt,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function seedOptions(): void
    {
        $options = [
            'site_title' => 'Iglesia de Cristo Metropolitana',
            'tagline' => 'Un lugar donde puedes encontrarte con Jesús',
            'logo' => '/assets/brand/logo.png',
            'address' => 'Milla 8.1, entrando por Revisalud, Panamá',
            'map_query' => 'Iglesia de Cristo Metropolitana, Panamá',
            'maps_url' => 'https://www.google.com/maps/place/Iglesia+de+Cristo+Metropolitana/@9.0715841,-79.5198664,16z',
            'phone' => '+507 315 0103',
            'email' => 'idecristom@gmail.com',
            'whatsapp' => '+507 315 0103',
            'live_url' => '',
            'hero_image' => self::IMG . 'hero-slide2-v2.jpg',
            'social_links' => ['instagram' => 'https://www.instagram.com/icmetropolitana/'],
            'service_times' => [
                'Clases bíblicas | 9:30 – 10:15 AM | Todos los domingos',
                'Culto principal | 11:00 AM – 12:30 MD | Todos los domingos',
                'Clases virtuales | 7:00 – 8:00 PM | Miércoles y viernes · Zoom y FB Live',
            ],
            'schedule_note' => 'Todos los domingos en Milla 8.1. Ven tal como eres: te recibiremos con los brazos abiertos y la Palabra de Dios.',
            'welcome_text' => "Somos una comunidad cristiana fundamentada en la Biblia, dedicada a amar a Dios y servir al prójimo. Creemos en el poder transformador del Evangelio y buscamos vivir de acuerdo a las enseñanzas de Jesús cada día.\n\nSi buscas un lugar donde puedas conocer más de la Palabra, fortalecer tu espíritu y encontrar una familia espiritual, ¡te estamos esperando!",
            'welcome_images' => [self::IMG . 'hero-slide3-v3.jpg', self::IMG . 'hero-slide4-v3.jpg', self::IMG . 'hero-slide5.jpg'],
            'mission' => 'Glorificar a Dios, edificar a los creyentes y proclamar el evangelio de Jesucristo a toda persona, haciendo discípulos que vivan para la honra y la gloria de Dios.',
            'mission_quote' => 'Siguiendo a Cristo, sirviendo con amor y compartiendo esperanza.',
            'hero_slides' => [
                ['image' => self::IMG . 'hero-slide2-v2.jpg', 'text' => 'Así que la fe es por el oír, y el oír, por la palabra de Dios.', 'ref' => 'Romanos 10:17'],
                ['image' => self::IMG . 'clases-biblicas.jpg', 'text' => 'Por eso os dije que moriréis en vuestros pecados; porque si no creéis que yo soy, en vuestros pecados moriréis.', 'ref' => 'Juan 8:24'],
                ['image' => self::IMG . 'hero-slide3-v3.jpg', 'text' => 'Así que, arrepentíos y convertíos, para que sean borrados vuestros pecados; para que vengan de la presencia del Señor tiempos de refrigerio.', 'ref' => 'Hechos 3:19'],
                ['image' => self::IMG . 'nuevos-miembros.jpg', 'text' => 'Que si confesares con tu boca que Jesús es el Señor, y creyeres en tu corazón que Dios le levantó de los muertos, serás salvo.', 'ref' => 'Romanos 10:9-10'],
                ['image' => self::IMG . 'hero-slide1-v2.jpg', 'text' => 'Arrepentíos, y bautícese cada uno de vosotros en el nombre de Jesucristo para perdón de los pecados; y recibiréis el don del Espíritu Santo.', 'ref' => 'Hechos 2:38'],
                ['image' => self::IMG . 'hero-slide5.jpg', 'text' => 'Estad firmes y constantes, creciendo en la obra del Señor siempre, sabiendo que vuestro trabajo en el Señor no es en vano.', 'ref' => '1 Corintios 15:58'],
            ],
            'pillars' => [
                ['title' => 'Nuestra adoración a Dios', 'text' => 'Creemos que Dios es digno de toda honra, gloria y alabanza. Nuestras reuniones de adoración están centradas en Él, y procuramos que cada aspecto de nuestra vida sea una expresión continua de adoración y gratitud.', 'ref' => 'Hebreos 12:28'],
                ['title' => 'Un pueblo santo', 'text' => 'Como pueblo escogido por Dios, buscamos vivir vidas que reflejen su santidad: vestidos de compasión, bondad, humildad, mansedumbre y paciencia, permitiendo que Cristo transforme nuestro corazón cada día.', 'ref' => 'Colosenses 3:12'],
                ['title' => 'Una familia que ama', 'text' => 'El amor es la evidencia visible de nuestra relación con Dios. Porque Él nos amó primero, procuramos amarnos unos a otros, para que cada persona experimente el cuidado, la aceptación y la gracia del Señor.', 'ref' => '1 Juan 4:11-12'],
                ['title' => 'Sirviendo a los demás', 'text' => 'Siguiendo el ejemplo de Jesucristo, vivimos para servir. Reconocemos las necesidades de quienes nos rodean y buscamos ser instrumentos de bendición, apoyo y esperanza en nuestra comunidad.', 'ref' => 'Marcos 10:45'],
                ['title' => 'Compartiendo las buenas nuevas', 'text' => 'Estamos comprometidos con la proclamación del evangelio. La salvación se encuentra únicamente en Jesucristo y deseamos compartir este mensaje de esperanza con nuestra ciudad, nuestro país y el mundo.', 'ref' => 'Mateo 28:18-20'],
            ],
            'pillars_quote' => ['text' => 'Pero nosotros predicamos a Cristo crucificado.', 'ref' => '1 Corintios 1:23'],
            'next_steps' => [
                ['title' => 'Clases bíblicas', 'text' => 'Cada domingo de 9:30 a 10:15 AM estudiamos la Palabra juntos. Hay una clase para cada edad.', 'image' => self::IMG . 'clases-biblicas.jpg', 'url' => '/#horarios', 'cta' => 'Ver horarios'],
                ['title' => 'Nuevos miembros', 'text' => '¿Quieres ser parte de nuestra familia? Te acompañamos en tus primeros pasos en la fe.', 'image' => self::IMG . 'nuevos-miembros.jpg', 'url' => '/contacto', 'cta' => 'Contáctanos'],
                ['title' => 'Próximos eventos', 'text' => 'Convenciones, actividades familiares y cultos especiales. Entérate de todo en nuestras redes.', 'image' => self::IMG . 'proximos-eventos.jpeg', 'url' => 'https://www.instagram.com/icmetropolitana/', 'cta' => 'Ver en Instagram'],
            ],
        ];

        foreach ($options as $name => $value) {
            $this->options->set($name, $value);
        }
    }

    private function seedPages(int $authorId): void
    {
        $this->upsertPage($authorId, 'conocenos', 'Conócenos', 'Una comunidad cristiana fundamentada en la Biblia, dedicada a amar a Dios y servir al prójimo.', <<<'HTML'
<h2>Bienvenidos a la Iglesia de Cristo Metropolitana</h2>
<p>Somos una comunidad cristiana fundamentada en la Biblia, dedicada a amar a Dios y servir al prójimo. Creemos en el poder transformador del Evangelio y buscamos vivir de acuerdo a las enseñanzas de Jesús cada día.</p>
<p>Si buscas un lugar donde puedas conocer más de la Palabra, fortalecer tu espíritu y encontrar una familia espiritual, ¡te estamos esperando!</p>
<h2>Nuestra misión</h2>
<p>Glorificar a Dios, edificar a los creyentes y proclamar el evangelio de Jesucristo a toda persona, haciendo discípulos que vivan para la honra y la gloria de Dios.</p>
<blockquote><p>Siguiendo a Cristo, sirviendo con amor y compartiendo esperanza.</p></blockquote>
<h2>Lo que nos define</h2>
<h3>Nuestra adoración a Dios</h3>
<p>Creemos que Dios es digno de toda honra, gloria y alabanza. Nuestras reuniones de adoración están centradas en Él, y procuramos que cada aspecto de nuestra vida sea una expresión continua de adoración y gratitud. <em>(Hebreos 12:28)</em></p>
<h3>Un pueblo santo</h3>
<p>Como pueblo escogido por Dios, buscamos vivir vidas que reflejen su santidad. Nos esforzamos por vestirnos de compasión, bondad, humildad, mansedumbre y paciencia, permitiendo que Cristo transforme nuestro corazón cada día. <em>(Colosenses 3:12)</em></p>
<h3>Una familia que ama</h3>
<p>Creemos que el amor es la evidencia visible de nuestra relación con Dios. Porque Él nos amó primero, procuramos amarnos unos a otros, fortaleciendo una comunidad donde cada persona pueda experimentar el cuidado, la aceptación y la gracia del Señor. <em>(1 Juan 4:11-12)</em></p>
<h3>Sirviendo a los demás</h3>
<p>Siguiendo el ejemplo de Jesucristo, vivimos para servir. Reconocemos las necesidades de quienes nos rodean y buscamos ser instrumentos de bendición, apoyo y esperanza en nuestra comunidad. <em>(Marcos 10:45)</em></p>
<h3>Compartiendo las buenas nuevas de Jesús</h3>
<p>Estamos comprometidos con la proclamación del evangelio. Creemos que la salvación se encuentra únicamente en Jesucristo y deseamos compartir este mensaje de esperanza con nuestra ciudad, nuestro país y el mundo. <em>(Mateo 28:18-20)</em></p>
<blockquote><p>“Pero nosotros predicamos a Cristo crucificado.” — 1 Corintios 1:23</p></blockquote>
HTML);

        $this->upsertPage($authorId, 'contacto', 'Contáctanos', 'Escríbenos, llámanos o visítanos. Será un gusto atenderte.', <<<'HTML'
<p>¿Tienes preguntas, necesitas oración o quieres visitarnos por primera vez? Escríbenos por WhatsApp o correo, o llámanos. También puedes acompañarnos cualquier domingo en Milla 8.1, entrando por Revisalud.</p>
HTML);
    }

    private function upsertPage(int $authorId, string $slug, string $title, string $excerpt, string $content): void
    {
        $data = [
            'title' => $title,
            'excerpt' => $excerpt,
            'content' => trim($content),
            'status' => 'published',
        ];

        if ($id = $this->connection->get('posts', 'id', ['post_type' => 'page', 'slug' => $slug])) {
            $this->posts->update((int) $id, $data);

            return;
        }

        $this->posts->create($data + [
            'author_id' => $authorId,
            'post_type' => 'page',
            'slug' => $slug,
            'published_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function seedMenus(): void
    {
        $menus = [
            'primary' => ['Principal', [
                ['Inicio', '/'],
                ['Conócenos', '/conocenos'],
                ['Horarios', '/#horarios'],
                ['Contacto', '/contacto'],
            ]],
            'footer' => ['Pie de página', [
                ['Conócenos', '/conocenos'],
                ['Horarios', '/#horarios'],
                ['Contacto', '/contacto'],
            ]],
        ];

        foreach ($menus as $location => [$name, $items]) {
            $menu = $this->menus->findByLocation($location);
            $menuId = $menu ? (int) $menu['id'] : $this->menus->create($name, $location);

            $this->connection->delete('menu_items', ['menu_id' => $menuId]);

            foreach ($items as $order => [$label, $url]) {
                $this->menus->addItem($menuId, $label, $url, null, null, $order);
            }
        }
    }
}
