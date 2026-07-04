<?php
$page_title = 'Bandeja de Entrada';
$unread_count = $unread_messages ?? 0;
$filter = $filter ?? 'all';
ob_start();
?>
<div class="admin-card">
    <div class="card-header">
        <h2>Mensajes del Formulario de Contacto</h2>
        <div style="display:flex;gap:8px;">
            <a href="/admin.php?route=messages&filter=all" class="btn <?= $filter==='all'?'btn-primary':'btn-outline' ?>" style="padding:6px 12px;font-size:12px;">Principales</a>
            <a href="/admin.php?route=messages&filter=unread" class="btn <?= $filter==='unread'?'btn-primary':'btn-outline' ?>" style="padding:6px 12px;font-size:12px;">No Leídos</a>
            <a href="/admin.php?route=messages&filter=archived" class="btn <?= $filter==='archived'?'btn-primary':'btn-outline' ?>" style="padding:6px 12px;font-size:12px;">Archivados</a>
        </div>
    </div>
    <?php if (empty($items)): ?>
        <div style="text-align:center;padding:60px;color:var(--text-muted);">
            <i data-lucide="mail-open" style="width:48px;height:48px;stroke-width:1.2;margin-bottom:12px;"></i>
            <p>No hay mensajes en esta sección.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive"><table class="admin-table"><thead><tr>
            <th style="width:180px;">Remitente</th><th>Mensaje / Resumen</th><th style="width:140px;">Fecha</th><th style="width:100px;">Estado</th><th style="width:150px;text-align:center;">Acciones</th>
        </tr></thead><tbody>
        <?php foreach ($items as $msg): ?>
            <tr style="<?= $msg['status']==='unread'?'font-weight:600;background-color:rgba(37,99,235,0.02);':'' ?>">
                <td>
                    <div style="font-size:14px;"><?= htmlspecialchars($msg['name']) ?></div>
                    <div style="font-size:11px;color:var(--text-muted);font-weight:normal;"><?= htmlspecialchars($msg['email']) ?></div>
                </td>
                <td>
                    <a href="/admin.php?route=messages&action=view&id=<?= $msg['id'] ?>" style="color:inherit;text-decoration:none;display:block;max-width:450px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($msg['message']) ?></a>
                </td>
                <td style="font-size:12px;color:var(--text-muted);"><?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?></td>
                <td>
                    <?php if ($msg['status']==='unread'): ?><span class="badge badge-danger">No Leído</span>
                    <?php elseif ($msg['status']==='read'): ?><span class="badge badge-success">Leído</span>
                    <?php else: ?><span class="badge badge-info">Archivado</span><?php endif; ?>
                </td>
                <td>
                    <div class="table-actions" style="justify-content:center;">
                        <a href="/admin.php?route=messages&action=view&id=<?= $msg['id'] ?>" class="btn-icon edit" title="Leer"><i data-lucide="eye" style="width:15px;height:15px;"></i></a>
                        <?php if ($msg['status'] !== 'archived'): ?>
                            <a href="/admin.php?route=messages&action=archive&id=<?= $msg['id'] ?>&filter=<?= $filter ?>" class="btn-icon" title="Archivar" style="color:var(--secondary);"><i data-lucide="archive" style="width:15px;height:15px;"></i></a>
                        <?php endif; ?>
                        <a href="/admin.php?route=messages&action=delete&id=<?= $msg['id'] ?>&filter=<?= $filter ?>" class="btn-icon delete" title="Eliminar" onclick="return confirm('¿Eliminar permanentemente?');"><i data-lucide="trash-2" style="width:15px;height:15px;"></i></a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody></table></div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
