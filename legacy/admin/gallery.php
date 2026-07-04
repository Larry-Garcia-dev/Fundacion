<?php
require_once __DIR__ . '/admin_layout.php';

$message = '';
$error = '';
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Cargar Datos para Edición (Cargamos antes de procesar el POST para conocer la ruta de archivo previa)
$edit_data = [];
if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM `gallery` WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $edit_data = $stmt->fetch();
    } catch (PDOException $e) {
        $error = 'Error al cargar datos: ' . $e->getMessage();
    }
}

// Procesar Guardado / Edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image_url = trim($_POST['image_url'] ?? '');
    $caption = trim($_POST['caption'] ?? '');
    $display_order = intval($_POST['display_order'] ?? 0);
    
    // Procesar subida de archivo si existe
    $existing_file = $edit_data['image_url'] ?? '';
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        try {
            $image_url = upload_admin_image('image_file', $existing_file);
        } catch (Exception $ex) {
            $error = $ex->getMessage();
        }
    }
    
    if (empty($image_url) && empty($error)) {
        $error = 'Debes subir una imagen o ingresar una dirección URL.';
    }
    
    if (empty($error)) {
        try {
            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO `gallery` (image_url, caption, display_order) VALUES (?, ?, ?)");
                $stmt->execute([$image_url, $caption, $display_order]);
                $message = 'Imagen agregada a la galería correctamente.';
                $action = 'list';
            } elseif ($action === 'edit' && $id > 0) {
                $stmt = $pdo->prepare("UPDATE `gallery` SET image_url = ?, caption = ?, display_order = ? WHERE id = ?");
                $stmt->execute([$image_url, $caption, $display_order, $id]);
                $message = 'Imagen de la galería actualizada correctamente.';
                $action = 'list';
            }
        } catch (PDOException $e) {
            $error = 'Error en la base de datos: ' . $e->getMessage();
        }
    }
}

