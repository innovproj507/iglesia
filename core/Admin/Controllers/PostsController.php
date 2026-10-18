<?php

namespace Cms\Admin\Controllers;

use Cms\Admin\AdminMenu;
use Cms\Auth\AuthManager;
use Cms\Content\PostRepository;
use Cms\Content\PostTypeRegistry;
use Cms\Http\Request;
use Cms\Http\Response;
use Cms\Themes\View;

class PostsController extends AdminController
{
    public function __construct(
        View $view,
        AuthManager $auth,
        AdminMenu $adminMenu,
        private PostRepository $posts,
        private PostTypeRegistry $postTypes,
    ) {
        parent::__construct($view, $auth, $adminMenu);
        $this->postTypes->boot();
    }

    public function index(Request $request): Response
    {
        $type = (string) $request->input('type', 'post');
        $search = trim((string) $request->input('q', ''));
        $status = (string) $request->input('status', '');
        $status = in_array($status, ['draft', 'published', 'scheduled'], true) ? $status : null;
        $isAll = $type === 'all';
        $postType = $isAll ? null : $this->postTypes->get($type);
        $user = $this->auth->user();
        $label = $isAll ? 'Todo el contenido' : ($postType?->label ?? ucfirst($type));

        $authorId = $user && !$user->can('edit_others_posts') ? $user->id : null;
        $result = $this->posts->paginate($isAll ? '' : $type, (int) $request->input('page', 1), 20, $status, $authorId, $search);

        return $this->render('posts/index', [
            'title' => $search !== '' ? 'Resultados para “' . $search . '”' : $label,
            'postType' => $type,
            'postTypeLabel' => $label,
            'posts' => $result['items'],
            'total' => $result['total'],
            'search' => $search,
            'status' => $status,
            'postTypes' => $this->postTypes,
        ], $isAll ? 'dashboard' : $this->menuSlug($type));
    }

    /** Ítem del menú lateral que corresponde a un tipo de contenido. */
    private function menuSlug(string $type): string
    {
        foreach ($this->adminMenu->items($this->auth->user()) as $item) {
            if (str_contains($item['url'], 'type=' . $type)) {
                return $item['slug'];
            }
        }

        return 'posts';
    }

    public function create(Request $request): Response
    {
        $type = (string) $request->input('type', 'post');

        return $this->render('posts/edit', [
            'title' => 'Crear ' . mb_strtolower($this->postTypes->get($type)?->labelSingular ?? $type),
            'postType' => $type,
            'post' => null,
        ], $this->menuSlug($type));
    }

    public function edit(Request $request): Response
    {
        $id = (int) $request->param('id');
        $post = $this->posts->find($id);

        if (!$post) {
            return Response::notFound();
        }

        return $this->render('posts/edit', [
            'title' => 'Editar: ' . $post['title'],
            'postType' => $post['post_type'],
            'post' => $post,
        ], $this->menuSlug($post['post_type']));
    }

    public function store(Request $request): Response
    {
        $type = (string) $request->input('post_type', 'post');
        $title = trim((string) $request->input('title', ''));
        $status = (string) $request->input('status', 'draft');
        $user = $this->auth->user();

        $id = $this->posts->create([
            'author_id' => $user->id,
            'post_type' => $type,
            'title' => $title !== '' ? $title : 'Sin título',
            'slug' => $this->slugify($request->input('slug') ?: $title),
            'content' => $request->input('content', ''),
            'excerpt' => $request->input('excerpt', ''),
            'status' => $status,
            'published_at' => $status === 'published' ? date('Y-m-d H:i:s') : null,
        ]);

        do_action('admin.post.saved', $id, $type, $request);

        return Response::redirect('/admin/posts/' . $id . '/edit');
    }

    public function update(Request $request): Response
    {
        $id = (int) $request->param('id');
        $post = $this->posts->find($id);

        if (!$post) {
            return Response::notFound();
        }

        $title = trim((string) $request->input('title', ''));
        $status = (string) $request->input('status', $post['status']);

        $this->posts->update($id, [
            'title' => $title !== '' ? $title : $post['title'],
            'slug' => $this->slugify($request->input('slug') ?: $title),
            'content' => $request->input('content', ''),
            'excerpt' => $request->input('excerpt', ''),
            'status' => $status,
            'published_at' => $status === 'published' ? ($post['published_at'] ?? date('Y-m-d H:i:s')) : $post['published_at'],
        ]);

        do_action('admin.post.saved', $id, $post['post_type'], $request);

        return Response::redirect('/admin/posts/' . $id . '/edit');
    }

    public function destroy(Request $request): Response
    {
        $id = (int) $request->param('id');
        $post = $this->posts->find($id);
        $this->posts->delete($id);

        return Response::redirect('/admin/posts?type=' . ($post['post_type'] ?? 'post'));
    }

    private function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? $value;
        $value = trim($value, '-');

        return $value !== '' ? $value : bin2hex(random_bytes(4));
    }
}
