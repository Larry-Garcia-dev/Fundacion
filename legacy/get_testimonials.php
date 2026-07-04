<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php';

if (!$pdo) {
    echo json_encode(['success' => false]);
    exit;
}

$page    = max(1, intval($_GET['page'] ?? 1));
$per_page = 6;
$offset  = ($page - 1) * $per_page;

try {
    // Total de aprobados
    $total = $pdo->query("SELECT COUNT(*) FROM `testimonials` WHERE `status` = 'approved'")->fetchColumn();

    // Cargar lote actual
    $stmt = $pdo->prepare("SELECT name, gender, message, photo_url, created_at FROM `testimonials` WHERE `status` = 'approved' ORDER BY `created_at` DESC LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $per_page, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $testimonials = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'data'    => $testimonials,
        'total'   => (int)$total,
        'page'    => $page,
        'pages'   => (int)ceil($total / $per_page)
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al cargar testimonios.']);
}
