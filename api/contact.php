<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

require_once __DIR__ . '/../core/Database.php';

try {
    $db = Database::connect();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión con la base de datos.']);
    exit;
}

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');

$errors = [];

if (empty($name))    $errors[] = 'El nombre es obligatorio.';
if (empty($email))   $errors[] = 'El correo electrónico es obligatorio.';
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'El correo electrónico ingresado no es válido.';
if (empty($message)) $errors[] = 'El mensaje no puede estar vacío.';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => 'Por favor, corrija los siguientes errores:', 'errors' => $errors]);
    exit;
}

try {
    $stmt = $db->prepare(
        "INSERT INTO `contact_messages` (`name`, `email`, `phone`, `message`, `status`)
         VALUES (:name, :email, :phone, :message, 'unread')"
    );
    $stmt->execute([
        ':name'    => htmlspecialchars($name),
        ':email'   => htmlspecialchars($email),
        ':phone'   => htmlspecialchars($phone),
        ':message' => htmlspecialchars($message),
    ]);

    echo json_encode([
        'success' => true,
        'message' => '¡Muchas gracias! Tu mensaje ha sido enviado con éxito. Nos pondremos en contacto contigo lo antes posible.',
    ]);
} catch (PDOException $e) {
    error_log("Error al insertar mensaje de contacto: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Lo sentimos, hubo un problema técnico al enviar tu mensaje. Por favor, inténtalo de nuevo más tarde.']);
}
