<?php
require_once __DIR__ . '/admin_layout.php';

$message_info = '';
$error = '';
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Procesar Cambios de Estado (Marcar Leído, Archivar, Eliminar)
try {
    if ($action === 'archive' && $id > 0) {
        $stmt = $pdo->prepare("UPDATE `contact_messages` SET `status` = 'archived' WHERE `id` = ?");
        $stmt->execute([$id]);
        $message_info = 'Mensaje archivado correctamente.';
        $action = 'list';
    } elseif ($action === 'delete' && $id > 0) {
        $stmt = $pdo->prepare("DELETE FROM `contact_messages` WHERE `id` = ?");
        $stmt->execute([$id]);
        $message_info = 'Mensaje eliminado permanentemente.';
        $action = 'list';
    } elseif ($action === 'mark_unread' && $id > 0) {
        $stmt = $pdo->prepare("UPDATE `contact_messages` SET `status` = 'unread' WHERE `id` = ?");
        $stmt->execute([$id]);
        $message_info = 'Mensaje marcado como no leído.';
        $action = 'list';
    }
} catch (PDOException $e) {
    $error = 'Error al actualizar el mensaje: ' . $e->getMessage();
}

// Cargar Detalles de un Mensaje Específico
$detail_msg = null;
if (($action === 'view' || isset($_GET['id'])) && $id > 0 && $action !== 'archive' && $action !== 'delete') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM `contact_messages` WHERE `id` = ? LIMIT 1");
        $stmt->execute([$id]);
        $detail_msg = $stmt->fetch();
        
        if ($detail_msg) {
            $action = 'view';
            // Si estaba No Leído, cambiarlo automáticamente a Leído
            if ($detail_msg['status'] === 'unread') {
                $update_stmt = $pdo->prepare("UPDATE `contact_messages` SET `status` = 'read' WHERE `id` = ?");
                $update_stmt->execute([$id]);
                // Actualizar estado en el objeto actual para renderizarlo correctamente
                $detail_msg['status'] = 'read';
            }
        } else {
            $error = 'Mensaje no encontrado.';
            $action = 'list';
        }
    } catch (PDOException $e) {
        $error = 'Error al cargar el mensaje: ' . $e->getMessage();
        $action = 'list';
    }
}

// Cargar Todos los Mensajes para Listado
$messages_list = [];
$filter = $_GET['filter'] ?? 'all'; // all, unread, archived
if ($action === 'list') {
    try {
        $sql = "SELECT * FROM `contact_messages`";
        if ($filter === 'unread') {
            $sql .= " WHERE `status` = 'unread'";
        } elseif ($filter === 'archived') {
            $sql .= " WHERE `status` = 'archived'";
        } else {
            $sql .= " WHERE `status` != 'archived'"; // Por defecto no mostrar los archivados en la principal
        }
        $sql .= " ORDER BY `created_at` DESC";
        
        $stmt = $pdo->query($sql);
        $messages_list = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = 'Error al cargar mensajes: ' . $e->getMessage();
    }
}

render_admin_header('Bandeja de Entrada de Mensajes');
?>

<?php if ($message_info): ?>
    <div class="alert alert-success">
        <i data-lucide="check-circle"></i>
        <span><?php echo htmlspecialchars($message_info); ?></span>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <i data-lucide="alert-circle"></i>
        <span><?php echo htmlspecialchars($error); ?></span>
    </div>
<?php endif; ?>

