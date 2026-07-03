<?php
require_once __DIR__ . '/admin_layout.php';

// Obtener estadísticas
$stats = [
    'services' => 0,
    'benefits' => 0,
    'values' => 0,
    'unread_messages' => 0,
    'total_messages' => 0
];

try {
    $stats['services'] = $pdo->query("SELECT COUNT(*) FROM `services`")->fetchColumn();
    $stats['benefits'] = $pdo->query("SELECT COUNT(*) FROM `benefits`")->fetchColumn();
    $stats['values'] = $pdo->query("SELECT COUNT(*) FROM `values`")->fetchColumn();
    $stats['unread_messages'] = $pdo->query("SELECT COUNT(*) FROM `contact_messages` WHERE `status` = 'unread'")->fetchColumn();
    $stats['total_messages'] = $pdo->query("SELECT COUNT(*) FROM `contact_messages`")->fetchColumn();
    
    // Obtener los 5 mensajes más recientes
    $recent_messages_stmt = $pdo->query("SELECT * FROM `contact_messages` ORDER BY `created_at` DESC LIMIT 5");
    $recent_messages = $recent_messages_stmt->fetchAll();
} catch (PDOException $e) {
    $error_msg = "Error al obtener estadísticas: " . $e->getMessage();
}

render_admin_header('Resumen del Sistema');
?>

<?php if (isset($error_msg)): ?>
    <div class="alert alert-danger">
        <i data-lucide="alert-circle"></i>
        <span><?php echo htmlspecialchars($error_msg); ?></span>
    </div>
<?php endif; ?>

<!-- Fichas Estadísticas -->
<div class="card-grid">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Mensajes Nuevos</h3>
            <div class="stat-value"><?php echo $stats['unread_messages']; ?></div>
        </div>
        <div class="stat-icon success">
            <i data-lucide="mail"></i>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-info">
            <h3>Servicios Ofrecidos</h3>
            <div class="stat-value"><?php echo $stats['services']; ?></div>
        </div>
        <div class="stat-icon primary">
            <i data-lucide="utensils"></i>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-info">
            <h3>Puntos de Elección</h3>
            <div class="stat-value"><?php echo $stats['benefits']; ?></div>
        </div>
        <div class="stat-icon secondary">
            <i data-lucide="award"></i>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-info">
            <h3>Valores</h3>
            <div class="stat-value"><?php echo $stats['values']; ?></div>
        </div>
        <div class="stat-icon primary">
            <i data-lucide="heart"></i>
        </div>
    </div>
</div>

<!-- Bloque de bienvenida e instrucciones -->
<div class="admin-card" style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); color: white;">
    <h2 style="color: white; margin-bottom: 10px; font-family: 'Outfit', sans-serif;">¡Hola, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h2>
    <p style="opacity: 0.9; font-size: 15px; max-width: 800px; line-height: 1.6;">
        Bienvenido al panel de control de la landing page de la **Fundación Visión de Futuro**. 
        Desde aquí puedes administrar dinámicamente cada sección del sitio web. Haz clic en las opciones del menú lateral para actualizar textos, cambiar el orden de los servicios o responder a los mensajes que los usuarios envían desde el formulario.
    </p>
</div>

<!-- Tabla de mensajes recientes -->
<div class="admin-card">
    <div class="card-header">
        <h2>Mensajes de Contacto Recientes</h2>
        <a href="messages.php" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px;">
            Ver Todos (<?php echo $stats['total_messages']; ?>)
        </a>
    </div>
    
    <?php if (empty($recent_messages)): ?>
        <div style="text-align: center; padding: 40px; color: var(--text-muted);">
            <i data-lucide="inbox" style="width: 40px; height: 40px; stroke-width: 1.5; margin-bottom: 12px;"></i>
            <p>No se han recibido mensajes de contacto todavía.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Remitente</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Mensaje</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_messages as $msg): ?>
                        <tr>
                            <td style="font-weight: 600;"><?php echo htmlspecialchars($msg['name']); ?></td>
                            <td><?php echo htmlspecialchars($msg['email']); ?></td>
                            <td><?php echo htmlspecialchars($msg['phone'] ?: 'N/D'); ?></td>
                            <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?php echo htmlspecialchars($msg['message']); ?>
                            </td>
                            <td style="font-size: 12px; color: var(--text-muted);">
                                <?php echo date('d/m/Y H:i', strtotime($msg['created_at'])); ?>
                            </td>
                            <td>
                                <?php if ($msg['status'] === 'unread'): ?>
                                    <span class="badge badge-danger">No Leído</span>
                                <?php elseif ($msg['status'] === 'read'): ?>
                                    <span class="badge badge-success">Leído</span>
                                <?php else: ?>
                                    <span class="badge badge-info">Archivado</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="messages.php?id=<?php echo $msg['id']; ?>" class="btn-icon edit" title="Ver Detalle / Responder">
                                        <i data-lucide="eye" style="width: 16px; height: 16px;"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php
render_admin_footer();
?>
