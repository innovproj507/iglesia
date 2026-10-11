<?php

namespace Cms\Media;

use Cms\Database\Connection;

class MediaLibrary
{
    public function __construct(private Connection $connection, private Uploader $uploader)
    {
    }

    public function upload(array $file, ?int $uploaderId, ?string $altText = null): array
    {
        $stored = $this->uploader->store($file);

        $id = $this->connection->insert('media', [
            'uploader_id' => $uploaderId,
            'file_name' => $stored['file_name'],
            'file_path' => $stored['file_path'],
            'mime_type' => $stored['mime_type'],
            'size' => $stored['size'],
            'alt_text' => $altText,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->find((int) $id);
    }

    public function find(int $id): ?array
    {
        return $this->connection->medoo()->get('media', '*', ['id' => $id]) ?: null;
    }

    public function paginate(int $page = 1, int $perPage = 24): array
    {
        $items = $this->connection->medoo()->select('media', '*', [
            'ORDER' => ['id' => 'DESC'],
            'LIMIT' => [($page - 1) * $perPage, $perPage],
        ]);

        return ['items' => $items, 'total' => $this->connection->count('media'), 'page' => $page, 'per_page' => $perPage];
    }

    public function delete(int $id, string $uploadsPath): void
    {
        $media = $this->find($id);

        if ($media) {
            $fullPath = rtrim($uploadsPath, '/\\') . '/' . str_replace('uploads/', '', $media['file_path']);

            if (is_file($fullPath)) {
                unlink($fullPath);
            }
        }

        $this->connection->delete('media', ['id' => $id]);
    }
}
