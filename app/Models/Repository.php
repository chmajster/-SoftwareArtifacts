<?php

declare(strict_types=1);

namespace App\Models;

final class Repository extends BaseModel
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO repositories (owner_id, name, slug, description, visibility, os_id, architecture_id, package_type_id, release_channel_id, version, listing_enabled, is_custom) VALUES (:owner_id,:name,:slug,:description,:visibility,:os_id,:architecture_id,:package_type_id,:release_channel_id,:version,:listing_enabled,:is_custom)');
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function byOwner(int $ownerId): array
    {
        $stmt = $this->db->prepare('SELECT r.*, os.name AS os_name, a.name AS arch_name, pt.name AS package_name, rc.name AS channel_name FROM repositories r LEFT JOIN operating_systems os ON os.id = r.os_id LEFT JOIN architectures a ON a.id = r.architecture_id LEFT JOIN package_types pt ON pt.id = r.package_type_id LEFT JOIN release_channels rc ON rc.id = r.release_channel_id WHERE owner_id = ? ORDER BY r.id DESC');
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll();
    }

    public function allPublic(): array
    {
        return $this->db->query("SELECT id, name, slug, description, visibility FROM repositories WHERE visibility='public' ORDER BY id DESC")->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM repositories WHERE slug = ? LIMIT 1');
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    public function all(): array
    {
        return $this->db->query('SELECT r.*, u.username FROM repositories r JOIN users u ON u.id = r.owner_id ORDER BY r.id DESC')->fetchAll();
    }
}
