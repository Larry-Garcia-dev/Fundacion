<?php
$page_title = $id > 0 ? 'Editar Servicio' : 'Agregar Servicio';
$unread_count = $unread_messages ?? 0;
ob_start();
?>
<div class="admin-card">
    <div class="card-header">
        <h2><?= $id > 0 ? 'Editar Servicio' : 'Agregar Nuevo Servicio' ?></h2>
        <a href="/admin.php?route=services" class="btn btn-outline">Cancelar</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <i data-lucide="alert-circle"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <form action="/admin.php?route=services&action=edit<?= $id > 0 ? '&id=' . $id : '' ?>" method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label for="title">Título del Servicio</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($item['title'] ?? '') ?>" required placeholder="Ej. Alimentación Hospitalaria">
            </div>

            <div class="form-group">
                <label for="icon">Icono Visual</label>
                <select id="icon" name="icon">
                    <?php foreach ($available_icons as $key => $label): ?>
                        <option value="<?= $key ?>" <?= (isset($item['icon']) && $item['icon'] === $key) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="display_order">Orden de Visualización</label>
                <input type="number" id="display_order" name="display_order" value="<?= htmlspecialchars($item['display_order'] ?? '0') ?>" required min="0">
                <span class="form-help">Valores más bajos aparecerán primero en la landing page.</span>
            </div>

            <div class="form-group full-width">
                <label for="description">Descripción Detallada</label>
                <textarea id="description" name="description" rows="4" required placeholder="Escribe aquí los detalles del servicio que ofrece la fundación..."><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <a href="/admin.php?route=services" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save"></i> Guardar Servicio
            </button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