// Procesar Eliminación
if ($action === 'delete' && $id > 0) {
    try {
        // Obtener ruta del archivo para borrarlo si es local
        $stmt_del = $pdo->prepare("SELECT image_url FROM `gallery` WHERE id = ? LIMIT 1");
        $stmt_del->execute([$id]);
        $img_del_path = $stmt_del->fetchColumn();
        
        if ($img_del_path && str_starts_with($img_del_path, 'uploads/') && file_exists(__DIR__ . '/../' . $img_del_path)) {
            unlink(__DIR__ . '/../' . $img_del_path);
        }

        $stmt = $pdo->prepare("DELETE FROM `gallery` WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Imagen eliminada de la galería correctamente.';
        $action = 'list';
    } catch (PDOException $e) {
        $error = 'Error al eliminar la imagen: ' . $e->getMessage();
        $action = 'list';
    }
}

// Recargar todos los elementos si estamos en la vista de lista
$gallery_items = [];
if ($action === 'list') {
    try {
        $stmt = $pdo->query("SELECT * FROM `gallery` ORDER BY `display_order` ASC, `id` DESC");
        $gallery_items = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = 'Error al cargar galería: ' . $e->getMessage();
    }
}

render_admin_header('Galería de Fotos');
?>

<?php if ($message): ?>
    <div class="alert alert-success">
        <i data-lucide="check-circle"></i>
        <span><?php echo htmlspecialchars($message); ?></span>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <i data-lucide="alert-circle"></i>
        <span><?php echo htmlspecialchars($error); ?></span>
    </div>
<?php endif; ?>

<!-- VISTA: AGREGAR O EDITAR -->
<?php if ($action === 'add' || $action === 'edit'): ?>
    <div class="admin-card">
        <div class="card-header">
            <h2><?php echo $action === 'add' ? 'Agregar Imagen a la Galería' : 'Editar Imagen'; ?></h2>
            <a href="gallery.php" class="btn btn-outline">Volver a la Galería</a>
        </div>
        
        <form action="gallery.php?action=<?php echo $action; ?><?php echo $action === 'edit' ? '&id=' . $id : ''; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="image_url">URL de la Imagen</label>
                    <input type="text" id="image_url" name="image_url" value="<?php echo htmlspecialchars($edit_data['image_url'] ?? ''); ?>" placeholder="https://images.unsplash.com/photo-1540479859555-17af45c78602...">
                    <span class="form-help">Ingresa una dirección URL externa, o sube un archivo local abajo.</span>
                    <div style="margin-top: 10px; display: flex; align-items: center; gap: 15px;">
                        <label for="image_file" class="btn btn-outline" style="padding: 8px 14px; font-size: 12px; cursor: pointer; display: inline-flex; gap: 6px; margin-bottom: 0;">
                            <i data-lucide="upload" style="width: 14px; height: 14px;"></i> Subir Imagen desde el PC
                        </label>
                        <input type="file" id="image_file" name="image_file" accept="image/*" style="display: none;" onchange="updateFileName(this, 'gallery_file_name')">
                        <span id="gallery_file_name" style="font-size: 12.5px; color: var(--text-muted); font-weight: 500;">
                            <?php echo (isset($edit_data['image_url']) && str_starts_with($edit_data['image_url'], 'uploads/')) ? 'Archivo activo: ' . basename($edit_data['image_url']) : 'Ningún archivo subido'; ?>
                        </span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="caption">Descripción corta (Pie de foto)</label>
                    <input type="text" id="caption" name="caption" value="<?php echo htmlspecialchars($edit_data['caption'] ?? ''); ?>" placeholder="Ej. Niños felices recibiendo complementos alimenticios.">
                </div>
                
                <div class="form-group">
                    <label for="display_order">Orden de Visualización</label>
                    <input type="number" id="display_order" name="display_order" value="<?php echo htmlspecialchars($edit_data['display_order'] ?? '0'); ?>" required min="0">
                    <span class="form-help">Los números más bajos aparecerán primero en la presentación deslizante.</span>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="gallery.php" class="btn btn-outline">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save"></i> Guardar Imagen
                </button>
            </div>
        </form>
    </div>

<!-- VISTA: LISTAR -->
<?php else: ?>
    <div class="admin-card">
        <div class="card-header">
            <h2>Imágenes de la Galería</h2>
            <a href="gallery.php?action=add" class="btn btn-primary">
                <i data-lucide="plus-circle"></i> Agregar Foto
            </a>
        </div>
        
        <?php if (empty($gallery_items)): ?>
            <div style="text-align: center; padding: 45px; color: var(--text-muted);">
                <i data-lucide="image" style="width: 45px; height: 45px; stroke-width: 1.5; margin-bottom: 12px; color: var(--primary);"></i>
                <p>No hay fotos cargadas en la galería de fotos. Agrega la primera.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 160px;">Vista Previa</th>
                            <th>Descripción / Pie de Foto</th>
                            <th style="width: 100px;">Orden</th>
                            <th style="width: 150px; text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gallery_items as $item): ?>
                            <tr>
                                <td>
                                    <?php 
                                    // Asegurar ruta correcta
                                    $img_src = (str_starts_with($item['image_url'], 'uploads/')) ? '../' . $item['image_url'] : $item['image_url'];
                                    ?>
                                    <img src="<?php echo htmlspecialchars($img_src); ?>" alt="Previsualización" style="width: 120px; height: 80px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                                </td>
                                <td>
                                    <div style="font-weight: 600; font-size: 14px; color: var(--text-dark);"><?php echo htmlspecialchars($item['caption'] ?: '(Sin Descripción)'); ?></div>
                                    <div style="font-size: 11px; color: var(--text-muted); word-break: break-all; margin-top: 3px;"><?php echo htmlspecialchars($item['image_url']); ?></div>
                                </td>
                                <td style="font-weight: 500;"><?php echo $item['display_order']; ?></td>
                                <td>
                                    <div class="table-actions" style="justify-content: center;">
                                        <a href="gallery.php?action=edit&id=<?php echo $item['id']; ?>" class="btn-icon edit" title="Editar">
                                            <i data-lucide="edit-3" style="width: 15px; height: 15px;"></i>
                                        </a>
                                        <a href="gallery.php?action=delete&id=<?php echo $item['id']; ?>" class="btn-icon delete" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar esta imagen de la galería?');">
                                            <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

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
render_admin_footer();
?>
