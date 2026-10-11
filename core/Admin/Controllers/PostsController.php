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
        $postType = $this->postTypes->get($type);
        $user = $this->auth->user();

        $authorId = $user && !$user->can('edit_others_posts') ? $user->id : null;
        $result = $this->posts->paginate($type, (int) $request->input('page', 1), 20, null, $authorId);

        return $this->render('posts/index', [
            'title' => $postType?->label ?? ucfirst($type),
            'postType' => $type,
            'postTypeLabel' => $postType?->label ?? ucfirst($type),
            'posts' => $result['items'],
        ], $type === 'page' ? 'pages' : 'posts');
    }

    public function create(Request $request): Response
    {
        $type = (string) $request->input('type', 'post');

        return $this->render('posts/edit', [
            'title' => 'Nueva entrada',
            'postType' => $type,
            'post' => null,
        ], $type === 'page' ? 'pages' : 'posts');
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
        ], $post['post_type'] === 'page' ? 'pages' : 'posts');
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
