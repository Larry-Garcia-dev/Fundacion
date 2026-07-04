<?php
$page_title = $id > 0 ? 'Editar Beneficio' : 'Agregar Beneficio';
$unread_count = $unread_messages ?? 0;
ob_start();
?>
<div class="admin-card">
    <div class="card-header">
        <h2><?= $id > 0 ? 'Editar Punto de Elección' : 'Agregar Nuevo Punto de Elección' ?></h2>
        <a href="/admin.php?route=benefits" class="btn btn-outline">Cancelar</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <i data-lucide="alert-circle"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <form action="/admin.php?route=benefits&action=edit<?= $id > 0 ? '&id=' . $id : '' ?>" method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label for="title">Título / Beneficio</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($item['title'] ?? '') ?>" required placeholder="Ej. Calidad Garantizada">
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
                <label for="description">Descripción Corta</label>
                <textarea id="description" name="description" rows="3" required placeholder="Escribe un breve texto explicativo (máximo 2 líneas recomendadas)..."><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <a href="/admin.php?route=benefits" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save"></i> Guardar Elemento
            </button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
