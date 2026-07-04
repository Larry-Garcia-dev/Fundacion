<?php
class ServicesController
{
    private const ICONS = [
        'utensils' => 'Cubiertos / Alimentación', 'briefcase' => 'Portafolio / Negocios',
        'users' => 'Comunidad', 'handshake' => 'Acompañamiento / Alianza',
        'heart' => 'Salud / Bienestar', 'award' => 'Calidad / Premio',
        'shield' => 'Seguridad / Inocuidad', 'activity' => 'Actividad / Salud',
        'globe' => 'Mundo / Social', 'graduation-cap' => 'Educación',
        'smile' => 'Felicidad / Compromiso', 'settings' => 'Operación / Soporte',
    ];

    private ServiceModel $model;

    public function __construct()
    {
        Auth::require();
        $this->model = new ServiceModel();
    }

    public function indexAction(): void
    {
        View::render('admin/services', [
            'items' => $this->model->allOrdered(),
        ]);
    }

    public function editAction(): void
    {
        $id   = (int) ($_GET['id'] ?? 0);
        $item = $id > 0 ? $this->model->find($id) : [];

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title       = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $icon        = trim($_POST['icon'] ?? 'briefcase');
            $order       = (int) ($_POST['display_order'] ?? 0);

            if (empty($title) || empty($description)) {
                $error = 'El título y la descripción son obligatorios.';
            } else {
                $data = compact('title', 'description', 'icon') + ['display_order' => $order];
                $id > 0 ? $this->model->update($id, $data) : $this->model->create($data);
                header('Location: /admin.php?route=services');
                exit;
            }
        }

        View::render('admin/services_edit', [
            'item'           => $item,
            'id'             => $id,
            'error'          => $error,
            'available_icons' => self::ICONS,
        ]);
    }

    public function deleteAction(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->model->delete($id);
        }
        header('Location: /admin.php?route=services');
        exit;
    }
}
