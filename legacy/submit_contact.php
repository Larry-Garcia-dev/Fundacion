<?php
header('Content-Type: application/json; charset=utf-8');

// Solo permitir solicitudes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

// Cargar la conexión a la base de datos
// (Si no está configurada, db.php detendrá la ejecución o mostrará error, pero por AJAX devolvemos JSON ordenado)
if (!file_exists(__DIR__ . '/config.php')) {
    echo json_encode(['success' => false, 'message' => 'El sistema no está completamente configurado. Comuníquese con el administrador.']);
    exit;
}

require_once __DIR__ . '/db.php';

// Validar que la base de datos esté lista
if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión con la base de datos.']);
    exit;
}

// Obtener e inicializar datos del POST
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');

$errors = [];

// Validaciones
if (empty($name)) {
    $errors[] = 'El nombre es obligatorio.';
}

if (empty($email)) {
    $errors[] = 'El correo electrónico es obligatorio.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'El correo electrónico ingresado no es válido.';
}

if (empty($message)) {
    $errors[] = 'El mensaje no puede estar vacío.';
}

// Si hay errores de validación, responder inmediatamente
if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor, corrija los siguientes errores:',
        'errors' => $errors
    ]);
    exit;
}

try {
    // Insertar en la base de datos con PDO (seguro contra inyecciones SQL)
    $stmt = $pdo->prepare("INSERT INTO `contact_messages` (name, email, phone, message, status) VALUES (?, ?, ?, ?, 'unread')");
    $stmt->execute([$name, $email, $phone, $message]);
    
    echo json_encode([
        'success' => true,
        'message' => '¡Muchas gracias! Tu mensaje ha sido enviado con éxito. Nos pondremos en contacto contigo lo antes posible.'
    ]);
} catch (PDOException $e) {
    // Registrar error internamente y mandar mensaje amigable al usuario
    error_log("Error al insertar mensaje de contacto: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Lo sentimos, hubo un problema técnico al enviar tu mensaje. Por favor, inténtalo de nuevo más tarde.'
    ]);
}
?>
