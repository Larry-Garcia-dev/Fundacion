<?php
class Uploader
{
    private const MAX_SIZE = 5 * 1024 * 1024;
    private const ALLOWED  = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private const UPLOAD_DIR = __DIR__ . '/../uploads/';

    public static function image(string $fileInput, string $existingPath = ''): string
    {
        if (!isset($_FILES[$fileInput]) || $_FILES[$fileInput]['error'] === UPLOAD_ERR_NO_FILE) {
            return $existingPath;
        }

        $file = $_FILES[$fileInput];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error al subir archivo (Código: {$file['error']})");
        }

        if ($file['size'] > self::MAX_SIZE) {
            throw new Exception("El archivo supera el límite de 5MB.");
        }

        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, self::ALLOWED)) {
            throw new Exception("Tipo no permitido. Solo JPG, PNG, GIF y WEBP.");
        }

        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0755, true);
        }

        $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
        $name = uniqid('img_', true) . '.' . $ext;
        $path = self::UPLOAD_DIR . $name;

        if (!move_uploaded_file($file['tmp_name'], $path)) {
            throw new Exception("No se pudo guardar el archivo.");
        }

        if (!empty($existingPath) && str_starts_with($existingPath, 'uploads/')) {
            $old = __DIR__ . '/../' . $existingPath;
            if (file_exists($old)) unlink($old);
        }

        return 'uploads/' . $name;
    }

    public static function resolvePath(string $path, string $prefix = ''): string
    {
        if (str_starts_with($path, 'http')) return $path;
        return $prefix . $path;
    }
}
