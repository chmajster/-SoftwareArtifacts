<?php

declare(strict_types=1);

namespace App\Models;

final class Artifact extends BaseModel
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO artifacts (repository_id, uploader_id, name, version, description, os_id, architecture_id, package_type_id, changelog, status, is_latest, is_hidden, is_deprecated, file_original_name, file_stored_name, file_size_bytes, file_mime_type, checksum_sha256, published_at) VALUES (:repository_id,:uploader_id,:name,:version,:description,:os_id,:architecture_id,:package_type_id,:changelog,:status,:is_latest,:is_hidden,:is_deprecated,:file_original_name,:file_stored_name,:file_size_bytes,:file_mime_type,:checksum_sha256,:published_at)');
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function byRepository(int $repoId, bool $publicOnly = false): array
    {
        $sql = 'SELECT ar.*, os.name AS os_name, a.name AS arch_name, pt.name AS package_name FROM artifacts ar LEFT JOIN operating_systems os ON os.id = ar.os_id LEFT JOIN architectures a ON a.id = ar.architecture_id LEFT JOIN package_types pt ON pt.id = ar.package_type_id WHERE ar.repository_id = ?';
        if ($publicOnly) {
            $sql .= " AND ar.status = 'published' AND ar.is_hidden = 0";
        }
        $sql .= ' ORDER BY ar.published_at DESC, ar.id DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$repoId]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM artifacts WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function all(): array
    {
        return $this->db->query('SELECT ar.id, ar.name, ar.version, ar.status, ar.download_count, r.name as repository_name FROM artifacts ar JOIN repositories r ON r.id = ar.repository_id ORDER BY ar.id DESC')->fetchAll();
    }

    public function incrementDownloads(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE artifacts SET download_count = download_count + 1 WHERE id = ?');
        $stmt->execute([$id]);
    }
}
