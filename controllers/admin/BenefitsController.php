<?php
class BenefitsController
{
    private const ICONS = [
        'award' => 'Premio / Excelencia', 'shield' => 'Protección / Inocuidad',
        'heart' => 'Amor / Responsabilidad', 'user-check' => 'Talento Humano',
        'check-circle' => 'Verificación / Calidad', 'star' => 'Destacado',
        'clock' => 'Puntualidad', 'thumbs-up' => 'Aprobación / Confianza',
        'trending-up' => 'Crecimiento / Futuro', 'users' => 'Comunidad',
        'gem' => 'Valor Premium',
    ];

    private BenefitModel $model;

    public function __construct()
    {
        Auth::require();
        $this->model = new BenefitModel();
    }

    public function indexAction(): void
    {
        View::render('admin/benefits', [
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
            $icon        = trim($_POST['icon'] ?? 'award');
            $order       = (int) ($_POST['display_order'] ?? 0);

            if (empty($title) || empty($description)) {
                $error = 'El título y la descripción son obligatorios.';
            } else {
                $data = compact('title', 'description', 'icon') + ['display_order' => $order];
                $id > 0 ? $this->model->update($id, $data) : $this->model->create($data);
                header('Location: /admin.php?route=benefits');
                exit;
            }
        }

        View::render('admin/benefits_edit', [
            'item'            => $item,
            'id'              => $id,
            'error'           => $error,
            'available_icons' => self::ICONS,
        ]);
    }

    public function deleteAction(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->model->delete($id);
        }
        header('Location: /admin.php?route=benefits');
        exit;
    }
}
