<?php

namespace Cms\Content;

use Cms\Database\Connection;

class PostRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function find(int $id): ?array
    {
        return $this->connection->medoo()->get('posts', '*', ['id' => $id]) ?: null;
    }

    public function findBySlug(string $postType, string $slug): ?array
    {
        return $this->connection->medoo()->get('posts', '*', [
            'post_type' => $postType,
            'slug' => $slug,
        ]) ?: null;
    }

    public function paginate(string $postType, int $page = 1, int $perPage = 10, ?string $status = null, ?int $authorId = null, ?string $search = null): array
    {
        $where = $postType !== '' ? ['post_type' => $postType] : [];

        if ($search !== null && $search !== '') {
            $where['title[~]'] = $search;
        }

        if ($status) {
            $where['status'] = $status;
        }

        if ($authorId) {
            $where['author_id'] = $authorId;
        }

        $where['ORDER'] = ['published_at' => 'DESC', 'id' => 'DESC'];
        $where['LIMIT'] = [($page - 1) * $perPage, $perPage];

        $items = $this->connection->medoo()->select('posts', '*', $where);
        unset($where['LIMIT'], $where['ORDER']);
        $total = $this->connection->medoo()->count('posts', $where);

        return ['items' => $items, 'total' => $total, 'page' => $page, 'per_page' => $perPage];
    }

    public function create(array $data): int
    {
        $now = date('Y-m-d H:i:s');

        return (int) $this->connection->insert('posts', array_merge($data, [
            'uuid' => $data['uuid'] ?? $this->uuid(),
            'created_at' => $now,
            'updated_at' => $now,
        ]));
    }

    public function update(int $id, array $data): void
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->connection->update('posts', $data, ['id' => $id]);
    }

    public function delete(int $id): void
    {
        $this->connection->delete('posts', ['id' => $id]);
    }

    public function getMeta(int $postId, string $key, mixed $default = null): mixed
    {
        $value = $this->connection->get('postmeta', 'meta_value', ['post_id' => $postId, 'meta_key' => $key]);

        return $value ?? $default;
    }

    public function allMeta(int $postId): array
    {
        $rows = $this->connection->select('postmeta', ['meta_key', 'meta_value'], ['post_id' => $postId]);
        $meta = [];

        foreach ($rows as $row) {
            $meta[$row['meta_key']] = $row['meta_value'];
        }

        return $meta;
    }

    public function setMeta(int $postId, string $key, mixed $value): void
    {
        $existing = $this->connection->get('postmeta', 'id', ['post_id' => $postId, 'meta_key' => $key]);

        if ($existing) {
            $this->connection->update('postmeta', ['meta_value' => $value], ['id' => $existing]);
        } else {
            $this->connection->insert('postmeta', ['post_id' => $postId, 'meta_key' => $key, 'meta_value' => $value]);
        }
    }

    private function uuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
