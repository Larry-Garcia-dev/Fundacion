<?php

class UserModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `users` WHERE `username` = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `users` WHERE `id` = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(string $username, string $email, string $password): int
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare(
            "INSERT INTO `users` (`username`, `email`, `password`)
             VALUES (:username, :email, :password)"
        );
        $stmt->execute([
            ':username' => $username,
            ':email'    => $email,
            ':password' => $hash,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function count(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM `users`")->fetchColumn();
    }

    public function verifyPassword(string $hash, string $password): bool
    {
        return password_verify($password, $hash);
    }
}