<!-- VISTA: VER MENSAJE EN DETALLE -->
<?php if ($action === 'view' && $detail_msg): ?>
    <div class="admin-card">
        <div class="card-header">
            <div style="display: flex; align-items: center; gap: 12px;">
                <a href="messages.php" class="btn btn-icon" title="Volver a la bandeja">
                    <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                </a>
                <h2>Mensaje de: <?php echo htmlspecialchars($detail_msg['name']); ?></h2>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="messages.php?action=mark_unread&id=<?php echo $detail_msg['id']; ?>" class="btn btn-outline" style="padding: 8px 14px; font-size: 13px;">
                    <i data-lucide="mail-open" style="width: 14px; height: 14px;"></i> Marcar No Leído
                </a>
                <a href="messages.php?action=archive&id=<?php echo $detail_msg['id']; ?>" class="btn btn-secondary" style="padding: 8px 14px; font-size: 13px;">
                    <i data-lucide="archive" style="width: 14px; height: 14px;"></i> Archivar
                </a>
                <a href="messages.php?action=delete&id=<?php echo $detail_msg['id']; ?>" class="btn btn-danger" style="padding: 8px 14px; font-size: 13px;" onclick="return confirm('¿Está seguro de eliminar permanentemente este mensaje?');">
                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Eliminar
                </a>
            </div>
        </div>
        
        <div class="message-meta-box" style="background-color: var(--border-light); padding: 20px; border-radius: var(--radius-md); margin-bottom: 25px;">
            <div class="form-grid" style="gap: 15px; margin-bottom: 0;">
                <div>
                    <span style="font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Correo Electrónico</span>
                    <p style="font-weight: 600; color: var(--text-dark); margin-top: 2px;">
                        <a href="mailto:<?php echo htmlspecialchars($detail_msg['email']); ?>" style="color: var(--primary); text-decoration: none;">
                            <?php echo htmlspecialchars($detail_msg['email']); ?>
                        </a>
                    </p>
                </div>
                <div>
                    <span style="font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Teléfono</span>
                    <p style="font-weight: 600; color: var(--text-dark); margin-top: 2px;">
                        <?php echo htmlspecialchars($detail_msg['phone'] ?: 'No proporcionado'); ?>
                    </p>
                </div>
                <div>
                    <span style="font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Fecha de Recepción</span>
                    <p style="font-weight: 600; color: var(--text-dark); margin-top: 2px;">
                        <?php echo date('d/m/Y - h:i A', strtotime($detail_msg['created_at'])); ?>
                    </p>
                </div>
                <div>
                    <span style="font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Estado</span>
                    <p style="margin-top: 2px;">
                        <?php if ($detail_msg['status'] === 'unread'): ?>
                            <span class="badge badge-danger">No Leído</span>
                        <?php elseif ($detail_msg['status'] === 'read'): ?>
                            <span class="badge badge-success">Leído</span>
                        <?php else: ?>
                            <span class="badge badge-info">Archivado</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="message-content-body" style="padding: 10px 10px 30px 10px; border-bottom: 1px solid var(--border-light); margin-bottom: 30px;">
            <h3 style="font-family: 'Outfit', sans-serif; font-size: 14px; color: var(--text-muted); text-transform: uppercase; margin-bottom: 12px; font-weight: 600;">Contenido del Mensaje</h3>
            <p style="font-size: 16px; color: var(--text-dark); line-height: 1.8; white-space: pre-wrap; background: #fafafa; padding: 25px; border-radius: var(--radius-sm); border: 1px solid var(--border-light);"><?php echo htmlspecialchars($detail_msg['message']); ?></p>
        </div>
        
        <div class="message-actions-reply">
            <a href="mailto:<?php echo htmlspecialchars($detail_msg['email']); ?>?subject=Contacto: Fundación Visión de Futuro" class="btn btn-primary">
                <i data-lucide="reply"></i> Responder por Correo Electrónico
            </a>
        </div>
    </div>

<!-- VISTA: LISTA DE MENSAJES -->
<?php else: ?>
    <div class="admin-card">
        <div class="card-header">
            <h2>Mensajes del Formulario de Contacto</h2>
            
            <!-- Filtros de Bandeja -->
            <div style="display: flex; gap: 8px;">
                <a href="messages.php?filter=all" class="btn <?php echo $filter === 'all' ? 'btn-primary' : 'btn-outline'; ?>" style="padding: 6px 12px; font-size: 12px;">
                    Principales
                </a>
                <a href="messages.php?filter=unread" class="btn <?php echo $filter === 'unread' ? 'btn-primary' : 'btn-outline'; ?>" style="padding: 6px 12px; font-size: 12px;">
                    No Leídos
                </a>
                <a href="messages.php?filter=archived" class="btn <?php echo $filter === 'archived' ? 'btn-primary' : 'btn-outline'; ?>" style="padding: 6px 12px; font-size: 12px;">
                    Archivados
                </a>
            </div>
        </div>
        
        <?php if (empty($messages_list)): ?>
            <div style="text-align: center; padding: 60px; color: var(--text-muted);">
                <i data-lucide="mail-open" style="width: 48px; height: 48px; stroke-width: 1.2; margin-bottom: 12px;"></i>
                <p>No hay mensajes en esta sección.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 180px;">Remitente</th>
                            <th>Mensaje / Resumen</th>
                            <th style="width: 140px;">Fecha</th>
                            <th style="width: 100px;">Estado</th>
                            <th style="width: 150px; text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages_list as $msg): ?>
                            <tr style="<?php echo $msg['status'] === 'unread' ? 'font-weight: 600; background-color: rgba(37, 99, 235, 0.02);' : ''; ?>">
                                <td>
                                    <div style="font-size: 14px; color: var(--text-dark);"><?php echo htmlspecialchars($msg['name']); ?></div>
                                    <div style="font-size: 11px; color: var(--text-muted); font-weight: normal;"><?php echo htmlspecialchars($msg['email']); ?></div>
                                </td>
                                <td>
                                    <a href="messages.php?id=<?php echo $msg['id']; ?>" style="color: inherit; text-decoration: none; display: block; max-width: 450px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?php echo htmlspecialchars($msg['message']); ?>
                                    </a>
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
                                    <div class="table-actions" style="justify-content: center;">
                                        <a href="messages.php?id=<?php echo $msg['id']; ?>" class="btn-icon edit" title="Leer Mensaje Completo">
                                            <i data-lucide="eye" style="width: 15px; height: 15px;"></i>
                                        </a>
                                        <?php if ($msg['status'] !== 'archived'): ?>
                                            <a href="messages.php?action=archive&id=<?php echo $msg['id']; ?>&filter=<?php echo $filter; ?>" class="btn-icon" title="Archivar" style="color: var(--secondary);">
                                                <i data-lucide="archive" style="width: 15px; height: 15px;"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="messages.php?action=delete&id=<?php echo $msg['id']; ?>&filter=<?php echo $filter; ?>" class="btn-icon delete" title="Eliminar permanentemente" onclick="return confirm('¿Está seguro de eliminar permanentemente este mensaje?');">
                                            <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
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
<?php endif; ?>

<?php
render_admin_footer();
?>
