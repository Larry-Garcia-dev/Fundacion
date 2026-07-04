<?php

class OrgValueModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM `org_values` ORDER BY `display_order` ASC, `id` ASC");
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `org_values` WHERE `id` = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO `org_values` (`title`, `display_order`)
             VALUES (:title, :display_order)"
        );
        $stmt->execute([
            ':title'         => $data['title'],
            ':display_order' => $data['display_order'] ?? 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE `org_values` SET `title` = :title, `display_order` = :display_order
             WHERE `id` = :id"
        );
        return $stmt->execute([
            ':title'         => $data['title'] ?? '',
            ':display_order' => $data['display_order'] ?? 0,
            ':id'            => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM `org_values` WHERE `id` = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function allOrdered(): array
    {
        return $this->all();
    }

    public function count(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM `org_values`")->fetchColumn();
    }
}
