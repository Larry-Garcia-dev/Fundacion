<?php
$page_title = 'Detalle del Mensaje';
$unread_count = $unread_messages ?? 0;
ob_start();
?>
<div class="admin-card">
    <div class="card-header">
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="/admin.php?route=messages" class="btn btn-icon" title="Volver a la bandeja">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
            </a>
            <h2>Mensaje de: <?= htmlspecialchars($message['name'] ?? 'Desconocido') ?></h2>
        </div>
        <div style="display: flex; gap: 10px;">
            <?php if (($message['status'] ?? '') === 'read'): ?>
                <a href="/admin.php?route=messages&action=edit&id=<?= $message['id'] ?>&do=mark_unread" class="btn btn-outline" style="padding: 8px 14px; font-size: 13px;">
                    <i data-lucide="mail-open" style="width: 14px; height: 14px;"></i> Marcar No Leído
                </a>
            <?php endif; ?>
            <?php if (($message['status'] ?? '') !== 'archived'): ?>
                <a href="/admin.php?route=messages&action=edit&id=<?= $message['id'] ?>&do=archive" class="btn btn-outline" style="padding: 8px 14px; font-size: 13px;">
                    <i data-lucide="archive" style="width: 14px; height: 14px;"></i> Archivar
                </a>
            <?php endif; ?>
            <a href="/admin.php?route=messages&action=delete&id=<?= $message['id'] ?>" class="btn btn-outline" style="padding: 8px 14px; font-size: 13px; color: #dc2626; border-color: #dc2626;" onclick="return confirm('¿Está seguro de eliminar permanentemente este mensaje?');">
                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i> Eliminar
            </a>
        </div>
    </div>

    <div style="background-color: var(--border-light); padding: 20px; border-radius: var(--radius-md); margin-bottom: 25px;">
        <div class="form-grid" style="gap: 15px; margin-bottom: 0;">
            <div>
                <span style="font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Correo Electrónico</span>
                <p style="font-weight: 600; color: var(--text-dark); margin-top: 2px;">
                    <a href="mailto:<?= htmlspecialchars($message['email'] ?? '') ?>" style="color: var(--primary); text-decoration: none;">
                        <?= htmlspecialchars($message['email'] ?? 'No proporcionado') ?>
                    </a>
                </p>
            </div>
            <div>
                <span style="font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Teléfono</span>
                <p style="font-weight: 600; color: var(--text-dark); margin-top: 2px;">
                    <?= htmlspecialchars($message['phone'] ?? 'No proporcionado') ?>
                </p>
            </div>
            <div>
                <span style="font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Fecha de Recepción</span>
                <p style="font-weight: 600; color: var(--text-dark); margin-top: 2px;">
                    <?= !empty($message['created_at']) ? date('d/m/Y - h:i A', strtotime($message['created_at'])) : 'Fecha desconocida' ?>
                </p>
            </div>
            <div>
                <span style="font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Estado</span>
                <p style="margin-top: 2px;">
                    <?php if (($message['status'] ?? '') === 'unread'): ?>
                        <span class="badge badge-danger">No Leído</span>
                    <?php elseif (($message['status'] ?? '') === 'read'): ?>
                        <span class="badge badge-success">Leído</span>
                    <?php else: ?>
                        <span class="badge badge-info">Archivado</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>

    <div style="padding: 10px 10px 30px 10px; border-bottom: 1px solid var(--border-light); margin-bottom: 30px;">
        <h3 style="font-family: 'Outfit', sans-serif; font-size: 14px; color: var(--text-muted); text-transform: uppercase; margin-bottom: 12px; font-weight: 600;">Contenido del Mensaje</h3>
        <p style="font-size: 16px; color: var(--text-dark); line-height: 1.8; white-space: pre-wrap; background: #fafafa; padding: 25px; border-radius: var(--radius-sm); border: 1px solid var(--border-light);"><?= htmlspecialchars($message['message'] ?? 'Sin contenido') ?></p>
    </div>

    <div>
        <a href="mailto:<?= htmlspecialchars($message['email'] ?? '') ?>?subject=Contacto: Fundación Visión de Futuro" class="btn btn-primary">
            <i data-lucide="reply"></i> Responder por Correo Electrónico
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
