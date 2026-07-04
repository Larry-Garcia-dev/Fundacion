<?php
class LoginController
{
    public function indexAction(): void
    {
        if (Auth::check()) {
            header('Location: /admin.php?route=dashboard');
            exit;
        }

        $error        = '';
        $users        = new UserModel();
        $register_mode = ($users->count() === 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($register_mode) {
                $error = $this->handleRegister($users);
            } else {
                $error = $this->handleLogin($users);
            }

            if (empty($error)) {
                header('Location: /admin.php?route=dashboard');
                exit;
            }
        }

        View::render('admin/login', [
            'error'         => $error,
            'register_mode' => $register_mode,
        ]);
    }

    private function handleLogin(UserModel $users): string
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            return 'Por favor ingrese usuario y contraseña.';
        }

        $user = $users->findByUsername($username);
        if (!$user || !password_verify($password, $user['password'])) {
            return 'Usuario o contraseña incorrectos.';
        }

        Auth::login((int) $user['id'], $user['username'], $user['email']);
        return '';
    }

    private function handleRegister(UserModel $users): string
    {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
            return 'Todos los campos son obligatorios.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'El correo electrónico no es válido.';
        }
        if (strlen($password) < 6) {
            return 'La contraseña debe tener al menos 6 caracteres.';
        }
        if ($password !== $confirm) {
            return 'Las contraseñas no coinciden.';
        }

        $id = $users->create($username, $email, $password);
        Auth::login($id, $username, $email);
        return '';
    }
}
