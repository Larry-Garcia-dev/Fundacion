<?php
class Settings
{
    private static ?array $cache = null;

    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $pdo    = Database::connect();
        $stmt   = $pdo->query("SELECT key_name, value_text FROM `settings`");
        $result = [];

        while ($row = $stmt->fetch()) {
            $result[$row['key_name']] = $row['value_text'];
        }

        self::$cache = $result;
        return $result;
    }

    public static function get(string $key, string $default = ''): string
    {
        $all = self::all();
        return $all[$key] ?? $default;
    }

    public static function set(string $key, string $value): void
    {
        $pdo  = Database::connect();
        $stmt = $pdo->prepare("UPDATE `settings` SET `value_text` = ? WHERE `key_name` = ?");
        $stmt->execute([$value, $key]);
        self::$cache[$key] = $value;
    }

    public static function setMany(array $pairs): void
    {
        $pdo = Database::connect();
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("UPDATE `settings` SET `value_text` = ? WHERE `key_name` = ?");

        foreach ($pairs as $key => $value) {
            $stmt->execute([$value, $key]);
        }

        $pdo->commit();
        self::$cache = null;
    }

    public static function ensureExists(string $key, string $default): void
    {
        $pdo   = Database::connect();
        $check = $pdo->prepare("SELECT COUNT(*) FROM `settings` WHERE `key_name` = ?");
        $check->execute([$key]);

        if ($check->fetchColumn() == 0) {
            $pdo->prepare("INSERT INTO `settings` (key_name, value_text) VALUES (?, ?)")
                ->execute([$key, $default]);
        }
    }

    public static function clearCache(): void
    {
        self::$cache = null;
    }
}
