<?php
$page_title = $id > 0 ? 'Editar Valor' : 'Agregar Valor';
$unread_count = $unread_messages ?? 0;
ob_start();
?>
<div class="admin-card">
    <div class="card-header">
        <h2><?= $id > 0 ? 'Editar Valor Institucional' : 'Agregar Nuevo Valor' ?></h2>
        <a href="/admin.php?route=values" class="btn btn-outline">Cancelar</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <i data-lucide="alert-circle"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <form action="/admin.php?route=values&action=edit<?= $id > 0 ? '&id=' . $id : '' ?>" method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label for="title">Nombre del Valor</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($item['title'] ?? '') ?>" required placeholder="Ej. Transparencia o Respeto">
            </div>

            <div class="form-group">
                <label for="display_order">Orden de Visualización</label>
                <input type="number" id="display_order" name="display_order" value="<?= htmlspecialchars($item['display_order'] ?? '0') ?>" required min="0">
                <span class="form-help">Determina el orden de aparición en las tarjetas de valores.</span>
            </div>
        </div>

        <div class="form-actions">
            <a href="/admin.php?route=values" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save"></i> Guardar Valor
            </button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
