<?php

class ContactMessageModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM `contact_messages` ORDER BY `created_at` DESC");
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `contact_messages` WHERE `id` = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO `contact_messages` (`name`, `email`, `phone`, `message`, `status`)
             VALUES (:name, :email, :phone, :message, 'unread')"
        );
        $stmt->execute([
            ':name'    => $data['name'],
            ':email'   => $data['email'] ?? '',
            ':phone'   => $data['phone'] ?? '',
            ':message' => $data['message'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function markRead(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE `contact_messages` SET `status` = 'read' WHERE `id` = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function archive(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE `contact_messages` SET `status` = 'archived' WHERE `id` = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM `contact_messages` WHERE `id` = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function count(?string $status = null): int
    {
        if ($status) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM `contact_messages` WHERE `status` = :status");
            $stmt->execute([':status' => $status]);
            return (int) $stmt->fetchColumn();
        }
        return (int) $this->db->query("SELECT COUNT(*) FROM `contact_messages`")->fetchColumn();
    }

    public function unreadCount(): int
    {
        return $this->count('unread');
    }

    public function allFiltered(string $filter = 'all'): array
    {
        if ($filter === 'all') {
            return $this->all();
        }
        $stmt = $this->db->prepare("SELECT * FROM `contact_messages` WHERE `status` = :status ORDER BY `created_at` DESC");
        $stmt->execute([':status' => $filter]);
        return $stmt->fetchAll();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE `contact_messages` SET `status` = :status WHERE `id` = :id");
        return $stmt->execute([
            ':status' => $data['status'],
            ':id'     => $id,
        ]);
    }

    public function latest(int $limit = 5): array
    {
        $stmt = $this->db->prepare("SELECT * FROM `contact_messages` ORDER BY `created_at` DESC LIMIT :lim");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
