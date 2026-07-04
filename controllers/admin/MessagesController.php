<?php
class MessagesController
{
    private ContactMessageModel $model;

    public function __construct()
    {
        Auth::require();
        $this->model = new ContactMessageModel();
    }

    public function indexAction(): void
    {
        $filter = $_GET['filter'] ?? 'all';

        View::render('admin/messages', [
            'items'  => $this->model->allFiltered($filter),
            'filter' => $filter,
        ]);
    }

    public function editAction(): void
    {
        $id     = (int) ($_GET['id'] ?? 0);
        $action = $_GET['do'] ?? '';

        if ($id > 0) {
            if ($action === 'archive') {
                $this->model->update($id, ['status' => 'archived']);
            } elseif ($action === 'mark_unread') {
                $this->model->update($id, ['status' => 'unread']);
            } elseif ($action === 'view') {
                $msg = $this->model->find($id);
                if ($msg && $msg['status'] === 'unread') {
                    $this->model->markRead($id);
                }

                View::render('admin/message_detail', [
                    'message' => $msg,
                ]);
                return;
            }
        }

        $filter = $_GET['filter'] ?? 'all';
        header('Location: /admin.php?route=messages&filter=' . $filter);
        exit;
    }

    public function deleteAction(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->model->delete($id);
        }

        $filter = $_GET['filter'] ?? 'all';
        header('Location: /admin.php?route=messages&filter=' . $filter);
        exit;
    }
}
