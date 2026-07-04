<?php

class TestimonialModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function approved(int $limit = 10, int $offset = 0): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM `testimonials` WHERE `status` = 'approved'
             ORDER BY `created_at` DESC LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function pending(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM `testimonials` WHERE `status` = 'pending' ORDER BY `created_at` DESC"
        );
        return $stmt->fetchAll();
    }

    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM `testimonials` ORDER BY `created_at` DESC");
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `testimonials` WHERE `id` = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO `testimonials` (`name`, `email`, `phone`, `gender`, `message`, `photo_url`, `status`)
             VALUES (:name, :email, :phone, :gender, :message, :photo_url, 'pending')"
        );
        $stmt->execute([
            ':name'      => $data['name'],
            ':email'     => $data['email'] ?? '',
            ':phone'     => $data['phone'] ?? '',
            ':gender'    => $data['gender'] ?? '',
            ':message'   => $data['message'],
            ':photo_url' => $data['photo_url'] ?? '',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function approve(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE `testimonials` SET `status` = 'approved' WHERE `id` = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function reject(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE `testimonials` SET `status` = 'rejected' WHERE `id` = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM `testimonials` WHERE `id` = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function count(?string $status = null): int
    {
        if ($status) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM `testimonials` WHERE `status` = :status");
            $stmt->execute([':status' => $status]);
            return (int) $stmt->fetchColumn();
        }
        return (int) $this->db->query("SELECT COUNT(*) FROM `testimonials`")->fetchColumn();
    }

    public function paginate(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $total = $this->count();
        $items = $this->approved($perPage, $offset);
        return [
            'items'    => $items,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
            'pages'    => (int) ceil($total / $perPage),
        ];
    }
}
