<?php
$page_title = 'Galería de Fotos';
$unread_count = $unread_messages ?? 0;
ob_start();
?>
<div class="admin-card">
    <div class="card-header">
        <h2>Imágenes de la Galería</h2>
        <a href="/admin.php?route=gallery&action=edit" class="btn btn-primary"><i data-lucide="plus-circle"></i> Agregar Foto</a>
    </div>
    <?php if (empty($items)): ?>
        <div style="text-align:center;padding:45px;color:var(--text-muted);">
            <i data-lucide="image" style="width:45px;height:45px;stroke-width:1.5;margin-bottom:12px;color:var(--primary);"></i>
            <p>No hay fotos cargadas. Agrega la primera.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive"><table class="admin-table"><thead><tr>
            <th style="width:160px;">Vista Previa</th><th>Descripción</th><th style="width:100px;">Orden</th><th style="width:150px;text-align:center;">Acciones</th>
        </tr></thead><tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td>
                    <?php $img = (str_starts_with($item['image_url'], 'uploads/')) ? '../' . $item['image_url'] : $item['image_url']; ?>
                    <img src="<?= htmlspecialchars($img) ?>" alt="" style="width:120px;height:80px;object-fit:cover;border-radius:var(--radius-sm);border:1px solid var(--border);">
                </td>
                <td>
                    <div style="font-weight:600;font-size:14px;"><?= htmlspecialchars($item['caption'] ?: '(Sin Descripción)') ?></div>
                    <div style="font-size:11px;color:var(--text-muted);word-break:break-all;margin-top:3px;"><?= htmlspecialchars($item['image_url']) ?></div>
                </td>
                <td style="font-weight:500;"><?= $item['display_order'] ?></td>
                <td>
                    <div class="table-actions" style="justify-content:center;">
                        <a href="/admin.php?route=gallery&action=edit&id=<?= $item['id'] ?>" class="btn-icon edit" title="Editar"><i data-lucide="edit-3" style="width:15px;height:15px;"></i></a>
                        <a href="/admin.php?route=gallery&action=delete&id=<?= $item['id'] ?>" class="btn-icon delete" title="Eliminar" onclick="return confirm('¿Eliminar esta imagen?');"><i data-lucide="trash-2" style="width:15px;height:15px;"></i></a>
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
