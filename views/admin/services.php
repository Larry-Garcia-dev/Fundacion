<?php
$page_title = 'Servicios';
$unread_count = $unread_messages ?? 0;
ob_start();
?>
<div class="admin-card">
    <div class="card-header">
        <h2>Servicios Ofrecidos</h2>
        <a href="/admin.php?route=services&action=edit" class="btn btn-primary"><i data-lucide="plus-circle"></i> Agregar Servicio</a>
    </div>
    <?php if (empty($items)): ?>
        <div style="text-align:center;padding:45px;color:var(--text-muted);">
            <i data-lucide="utensils" style="width:45px;height:45px;stroke-width:1.5;margin-bottom:12px;color:var(--primary);"></i>
            <p>No hay servicios registrados.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive"><table class="admin-table"><thead><tr>
            <th style="width:60px;">Icono</th><th>Título</th><th style="width:350px;">Descripción</th><th style="width:80px;">Orden</th><th style="width:120px;text-align:center;">Acciones</th>
        </tr></thead><tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td style="text-align:center;"><i data-lucide="<?= htmlspecialchars($item['icon']) ?>" style="width:24px;height:24px;color:var(--primary);"></i></td>
                <td style="font-weight:600;"><?= htmlspecialchars($item['title']) ?></td>
                <td style="font-size:13px;color:var(--text-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($item['description']) ?></td>
                <td style="font-weight:500;"><?= $item['display_order'] ?></td>
                <td>
                    <div class="table-actions" style="justify-content:center;">
                        <a href="/admin.php?route=services&action=edit&id=<?= $item['id'] ?>" class="btn-icon edit" title="Editar"><i data-lucide="edit-3" style="width:15px;height:15px;"></i></a>
                        <a href="/admin.php?route=services&action=delete&id=<?= $item['id'] ?>" class="btn-icon delete" title="Eliminar" onclick="return confirm('¿Eliminar este servicio?');"><i data-lucide="trash-2" style="width:15px;height:15px;"></i></a>
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
