<?php
$page_title = $id > 0 ? 'Editar Obra de Caridad' : 'Agregar Obra de Caridad';
$unread_count = $unread_messages ?? 0;
ob_start();
?>
<div class="admin-card">
    <div class="card-header">
        <h2><?= $id > 0 ? 'Editar Obra de Caridad' : 'Agregar Nueva Obra de Caridad' ?></h2>
        <a href="/admin.php?route=charity" class="btn btn-outline">Cancelar</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <i data-lucide="alert-circle"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <form action="/admin.php?route=charity&action=edit<?= $id > 0 ? '&id=' . $id : '' ?>" method="POST" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-group">
                <label for="title">Título de la Obra / Proyecto</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($item['title'] ?? '') ?>" required placeholder="Ej. Entrega de desayunos escolares">
            </div>

            <div class="form-group">
                <label for="display_order">Orden de Visualización</label>
                <input type="number" id="display_order" name="display_order" value="<?= htmlspecialchars($item['display_order'] ?? '0') ?>" required min="0">
                <span class="form-help">Los números más bajos aparecerán primero en la sección de la web.</span>
            </div>

            <div class="form-group full-width">
                <label for="image_url">URL de la Imagen Representativa</label>
                <input type="text" id="image_url" name="image_url" value="<?= htmlspecialchars($item['image_url'] ?? '') ?>" placeholder="https://images.unsplash.com/photo-...">
                <span class="form-help">Ingresa una dirección URL externa, o sube un archivo local abajo.</span>
                <div style="margin-top: 10px; display: flex; align-items: center; gap: 15px;">
                    <label for="image_file" class="btn btn-outline" style="padding: 8px 14px; font-size: 12px; cursor: pointer; display: inline-flex; gap: 6px; margin-bottom: 0;">
                        <i data-lucide="upload" style="width: 14px; height: 14px;"></i> Subir Imagen desde el PC
                    </label>
                    <input type="file" id="image_file" name="image_file" accept="image/*" style="display: none;" onchange="updateFileName(this, 'charity_file_name')">
                    <span id="charity_file_name" style="font-size: 12.5px; color: var(--text-muted); font-weight: 500;">
                        <?= (!empty($item['image_url']) && str_starts_with($item['image_url'], 'uploads/')) ? 'Archivo activo: ' . basename($item['image_url']) : 'Ningún archivo subido' ?>
                    </span>
                </div>
            </div>

            <?php if (!empty($item['image_url'])): ?>
                <div class="form-group full-width">
                    <label>Vista Previa Actual</label>
                    <?php $img = (str_starts_with($item['image_url'], 'uploads/')) ? '../' . $item['image_url'] : $item['image_url']; ?>
                    <img src="<?= htmlspecialchars($img) ?>" alt="Vista previa" style="max-width: 300px; height: auto; border-radius: var(--radius-md); border: 2px solid var(--border);">
                </div>
            <?php endif; ?>

            <div class="form-group full-width">
                <label for="description">Descripción / Relato de la Obra Social</label>
                <textarea id="description" name="description" rows="5" required placeholder="Describe los logros, a quiénes benefició y cómo se llevó a cabo esta obra de caridad de la fundación..."><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <a href="/admin.php?route=charity" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save"></i> Guardar Proyecto
            </button>
        </div>
    </form>
</div>

<script>
function updateFileName(input, spanId) {
    const span = document.getElementById(spanId);
    if (input.files && input.files.length > 0) {
        span.textContent = "Seleccionado: " + input.files[0].name;
        span.style.color = "var(--secondary)";
    } else {
        span.textContent = "Ningún archivo seleccionado";
        span.style.color = "var(--text-muted)";
    }
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
