<?php

class GalleryModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM `gallery` ORDER BY `display_order` ASC, `id` ASC");
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `gallery` WHERE `id` = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO `gallery` (`image_url`, `caption`, `display_order`)
             VALUES (:image_url, :caption, :display_order)"
        );
        $stmt->execute([
            ':image_url'     => $data['image_url'],
            ':caption'       => $data['caption'] ?? '',
            ':display_order' => $data['display_order'] ?? 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE `gallery` SET `image_url` = :image_url, `caption` = :caption,
             `display_order` = :display_order WHERE `id` = :id"
        );
        return $stmt->execute([
            ':image_url'     => $data['image_url'] ?? '',
            ':caption'       => $data['caption'] ?? '',
            ':display_order' => $data['display_order'] ?? 0,
            ':id'            => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM `gallery` WHERE `id` = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function allOrdered(): array
    {
        return $this->all();
    }

    public function count(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM `gallery`")->fetchColumn();
    }
}
