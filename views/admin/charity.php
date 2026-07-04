<?php
$page_title = 'Obras de Caridad';
$unread_count = $unread_messages ?? 0;
ob_start();
?>
<div class="admin-card">
    <div class="card-header">
        <h2>Obras de Caridad</h2>
        <a href="/admin.php?route=charity&action=edit" class="btn btn-primary"><i data-lucide="plus-circle"></i> Agregar Obra</a>
    </div>
    <?php if (empty($items)): ?>
        <div style="text-align:center;padding:45px;color:var(--text-muted);">
            <i data-lucide="heart-handshake" style="width:45px;height:45px;stroke-width:1.5;margin-bottom:12px;color:var(--primary);"></i>
            <p>No hay obras de caridad registradas.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive"><table class="admin-table"><thead><tr>
            <th style="width:160px;">Imagen</th><th>Título</th><th style="width:300px;">Descripción</th><th style="width:80px;">Orden</th><th style="width:120px;text-align:center;">Acciones</th>
        </tr></thead><tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td>
                    <?php $img = (str_starts_with($item['image_url'], 'uploads/')) ? '../' . $item['image_url'] : $item['image_url']; ?>
                    <img src="<?= htmlspecialchars($img) ?>" alt="" style="width:120px;height:80px;object-fit:cover;border-radius:var(--radius-sm);border:1px solid var(--border);">
                </td>
                <td style="font-weight:600;"><?= htmlspecialchars($item['title']) ?></td>
                <td style="font-size:13px;color:var(--text-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($item['description']) ?></td>
                <td style="font-weight:500;"><?= $item['display_order'] ?></td>
                <td>
                    <div class="table-actions" style="justify-content:center;">
                        <a href="/admin.php?route=charity&action=edit&id=<?= $item['id'] ?>" class="btn-icon edit" title="Editar"><i data-lucide="edit-3" style="width:15px;height:15px;"></i></a>
                        <a href="/admin.php?route=charity&action=delete&id=<?= $item['id'] ?>" class="btn-icon delete" title="Eliminar" onclick="return confirm('¿Eliminar esta obra?');"><i data-lucide="trash-2" style="width:15px;height:15px;"></i></a>
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
