<?php
require_once __DIR__ . '/admin_layout.php';

$message = '';
$error = '';
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lista de iconos Lucide populares para servicios
$available_icons = [
    'utensils' => 'Cubiertos / Alimentación (Utensils)',
    'briefcase' => 'Portafolio / Negocios (Briefcase)',
    'users' => 'Usuarios / Comunidad (Users)',
    'handshake' => 'Acompañamiento / Alianza (Handshake)',
    'heart' => 'Salud / Bienestar (Heart)',
    'award' => 'Calidad / Premio (Award)',
    'shield' => 'Seguridad / Inocuidad (Shield)',
    'activity' => 'Actividad / Salud (Activity)',
    'globe' => 'Mundo / Social (Globe)',
    'graduation-cap' => 'Educación / Capacitación (Graduation Cap)',
    'smile' => 'Felicidad / Compromiso (Smile)',
    'settings' => 'Operación / Soporte (Settings)'
];

// Procesar Formulario de Guardado/Edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $icon = trim($_POST['icon'] ?? 'briefcase');
    $display_order = intval($_POST['display_order'] ?? 0);
    
    if (empty($title) || empty($description)) {
        $error = 'El título y la descripción son campos obligatorios.';
    } else {
        try {
            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO `services` (title, description, icon, display_order) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $description, $icon, $display_order]);
                $message = 'Servicio agregado correctamente.';
                $action = 'list';
            } elseif ($action === 'edit' && $id > 0) {
                $stmt = $pdo->prepare("UPDATE `services` SET title = ?, description = ?, icon = ?, display_order = ? WHERE id = ?");
                $stmt->execute([$title, $description, $icon, $display_order, $id]);
                $message = 'Servicio actualizado correctamente.';
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
        $stmt = $pdo->prepare("DELETE FROM `services` WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Servicio eliminado correctamente.';
        $action = 'list';
    } catch (PDOException $e) {
        $error = 'Error al eliminar el servicio: ' . $e->getMessage();
        $action = 'list';
    }
}

// Cargar Datos para Edición
$edit_data = [];
if ($action === 'edit' && $id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM `services` WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $edit_data = $stmt->fetch();
        if (!$edit_data) {
            $error = 'Servicio no encontrado.';
            $action = 'list';
        }
    } catch (PDOException $e) {
        $error = 'Error al cargar datos: ' . $e->getMessage();
        $action = 'list';
    }
}

// Cargar Todos los Servicios para Listado
$services = [];
if ($action === 'list') {
    try {
        $stmt = $pdo->query("SELECT * FROM `services` ORDER BY `display_order` ASC, `id` ASC");
        $services = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = 'Error al listar servicios: ' . $e->getMessage();
    }
}

render_admin_header('Gestión de Servicios');
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
            <h2><?php echo $action === 'add' ? 'Agregar Nuevo Servicio' : 'Editar Servicio'; ?></h2>
            <a href="services.php" class="btn btn-outline">Volver al Listado</a>
        </div>
        
        <form action="services.php?action=<?php echo $action; ?><?php echo $action === 'edit' ? '&id=' . $id : ''; ?>" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label for="title">Título del Servicio</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($edit_data['title'] ?? ''); ?>" required placeholder="Ej. Alimentación Hospitalaria">
                </div>
                
                <div class="form-group">
                    <label for="icon">Icono Visual</label>
                    <select id="icon" name="icon">
                        <?php foreach ($available_icons as $key => $label): ?>
                            <option value="<?php echo $key; ?>" <?php echo (isset($edit_data['icon']) && $edit_data['icon'] === $key) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="display_order">Orden de Visualización</label>
                    <input type="number" id="display_order" name="display_order" value="<?php echo htmlspecialchars($edit_data['display_order'] ?? '0'); ?>" required min="0">
                    <span class="form-help">Valores más bajos aparecerán primero en la landing page.</span>
                </div>
                
                <div class="form-group full-width">
                    <label for="description">Descripción Detallada</label>
                    <textarea id="description" name="description" rows="4" required placeholder="Escribe aquí los detalles del servicio que ofrece la fundación..."><?php echo htmlspecialchars($edit_data['description'] ?? ''); ?></textarea>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="services.php" class="btn btn-outline">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save"></i> Guardar Servicio
                </button>
            </div>
        </form>
    </div>

<!-- VISTA: LISTAR -->
<?php else: ?>
    <div class="admin-card">
        <div class="card-header">
            <h2>Servicios Registrados en la Landing</h2>
            <a href="services.php?action=add" class="btn btn-primary">
                <i data-lucide="plus-circle"></i> Agregar Servicio
            </a>
        </div>
        
        <?php if (empty($services)): ?>
            <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                <i data-lucide="info" style="width: 40px; height: 40px; stroke-width: 1.5; margin-bottom: 12px; color: var(--primary);"></i>
                <p>No se encontraron servicios registrados. Por favor, crea uno nuevo.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Icono</th>
                            <th>Título</th>
                            <th>Descripción</th>
                            <th style="width: 100px;">Orden</th>
                            <th style="width: 120px; text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $srv): ?>
                            <tr>
                                <td>
                                    <div class="stat-icon primary" style="width: 40px; height: 40px; margin: 0 auto;">
                                        <i data-lucide="<?php echo htmlspecialchars($srv['icon']); ?>" style="width: 18px; height: 18px;"></i>
                                    </div>
                                </td>
                                <td style="font-weight: 600; font-size: 15px; color: var(--text-dark);">
                                    <?php echo htmlspecialchars($srv['title']); ?>
                                </td>
                                <td style="color: var(--text-muted); font-size: 13px; max-width: 400px; line-height: 1.4;">
                                    <?php echo htmlspecialchars($srv['description']); ?>
                                </td>
                                <td style="font-weight: 500;"><?php echo $srv['display_order']; ?></td>
                                <td>
                                    <div class="table-actions" style="justify-content: center;">
                                        <a href="services.php?action=edit&id=<?php echo $srv['id']; ?>" class="btn-icon edit" title="Editar">
                                            <i data-lucide="edit-3" style="width: 15px; height: 15px;"></i>
                                        </a>
                                        <a href="services.php?action=delete&id=<?php echo $srv['id']; ?>" class="btn-icon delete" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar este servicio? Esta acción no se puede deshacer.');">
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

<?php
render_admin_footer();
?>
