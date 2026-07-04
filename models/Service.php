<?php

class ServiceModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM `services` ORDER BY `display_order` ASC, `id` ASC");
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `services` WHERE `id` = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO `services` (`icon`, `title`, `description`, `display_order`)
             VALUES (:icon, :title, :description, :display_order)"
        );
        $stmt->execute([
            ':icon'        => $data['icon'] ?? '',
            ':title'       => $data['title'],
            ':description' => $data['description'] ?? '',
            ':display_order' => $data['display_order'] ?? 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE `services` SET `icon` = :icon, `title` = :title,
             `description` = :description, `display_order` = :display_order
             WHERE `id` = :id"
        );
        return $stmt->execute([
            ':icon'        => $data['icon'] ?? '',
            ':title'       => $data['title'] ?? '',
            ':description' => $data['description'] ?? '',
            ':display_order' => $data['display_order'] ?? 0,
            ':id'          => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM `services` WHERE `id` = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function allOrdered(): array
    {
        return $this->all();
    }

    public function count(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM `services`")->fetchColumn();
    }
}
