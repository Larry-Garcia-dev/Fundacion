<?php
class ValuesController
{
    private OrgValueModel $model;

    public function __construct()
    {
        Auth::require();
        $this->model = new OrgValueModel();
    }

    public function indexAction(): void
    {
        View::render('admin/values', [
            'items' => $this->model->allOrdered(),
        ]);
    }

    public function editAction(): void
    {
        $id   = (int) ($_GET['id'] ?? 0);
        $item = $id > 0 ? $this->model->find($id) : [];

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $order = (int) ($_POST['display_order'] ?? 0);

            if (empty($title)) {
                $error = 'El nombre del valor es obligatorio.';
            } else {
                $data = ['title' => $title, 'display_order' => $order];
                $id > 0 ? $this->model->update($id, $data) : $this->model->create($data);
                header('Location: /admin.php?route=values');
                exit;
            }
        }

        View::render('admin/values_edit', [
            'item'  => $item,
            'id'    => $id,
            'error' => $error,
        ]);
    }

    public function deleteAction(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->model->delete($id);
        }
        header('Location: /admin.php?route=values');
        exit;
    }
}
