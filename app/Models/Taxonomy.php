<?php

declare(strict_types=1);

namespace App\Models;

final class Taxonomy extends BaseModel
{
    public function all(string $table): array
    {
        $allowed = ['operating_systems', 'architectures', 'package_types', 'release_channels'];
        if (!in_array($table, $allowed, true)) {
            return [];
        }
        return $this->db->query("SELECT * FROM {$table} ORDER BY sort_order, name")->fetchAll();
    }

    public function create(string $table, string $name, string $slug): void
    {
        $allowed = ['operating_systems', 'architectures', 'package_types', 'release_channels'];
        if (!in_array($table, $allowed, true)) {
            return;
        }
        $stmt = $this->db->prepare("INSERT INTO {$table} (name, slug, is_custom) VALUES (?, ?, 1)");
        $stmt->execute([$name, $slug]);
    }
}
