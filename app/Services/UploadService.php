<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class UploadService
{
    public function __construct(private array $config)
    {
    }

    public function store(array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload failed.');
        }

        $originalName = $file['name'];
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowed = $this->config['security']['allowed_extensions'];

        if (!in_array($extension, $allowed, true)) {
            throw new RuntimeException('File extension is not allowed.');
        }

        $maxBytes = ((int)$this->config['security']['max_upload_mb']) * 1024 * 1024;
        if ((int)$file['size'] > $maxBytes) {
            throw new RuntimeException('File is too large.');
        }

        $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName) ?: 'artifact.bin';
        $storedName = bin2hex(random_bytes(12)) . '_' . $safeName;
        $targetPath = rtrim($this->config['storage']['artifacts_path'], '/') . '/' . $storedName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new RuntimeException('Cannot move uploaded file.');
        }

        return [
            'stored_name' => $storedName,
            'original_name' => $originalName,
            'mime_type' => mime_content_type($targetPath) ?: 'application/octet-stream',
            'size_bytes' => (int)$file['size'],
            'checksum_sha256' => hash_file('sha256', $targetPath),
            'path' => $targetPath,
        ];
    }
}
