<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

if (!file_exists(__DIR__ . '/config.php')) {
    echo json_encode(['success' => false, 'message' => 'Sistema no configurado.']);
    exit;
}

require_once __DIR__ . '/db.php';

if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión.']);
    exit;
}

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$gender  = trim($_POST['gender'] ?? 'other');
$message = trim($_POST['message'] ?? '');

$errors = [];

if (empty($name))    $errors[] = 'El nombre es obligatorio.';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Correo electrónico no válido.';
if (empty($message)) $errors[] = 'El testimonio no puede estar vacío.';
if (!in_array($gender, ['male', 'female', 'other'])) $gender = 'other';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => 'Corrige los siguientes errores:', 'errors' => $errors]);
    exit;
}

// Procesar foto opcional
$photo_url = null;
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['photo'];

    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'La foto no puede superar los 5MB.']);
        exit;
    }

    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $mime = mime_content_type($file['tmp_name']);
    if (!in_array($mime, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Formato de imagen no permitido. Usa JPG, PNG, GIF o WEBP.']);
        exit;
    }

    $upload_dir = __DIR__ . '/uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('test_', true) . '.' . $ext;
    $target = $upload_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $target)) {
        $photo_url = 'uploads/' . $filename;
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar la foto.']);
        exit;
    }
}

try {
    $stmt = $pdo->prepare("INSERT INTO `testimonials` (name, email, phone, gender, message, photo_url, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([$name, $email, $phone, $gender, $message, $photo_url]);

    echo json_encode([
        'success' => true,
        'message' => '¡Gracias por tu testimonio! Será revisado por el equipo y, una vez aprobado, aparecerá en la página.'
    ]);
} catch (PDOException $e) {
    error_log("Error testimonio: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error técnico. Inténtalo más tarde.']);
}
