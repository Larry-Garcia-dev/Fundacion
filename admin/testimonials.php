<?php
require_once __DIR__ . '/admin_layout.php';

$message = '';
$error = '';
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Procesar acciones: aprobar, rechazar, eliminar
if ($action === 'approve' && $id > 0) {
    try {
        $stmt = $pdo->prepare("UPDATE `testimonials` SET `status` = 'approved' WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Testimonio aprobado correctamente. Ya es visible en la landing page.';
        $action = 'list';
    } catch (PDOException $e) {
        $error = 'Error al aprobar: ' . $e->getMessage();
        $action = 'list';
    }
}

if ($action === 'reject' && $id > 0) {
    try {
        $stmt = $pdo->prepare("UPDATE `testimonials` SET `status` = 'rejected' WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Testimonio rechazado. No se mostrará en la landing page.';
        $action = 'list';
    } catch (PDOException $e) {
        $error = 'Error al rechazar: ' . $e->getMessage();
        $action = 'list';
    }
}

if ($action === 'delete' && $id > 0) {
    try {
        // Obtener foto para borrar si es local
        $stmt = $pdo->prepare("SELECT photo_url FROM `testimonials` WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && !empty($row['photo_url']) && str_starts_with($row['photo_url'], 'uploads/')) {
            $file_path = __DIR__ . '/../' . $row['photo_url'];
            if (file_exists($file_path)) unlink($file_path);
        }

        $stmt = $pdo->prepare("DELETE FROM `testimonials` WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Testimonio eliminado correctamente.';
        $action = 'list';
    } catch (PDOException $e) {
        $error = 'Error al eliminar: ' . $e->getMessage();
        $action = 'list';
    }
}

// Filtro por estado
$filter = $_GET['filter'] ?? 'all';
$valid_filters = ['all', 'pending', 'approved', 'rejected'];
if (!in_array($filter, $valid_filters)) $filter = 'all';

// Cargar testimonios
$testimonials = [];
$counts = ['all' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];

try {
    $counts['all'] = $pdo->query("SELECT COUNT(*) FROM `testimonials`")->fetchColumn();
    $counts['pending'] = $pdo->query("SELECT COUNT(*) FROM `testimonials` WHERE `status` = 'pending'")->fetchColumn();
    $counts['approved'] = $pdo->query("SELECT COUNT(*) FROM `testimonials` WHERE `status` = 'approved'")->fetchColumn();
    $counts['rejected'] = $pdo->query("SELECT COUNT(*) FROM `testimonials` WHERE `status` = 'rejected'")->fetchColumn();

    $sql = "SELECT * FROM `testimonials`";
    if ($filter !== 'all') {
        $sql .= " WHERE `status` = ?";
        $stmt = $pdo->prepare($sql . " ORDER BY `created_at` DESC");
        $stmt->execute([$filter]);
    } else {
        $stmt = $pdo->query($sql . " ORDER BY `created_at` DESC");
    }
    $testimonials = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Error al cargar testimonios: ' . $e->getMessage();
}

render_admin_header('Gestión de Testimonios');
?>

<?php if ($message): ?>
    <div class="alert alert-success">
        <i data-lucide="check-circle"></i>
        <span><?php echo htmlspecialchars($message); ?></span>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <i data-lucide="alert-circle"></i>
        <span><?php echo htmlspecialchars($error); ?></span>
    </div>
<?php endif; ?>

<!-- Tarjetas de resumen -->
<div class="testimonials-summary">
    <a href="testimonials.php?filter=all" class="summary-card <?php echo $filter === 'all' ? 'active' : ''; ?>">
        <span class="summary-num"><?php echo $counts['all']; ?></span>
        <span class="summary-label">Total</span>
    </a>
    <a href="testimonials.php?filter=pending" class="summary-card pending <?php echo $filter === 'pending' ? 'active' : ''; ?>">
        <span class="summary-num"><?php echo $counts['pending']; ?></span>
        <span class="summary-label">Pendientes</span>
    </a>
    <a href="testimonials.php?filter=approved" class="summary-card approved <?php echo $filter === 'approved' ? 'active' : ''; ?>">
        <span class="summary-num"><?php echo $counts['approved']; ?></span>
        <span class="summary-label">Aprobados</span>
    </a>
    <a href="testimonials.php?filter=rejected" class="summary-card rejected <?php echo $filter === 'rejected' ? 'active' : ''; ?>">
        <span class="summary-num"><?php echo $counts['rejected']; ?></span>
        <span class="summary-label">Rechazados</span>
    </a>
</div>

<div class="admin-card">
    <div class="card-header">
        <h2>
            <?php
            $filter_labels = ['all' => 'Todos los Testimonios', 'pending' => 'Pendientes de Revisión', 'approved' => 'Aprobados', 'rejected' => 'Rechazados'];
            echo $filter_labels[$filter];
            ?>
        </h2>
    </div>

    <?php if (empty($testimonials)): ?>
        <div style="text-align: center; padding: 40px; color: var(--text-muted);">
            <i data-lucide="message-circle-off" style="width: 40px; height: 40px; stroke-width: 1.5; margin-bottom: 12px; color: var(--primary);"></i>
            <p>No hay testimonios <?php echo $filter !== 'all' ? 'en esta categoría' : 'registrados'; ?>.</p>
        </div>
    <?php else: ?>
        <div class="testimonials-admin-list">
            <?php foreach ($testimonials as $t): ?>
                <div class="testimonial-admin-item status-<?php echo $t['status']; ?>">
                    <div class="testimonial-admin-left">
                        <?php if (!empty($t['photo_url'])): ?>
                            <img src="../<?php echo htmlspecialchars($t['photo_url']); ?>" alt="<?php echo htmlspecialchars($t['name']); ?>" class="testimonial-admin-avatar">
                        <?php else: ?>
                            <div class="testimonial-admin-avatar-default <?php echo $t['gender']; ?>">
                                <i data-lucide="<?php echo $t['gender'] === 'other' ? 'circle-user-round' : 'user-round'; ?>"></i>
                            </div>
                        <?php endif; ?>
                        <div class="testimonial-admin-info">
                            <div class="testimonial-admin-header">
                                <strong><?php echo htmlspecialchars($t['name']); ?></strong>
                                <span class="status-badge status-<?php echo $t['status']; ?>">
                                    <?php
                                    $status_labels = ['pending' => 'Pendiente', 'approved' => 'Aprobado', 'rejected' => 'Rechazado'];
                                    echo $status_labels[$t['status']];
                                    ?>
                                </span>
                            </div>
                            <div class="testimonial-admin-meta">
                                <span><i data-lucide="mail" style="width:13px;height:13px;"></i> <?php echo htmlspecialchars($t['email']); ?></span>
                                <?php if (!empty($t['phone'])): ?>
                                    <span><i data-lucide="phone" style="width:13px;height:13px;"></i> <?php echo htmlspecialchars($t['phone']); ?></span>
                                <?php endif; ?>
                                <span><i data-lucide="calendar" style="width:13px;height:13px;"></i> <?php echo date('d/m/Y H:i', strtotime($t['created_at'])); ?></span>
                            </div>
                            <p class="testimonial-admin-message"><?php echo htmlspecialchars($t['message']); ?></p>
                        </div>
                    </div>
                    <div class="testimonial-admin-actions">
                        <?php if ($t['status'] !== 'approved'): ?>
                            <a href="testimonials.php?action=approve&id=<?php echo $t['id']; ?>&filter=<?php echo $filter; ?>" class="btn-admin-action approve" title="Aprobar" onclick="return confirm('¿Aprobar este testimonio? Se mostrará en la landing page.');">
                                <i data-lucide="check-circle" style="width:16px;height:16px;"></i>
                                <span>Aprobar</span>
                            </a>
                        <?php endif; ?>
                        <?php if ($t['status'] !== 'rejected'): ?>
                            <a href="testimonials.php?action=reject&id=<?php echo $t['id']; ?>&filter=<?php echo $filter; ?>" class="btn-admin-action reject" title="Rechazar" onclick="return confirm('¿Rechazar este testimonio? No se mostrará en la landing page.');">
                                <i data-lucide="x-circle" style="width:16px;height:16px;"></i>
                                <span>Rechazar</span>
                            </a>
                        <?php endif; ?>
                        <a href="testimonials.php?action=delete&id=<?php echo $t['id']; ?>&filter=<?php echo $filter; ?>" class="btn-admin-action delete" title="Eliminar" onclick="return confirm('¿Eliminar este testimonio? Esta acción no se puede deshacer.');">
                            <i data-lucide="trash-2" style="width:16px;height:16px;"></i>
                            <span>Eliminar</span>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .testimonials-summary {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .summary-card {
        background: var(--light);
        border: 2px solid transparent;
        border-radius: var(--radius-md);
        padding: 20px;
        text-align: center;
        text-decoration: none;
        color: var(--text-dark);
        transition: all 0.2s ease;
    }
    .summary-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
    .summary-card.active { border-color: var(--primary); background: rgba(37, 99, 235, 0.05); }
    .summary-num { display: block; font-size: 28px; font-weight: 800; font-family: 'Outfit', sans-serif; color: var(--primary); }
    .summary-card.pending .summary-num { color: var(--warning); }
    .summary-card.approved .summary-num { color: var(--success); }
    .summary-card.rejected .summary-num { color: var(--danger); }
    .summary-label { font-size: 13px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }

    .testimonials-admin-list { display: flex; flex-direction: column; gap: 16px; }

    .testimonial-admin-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        padding: 20px;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        background: var(--light);
        transition: var(--transition-smooth, all 0.2s);
    }
    .testimonial-admin-item:hover { box-shadow: var(--shadow); }
    .testimonial-admin-item.status-pending { border-left: 4px solid var(--warning); }
    .testimonial-admin-item.status-approved { border-left: 4px solid var(--success); }
    .testimonial-admin-item.status-rejected { border-left: 4px solid var(--danger); }

    .testimonial-admin-left { display: flex; gap: 16px; flex: 1; min-width: 0; }

    .testimonial-admin-avatar {
        width: 48px; height: 48px; border-radius: 50%; object-fit: cover; flex-shrink: 0;
    }
    .testimonial-admin-avatar-default {
        width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .testimonial-admin-avatar-default.female { background: rgba(168,85,247,0.12); color: #a855f7; }
    .testimonial-admin-avatar-default.male { background: rgba(37,99,235,0.12); color: #2563eb; }
    .testimonial-admin-avatar-default.other { background: rgba(13,148,136,0.12); color: #0d9488; }
    .testimonial-admin-avatar-default i { width: 24px; height: 24px; }

    .testimonial-admin-info { flex: 1; min-width: 0; }
    .testimonial-admin-header { display: flex; align-items: center; gap: 10px; margin-bottom: 6px; flex-wrap: wrap; }
    .testimonial-admin-header strong { font-size: 15px; color: var(--text-dark); }
    .testimonial-admin-meta { display: flex; gap: 16px; flex-wrap: wrap; font-size: 12px; color: var(--text-muted); margin-bottom: 10px; }
    .testimonial-admin-meta span { display: flex; align-items: center; gap: 4px; }
    .testimonial-admin-message { font-size: 13.5px; color: var(--text-muted); line-height: 1.6; margin: 0; }

    .status-badge {
        font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px;
    }
    .status-badge.status-pending { background: #fef3c7; color: #92400e; }
    .status-badge.status-approved { background: #d1fae5; color: #065f46; }
    .status-badge.status-rejected { background: #fee2e2; color: #991b1b; }

    .testimonial-admin-actions { display: flex; flex-direction: column; gap: 8px; flex-shrink: 0; }

    .btn-admin-action {
        display: flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: var(--radius-sm); font-size: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s; white-space: nowrap;
    }
    .btn-admin-action.approve { background: #d1fae5; color: #065f46; }
    .btn-admin-action.approve:hover { background: #a7f3d0; }
    .btn-admin-action.reject { background: #fef3c7; color: #92400e; }
    .btn-admin-action.reject:hover { background: #fde68a; }
    .btn-admin-action.delete { background: #fee2e2; color: #991b1b; }
    .btn-admin-action.delete:hover { background: #fecaca; }

    @media (max-width: 768px) {
        .testimonials-summary { grid-template-columns: repeat(2, 1fr); }
        .testimonial-admin-item { flex-direction: column; }
        .testimonial-admin-actions { flex-direction: row; }
    }
</style>

<?php
render_admin_footer();
?>
