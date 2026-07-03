<?php
require_once __DIR__ . '/admin_layout.php';

$message = '';
$error = '';
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Procesar Formulario de Guardado/Edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $display_order = intval($_POST['display_order'] ?? 0);
    
    if (empty($title)) {
        $error = 'El nombre del valor es obligatorio.';
    } else {
        try {
            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO `values` (title, display_order) VALUES (?, ?)");
                $stmt->execute([$title, $display_order]);
                $message = 'Valor agregado correctamente.';
                $action = 'list';
            } elseif ($action === 'edit' && $id > 0) {
                $stmt = $pdo->prepare("UPDATE `values` SET title = ?, display_order = ? WHERE id = ?");
                $stmt->execute([$title, $display_order, $id]);
                $message = 'Valor actualizado correctamente.';
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
        $stmt = $pdo->prepare("DELETE FROM `values` WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Valor de la organización eliminado correctamente.';
        $action = 'list';
    } catch (PDOException $e) {
        $error = 'Error al eliminar el valor: ' . $e->getMessage();
        $action = 'list';
    }
}

// Cargar Datos para Edición
$edit_data = [];
if ($action === 'edit' && $id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM `values` WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $edit_data = $stmt->fetch();
        if (!$edit_data) {
            $error = 'Valor no encontrado.';
            $action = 'list';
        }
    } catch (PDOException $e) {
        $error = 'Error al cargar datos: ' . $e->getMessage();
        $action = 'list';
    }
}

// Cargar Todos los Valores para Listado
$values = [];
if ($action === 'list') {
    try {
        $stmt = $pdo->query("SELECT * FROM `values` ORDER BY `display_order` ASC, `id` ASC");
        $values = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = 'Error al listar valores: ' . $e->getMessage();
    }
}

render_admin_header('Gestión de Valores');
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
            <h2><?php echo $action === 'add' ? 'Agregar Nuevo Valor' : 'Editar Valor'; ?></h2>
            <a href="values.php" class="btn btn-outline">Volver al Listado</a>
        </div>
        
        <form action="values.php?action=<?php echo $action; ?><?php echo $action === 'edit' ? '&id=' . $id : ''; ?>" method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label for="title">Nombre del Valor</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($edit_data['title'] ?? ''); ?>" required placeholder="Ej. Transparencia o Respeto">
                </div>
                
                <div class="form-group">
                    <label for="display_order">Orden de Visualización</label>
                    <input type="number" id="display_order" name="display_order" value="<?php echo htmlspecialchars($edit_data['display_order'] ?? '0'); ?>" required min="0">
                    <span class="form-help">Determina el orden de aparición en las tarjetas de valores.</span>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="values.php" class="btn btn-outline">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save"></i> Guardar Valor
                </button>
            </div>
        </form>
    </div>

<!-- VISTA: LISTAR -->
<?php else: ?>
    <div class="admin-card">
        <div class="card-header">
            <h2>Valores Institucionales</h2>
            <a href="values.php?action=add" class="btn btn-primary">
                <i data-lucide="plus-circle"></i> Agregar Valor
            </a>
        </div>
        
        <?php if (empty($values)): ?>
            <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                <i data-lucide="info" style="width: 40px; height: 40px; stroke-width: 1.5; margin-bottom: 12px; color: var(--primary);"></i>
                <p>No se encontraron valores registrados. Por favor, crea uno nuevo.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive" style="max-width: 600px; margin: 0 auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">#</th>
                            <th>Nombre del Valor</th>
                            <th style="width: 100px;">Orden</th>
                            <th style="width: 120px; text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $counter = 1;
                        foreach ($values as $val): 
                        ?>
                            <tr>
                                <td style="text-align: center; color: var(--text-muted); font-weight: 500;">
                                    <?php echo $counter++; ?>
                                </td>
                                <td style="font-weight: 600; font-size: 15px; color: var(--text-dark);">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <i data-lucide="check" style="color: var(--success); width: 18px; height: 18px; stroke-width: 3px;"></i>
                                        <?php echo htmlspecialchars($val['title']); ?>
                                    </div>
                                </td>
                                <td style="font-weight: 500;"><?php echo $val['display_order']; ?></td>
                                <td>
                                    <div class="table-actions" style="justify-content: center;">
                                        <a href="values.php?action=edit&id=<?php echo $val['id']; ?>" class="btn-icon edit" title="Editar">
                                            <i data-lucide="edit-3" style="width: 15px; height: 15px;"></i>
                                        </a>
                                        <a href="values.php?action=delete&id=<?php echo $val['id']; ?>" class="btn-icon delete" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar este valor?');">
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
