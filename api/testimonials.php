<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Uploader.php';

try {
    $db = Database::connect();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión.']);
    exit;
}

// ── GET: paginated approved testimonials ──
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $page     = max(1, intval($_GET['page'] ?? 1));
    $per_page = 6;
    $offset   = ($page - 1) * $per_page;

    try {
        $total = $db->query("SELECT COUNT(*) FROM `testimonials` WHERE `status` = 'approved'")->fetchColumn();

        $stmt = $db->prepare(
            "SELECT name, gender, message, photo_url, created_at
             FROM `testimonials` WHERE `status` = 'approved'
             ORDER BY `created_at` DESC LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode([
            'success' => true,
            'data'    => $stmt->fetchAll(),
            'total'   => (int) $total,
            'page'    => $page,
            'pages'   => (int) ceil($total / $per_page),
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al cargar testimonios.']);
    }
    exit;
}

// ── POST: submit new testimonial ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    // Handle optional photo upload
    $photo_url = '';
    try {
        $photo_url = Uploader::image('photo');
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }

    try {
        $stmt = $db->prepare(
            "INSERT INTO `testimonials` (`name`, `email`, `phone`, `gender`, `message`, `photo_url`, `status`)
             VALUES (:name, :email, :phone, :gender, :message, :photo_url, 'pending')"
        );
        $stmt->execute([
            ':name'      => htmlspecialchars($name),
            ':email'     => htmlspecialchars($email),
            ':phone'     => htmlspecialchars($phone),
            ':gender'    => $gender,
            ':message'   => htmlspecialchars($message),
            ':photo_url' => $photo_url,
        ]);

        echo json_encode([
            'success' => true,
            'message' => '¡Gracias por tu testimonio! Será revisado por el equipo y, una vez aprobado, aparecerá en la página.',
        ]);
    } catch (PDOException $e) {
        error_log("Error testimonio: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error técnico. Inténtalo más tarde.']);
    }
    exit;
}

// ── Anything else ──
http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
