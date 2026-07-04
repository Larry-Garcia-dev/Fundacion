<?php
class GalleryController
{
    private GalleryModel $model;

    public function __construct()
    {
        Auth::require();
        $this->model = new GalleryModel();
    }

    public function indexAction(): void
    {
        View::render('admin/gallery', [
            'items' => $this->model->allOrdered(),
        ]);
    }

    public function editAction(): void
    {
        $id   = (int) ($_GET['id'] ?? 0);
        $item = $id > 0 ? $this->model->find($id) : [];

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $image_url = trim($_POST['image_url'] ?? '');
            $caption   = trim($_POST['caption'] ?? '');
            $order     = (int) ($_POST['display_order'] ?? 0);

            $existing = $item['image_url'] ?? '';
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                try {
                    $image_url = Uploader::image('image_file', $existing);
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
            }

            if (empty($image_url) && empty($error)) {
                $error = 'Debes subir una imagen o ingresar una URL.';
            }

            if (empty($error)) {
                $data = ['image_url' => $image_url, 'caption' => $caption, 'display_order' => $order];
                $id > 0 ? $this->model->update($id, $data) : $this->model->create($data);
                header('Location: /admin.php?route=gallery');
                exit;
            }
        }

        View::render('admin/gallery_edit', [
            'item'  => $item,
            'id'    => $id,
            'error' => $error,
        ]);
    }

    public function deleteAction(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) {
            $item = $this->model->find($id);
            if ($item) {
                $path = $item['image_url'] ?? '';
                if (!empty($path) && str_starts_with($path, 'uploads/')) {
                    $full = __DIR__ . '/../../' . $path;
                    if (file_exists($full)) unlink($full);
                }
                $this->model->delete($id);
            }
        }
        header('Location: /admin.php?route=gallery');
        exit;
    }
}
