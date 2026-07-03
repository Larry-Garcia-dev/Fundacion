<?php
require_once __DIR__ . '/admin_layout.php';

$message = '';
$error = '';
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lista de iconos Lucide adecuados para beneficios / por qué elegirnos
$available_icons = [
    'award' => 'Premio / Excelencia (Award)',
    'shield' => 'Protección / Inocuidad (Shield)',
    'heart' => 'Amor / Responsabilidad (Heart)',
    'user-check' => 'Usuario / Talento Humano (User Check)',
    'check-circle' => 'Verificación / Calidad (Check Circle)',
    'star' => 'Estrella / Destacado (Star)',
    'clock' => 'Reloj / Puntualidad (Clock)',
    'thumbs-up' => 'Aprobación / Confianza (Thumbs Up)',
    'trending-up' => 'Crecimiento / Futuro (Trending Up)',
    'users' => 'Grupo / Comunidad (Users)',
    'gem' => 'Gema / Valor Premium (Gem)'
];

// Procesar Formulario de Guardado/Edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $icon = trim($_POST['icon'] ?? 'award');
    $display_order = intval($_POST['display_order'] ?? 0);
    
    if (empty($title) || empty($description)) {
        $error = 'El título y la descripción son campos obligatorios.';
    } else {
        try {
            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO `benefits` (title, description, icon, display_order) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $description, $icon, $display_order]);
                $message = 'Punto de elección creado correctamente.';
                $action = 'list';
            } elseif ($action === 'edit' && $id > 0) {
                $stmt = $pdo->prepare("UPDATE `benefits` SET title = ?, description = ?, icon = ?, display_order = ? WHERE id = ?");
                $stmt->execute([$title, $description, $icon, $display_order, $id]);
                $message = 'Punto de elección actualizado correctamente.';
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
        $stmt = $pdo->prepare("DELETE FROM `benefits` WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Punto de elección eliminado correctamente.';
        $action = 'list';
    } catch (PDOException $e) {
        $error = 'Error al eliminar el punto de elección: ' . $e->getMessage();
        $action = 'list';
    }
}

// Cargar Datos para Edición
$edit_data = [];
if ($action === 'edit' && $id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM `benefits` WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $edit_data = $stmt->fetch();
        if (!$edit_data) {
            $error = 'Elemento no encontrado.';
            $action = 'list';
        }
    } catch (PDOException $e) {
        $error = 'Error al cargar datos: ' . $e->getMessage();
        $action = 'list';
    }
}

// Cargar Todos los Elementos para Listado
$benefits = [];
if ($action === 'list') {
    try {
        $stmt = $pdo->query("SELECT * FROM `benefits` ORDER BY `display_order` ASC, `id` ASC");
        $benefits = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = 'Error al listar beneficios: ' . $e->getMessage();
    }
}

render_admin_header('Gestión de "¿Por qué elegirnos?"');
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
            <h2><?php echo $action === 'add' ? 'Agregar Nuevo Punto de Elección' : 'Editar Punto de Elección'; ?></h2>
            <a href="benefits.php" class="btn btn-outline">Volver al Listado</a>
        </div>
        
        <form action="benefits.php?action=<?php echo $action; ?><?php echo $action === 'edit' ? '&id=' . $id : ''; ?>" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label for="title">Título / Beneficio</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($edit_data['title'] ?? ''); ?>" required placeholder="Ej. Calidad Garantizada">
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
                    <label for="description">Descripción Corta</label>
                    <textarea id="description" name="description" rows="3" required placeholder="Escribe un breve texto explicativo (máximo 2 líneas recomendadas)..."><?php echo htmlspecialchars($edit_data['description'] ?? ''); ?></textarea>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="benefits.php" class="btn btn-outline">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save"></i> Guardar Elemento
                </button>
            </div>
        </form>
    </div>

<!-- VISTA: LISTAR -->
<?php else: ?>
    <div class="admin-card">
        <div class="card-header">
            <h2>Puntos de Diferenciación (¿Por qué elegirnos?)</h2>
            <a href="benefits.php?action=add" class="btn btn-primary">
                <i data-lucide="plus-circle"></i> Agregar Punto
            </a>
        </div>
        
        <?php if (empty($benefits)): ?>
            <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                <i data-lucide="info" style="width: 40px; height: 40px; stroke-width: 1.5; margin-bottom: 12px; color: var(--primary);"></i>
                <p>No se encontraron puntos registrados. Por favor, crea uno nuevo.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Icono</th>
                            <th>Beneficio</th>
                            <th>Descripción</th>
                            <th style="width: 100px;">Orden</th>
                            <th style="width: 120px; text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($benefits as $ben): ?>
                            <tr>
                                <td>
                                    <div class="stat-icon secondary" style="width: 40px; height: 40px; margin: 0 auto;">
                                        <i data-lucide="<?php echo htmlspecialchars($ben['icon']); ?>" style="width: 18px; height: 18px;"></i>
                                    </div>
                                </td>
                                <td style="font-weight: 600; font-size: 15px; color: var(--text-dark);">
                                    <?php echo htmlspecialchars($ben['title']); ?>
                                </td>
                                <td style="color: var(--text-muted); font-size: 13px; max-width: 400px; line-height: 1.4;">
                                    <?php echo htmlspecialchars($ben['description']); ?>
                                </td>
                                <td style="font-weight: 500;"><?php echo $ben['display_order']; ?></td>
                                <td>
                                    <div class="table-actions" style="justify-content: center;">
                                        <a href="benefits.php?action=edit&id=<?php echo $ben['id']; ?>" class="btn-icon edit" title="Editar">
                                            <i data-lucide="edit-3" style="width: 15px; height: 15px;"></i>
                                        </a>
                                        <a href="benefits.php?action=delete&id=<?php echo $ben['id']; ?>" class="btn-icon delete" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar este beneficio?');">
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
