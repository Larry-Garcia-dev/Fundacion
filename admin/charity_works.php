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
        $stmt = $pdo->prepare("SELECT * FROM `charity_works` WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $edit_data = $stmt->fetch();
    } catch (PDOException $e) {
        $error = 'Error al cargar datos: ' . $e->getMessage();
    }
}

// Procesar Guardado / Edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');
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
    
    if (empty($title) || empty($description)) {
        $error = 'El título y la descripción son obligatorios.';
    } elseif (empty($image_url) && empty($error)) {
        $error = 'Debes subir una imagen representativa o ingresar una dirección URL.';
    }
    
    if (empty($error)) {
        try {
            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO `charity_works` (title, description, image_url, display_order) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $description, $image_url, $display_order]);
                $message = 'Obra de caridad agregada correctamente.';
                $action = 'list';
            } elseif ($action === 'edit' && $id > 0) {
                $stmt = $pdo->prepare("UPDATE `charity_works` SET title = ?, description = ?, image_url = ?, display_order = ? WHERE id = ?");
                $stmt->execute([$title, $description, $image_url, $display_order, $id]);
                $message = 'Obra de caridad actualizada correctamente.';
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
        $stmt_del = $pdo->prepare("SELECT image_url FROM `charity_works` WHERE id = ? LIMIT 1");
        $stmt_del->execute([$id]);
        $img_del_path = $stmt_del->fetchColumn();
        
        if ($img_del_path && str_starts_with($img_del_path, 'uploads/') && file_exists(__DIR__ . '/../' . $img_del_path)) {
            unlink(__DIR__ . '/../' . $img_del_path);
        }

        $stmt = $pdo->prepare("DELETE FROM `charity_works` WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Obra de caridad eliminada correctamente.';
        $action = 'list';
    } catch (PDOException $e) {
        $error = 'Error al eliminar la obra: ' . $e->getMessage();
        $action = 'list';
    }
}

// Recargar todos los elementos si estamos en la vista de lista
$charity_works = [];
if ($action === 'list') {
    try {
        $stmt = $pdo->query("SELECT * FROM `charity_works` ORDER BY `display_order` ASC, `id` DESC");
        $charity_works = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = 'Error al cargar obras de caridad: ' . $e->getMessage();
    }
}

render_admin_header('Gestión de Obras de Caridad (Blog/Proyectos)');
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
            <h2><?php echo $action === 'add' ? 'Agregar Nueva Obra de Caridad' : 'Editar Obra de Caridad'; ?></h2>
            <a href="charity_works.php" class="btn btn-outline">Volver al Listado</a>
        </div>
        
        <form action="charity_works.php?action=<?php echo $action; ?><?php echo $action === 'edit' ? '&id=' . $id : ''; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="title">Título de la Obra / Proyecto</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($edit_data['title'] ?? ''); ?>" required placeholder="Ej. Entrega de desayunos escolares">
                </div>
                
                <div class="form-group">
                    <label for="display_order">Orden de Visualización</label>
                    <input type="number" id="display_order" name="display_order" value="<?php echo htmlspecialchars($edit_data['display_order'] ?? '0'); ?>" required min="0">
                    <span class="form-help">Los números más bajos aparecerán primero en la sección de la web.</span>
                </div>
                
                <div class="form-group full-width">
                    <label for="image_url">URL de la Imagen Representativa</label>
                    <input type="text" id="image_url" name="image_url" value="<?php echo htmlspecialchars($edit_data['image_url'] ?? ''); ?>" placeholder="https://images.unsplash.com/photo-...">
                    <span class="form-help">Ingresa una dirección URL externa, o sube un archivo local abajo.</span>
                    <div style="margin-top: 10px; display: flex; align-items: center; gap: 15px;">
                        <label for="image_file" class="btn btn-outline" style="padding: 8px 14px; font-size: 12px; cursor: pointer; display: inline-flex; gap: 6px; margin-bottom: 0;">
                            <i data-lucide="upload" style="width: 14px; height: 14px;"></i> Subir Imagen desde el PC
                        </label>
                        <input type="file" id="image_file" name="image_file" accept="image/*" style="display: none;" onchange="updateFileName(this, 'work_file_name')">
                        <span id="work_file_name" style="font-size: 12.5px; color: var(--text-muted); font-weight: 500;">
                            <?php echo (isset($edit_data['image_url']) && str_starts_with($edit_data['image_url'], 'uploads/')) ? 'Archivo activo: ' . basename($edit_data['image_url']) : 'Ningún archivo subido'; ?>
                        </span>
                    </div>
                </div>
                
                <div class="form-group full-width">
                    <label for="description">Descripción / Relato de la Obra Social</label>
                    <textarea id="description" name="description" rows="5" required placeholder="Describe los logros, a quiénes benefició y cómo se llevó a cabo esta obra de caridad de la fundación..."><?php echo htmlspecialchars($edit_data['description'] ?? ''); ?></textarea>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="charity_works.php" class="btn btn-outline">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save"></i> Guardar Proyecto
                </button>
            </div>
        </form>
    </div>

<!-- VISTA: LISTAR -->
<?php else: ?>
    <div class="admin-card">
        <div class="card-header">
            <h2>Obras de Caridad y Proyectos</h2>
            <a href="charity_works.php?action=add" class="btn btn-primary">
                <i data-lucide="plus-circle"></i> Agregar Obra
            </a>
        </div>
        
        <?php if (empty($charity_works)): ?>
            <div style="text-align: center; padding: 45px; color: var(--text-muted);">
                <i data-lucide="heart-handshake" style="width: 45px; height: 45px; stroke-width: 1.5; margin-bottom: 12px; color: var(--primary);"></i>
                <p>No hay obras de caridad cargadas. Comienza agregando la primera.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 140px;">Imagen</th>
                            <th>Título de la Obra</th>
                            <th>Resumen / Descripción</th>
                            <th style="width: 80px;">Orden</th>
                            <th style="width: 150px; text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($charity_works as $work): ?>
                            <tr>
                                <td>
                                    <?php 
                                    $img_src = (str_starts_with($work['image_url'], 'uploads/')) ? '../' . $work['image_url'] : $work['image_url'];
                                    ?>
                                    <img src="<?php echo htmlspecialchars($img_src); ?>" alt="Previsualización" style="width: 110px; height: 75px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                                </td>
                                <td style="font-weight: 600; font-size: 15px; color: var(--text-dark);">
                                    <?php echo htmlspecialchars($work['title']); ?>
                                </td>
                                <td style="color: var(--text-muted); font-size: 13.5px; max-width: 380px; line-height: 1.4;">
                                    <?php echo htmlspecialchars(substr($work['description'], 0, 150)) . (strlen($work['description']) > 150 ? '...' : ''); ?>
                                </td>
                                <td style="font-weight: 500;"><?php echo $work['display_order']; ?></td>
                                <td>
                                    <div class="table-actions" style="justify-content: center;">
                                        <a href="charity_works.php?action=edit&id=<?php echo $work['id']; ?>" class="btn-icon edit" title="Editar">
                                            <i data-lucide="edit-3" style="width: 15px; height: 15px;"></i>
                                        </a>
                                        <a href="charity_works.php?action=delete&id=<?php echo $work['id']; ?>" class="btn-icon delete" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar este registro?');">
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
