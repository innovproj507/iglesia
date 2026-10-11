<?php

namespace Cms\Media;

use RuntimeException;

class Uploader
{
    private const ALLOWED_MIME = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
        'audio/mpeg', 'audio/mp4', 'audio/ogg',
        'video/mp4', 'video/webm',
        'application/pdf',
    ];

    private const MAX_SIZE = 20 * 1024 * 1024; // 20MB

    public function __construct(private string $uploadsPath)
    {
    }

    public function store(array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Error al subir el archivo.');
        }

        if ($file['size'] > self::MAX_SIZE) {
            throw new RuntimeException('El archivo excede el tamaño máximo permitido (20MB).');
        }

        $mimeType = mime_content_type($file['tmp_name']) ?: 'application/octet-stream';

        if (!in_array($mimeType, self::ALLOWED_MIME, true)) {
            throw new RuntimeException('Tipo de archivo no permitido.');
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safeName = bin2hex(random_bytes(8)) . ($extension ? '.' . strtolower($extension) : '');
        $subDir = date('Y/m');
        $destinationDir = $this->uploadsPath . '/' . $subDir;

        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        $destination = $destinationDir . '/' . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new RuntimeException('No se pudo guardar el archivo.');
        }

        return [
            'file_name' => $file['name'],
            'file_path' => 'uploads/' . $subDir . '/' . $safeName,
            'mime_type' => $mimeType,
            'size' => $file['size'],
        ];
    }
}
