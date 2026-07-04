<?php
class TestimonialsController
{
    private TestimonialModel $model;

    public function __construct()
    {
        Auth::require();
        $this->model = new TestimonialModel();
    }

    public function indexAction(): void
    {
        $filter = $_GET['filter'] ?? 'all';
        $valid  = ['all', 'pending', 'approved', 'rejected'];
        if (!in_array($filter, $valid)) $filter = 'all';

        View::render('admin/testimonials', [
            'items'        => $filter === 'all' ? $this->model->all() : $this->model->allByStatus($filter),
            'filter'       => $filter,
            'counts'       => [
                'all'      => $this->model->count(),
                'pending'  => $this->model->count('pending'),
                'approved' => $this->model->count('approved'),
                'rejected' => $this->model->count('rejected'),
            ],
        ]);
    }

    public function editAction(): void
    {
        $id     = (int) ($_GET['id'] ?? 0);
        $action = $_GET['action'] ?? '';

        if ($id > 0 && in_array($action, ['approve', 'reject'])) {
            $status = ($action === 'approve') ? 'approved' : 'rejected';
            $this->model->update($id, ['status' => $status]);
        }

        header('Location: /admin.php?route=testimonials&filter=' . ($_GET['filter'] ?? 'all'));
        exit;
    }

    public function deleteAction(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) {
            $item = $this->model->find($id);
            if ($item && !empty($item['photo_url']) && str_starts_with($item['photo_url'], 'uploads/')) {
                $full = __DIR__ . '/../../' . $item['photo_url'];
                if (file_exists($full)) unlink($full);
            }
            $this->model->delete($id);
        }

        header('Location: /admin.php?route=testimonials&filter=' . ($_GET['filter'] ?? 'all'));
        exit;
    }
}
