<?php
$page_title = 'Resumen del Sistema';
$unread_count = $unread_messages ?? 0;
ob_start();
?>
<div class="card-grid">
    <div class="stat-card">
        <div class="stat-info"><h3>Mensajes Nuevos</h3><div class="stat-value"><?= $unread_messages ?? 0 ?></div></div>
        <div class="stat-icon success"><i data-lucide="mail"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info"><h3>Servicios</h3><div class="stat-value"><?= $total_services ?? 0 ?></div></div>
        <div class="stat-icon primary"><i data-lucide="utensils"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info"><h3>Puntos de Elección</h3><div class="stat-value"><?= $total_benefits ?? 0 ?></div></div>
        <div class="stat-icon secondary"><i data-lucide="award"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info"><h3>Valores</h3><div class="stat-value"><?= $total_values ?? 0 ?></div></div>
        <div class="stat-icon primary"><i data-lucide="heart"></i></div>
    </div>
</div>

<div class="admin-card" style="background:linear-gradient(135deg,#1e3a8a 0%,#1e40af 100%);color:#fff;">
    <h2 style="color:#fff;margin-bottom:10px;font-family:'Outfit',sans-serif;">¡Hola, <?= htmlspecialchars($user['username'] ?? 'Admin') ?>!</h2>
    <p style="opacity:.9;font-size:15px;max-width:800px;line-height:1.6;">
        Bienvenido al panel de control de la Fundación Visión de Futuro. Administra cada sección del sitio desde el menú lateral.
    </p>
</div>

<div class="admin-card">
    <div class="card-header">
        <h2>Mensajes de Contacto Recientes</h2>
        <a href="/admin.php?route=messages" class="btn btn-outline" style="padding:6px 12px;font-size:12px;">Ver Todos (<?= $total_messages ?? 0 ?>)</a>
    </div>
    <?php if (empty($recent_messages)): ?>
        <div style="text-align:center;padding:40px;color:var(--text-muted);">
            <i data-lucide="inbox" style="width:40px;height:40px;stroke-width:1.5;margin-bottom:12px;"></i>
            <p>No se han recibido mensajes todavía.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive"><table class="admin-table"><thead><tr>
            <th>Remitente</th><th>Correo</th><th>Teléfono</th><th>Mensaje</th><th>Fecha</th><th>Estado</th><th>Acción</th>
        </tr></thead><tbody>
        <?php foreach ($recent_messages as $msg): ?>
            <tr>
                <td style="font-weight:600;"><?= htmlspecialchars($msg['name']) ?></td>
                <td><?= htmlspecialchars($msg['email']) ?></td>
                <td><?= htmlspecialchars($msg['phone'] ?: 'N/D') ?></td>
                <td style="max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($msg['message']) ?></td>
                <td style="font-size:12px;color:var(--text-muted);"><?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?></td>
                <td>
                    <?php if ($msg['status'] === 'unread'): ?><span class="badge badge-danger">No Leído</span>
                    <?php elseif ($msg['status'] === 'read'): ?><span class="badge badge-success">Leído</span>
                    <?php else: ?><span class="badge badge-info">Archivado</span><?php endif; ?>
                </td>
                <td><div class="table-actions">
                    <a href="/admin.php?route=messages&action=view&id=<?= $msg['id'] ?>" class="btn-icon edit" title="Ver"><i data-lucide="eye" style="width:16px;height:16px;"></i></a>
                </div></td>
            </tr>
        <?php endforeach; ?>
        </tbody></table></div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
