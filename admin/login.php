<?php
session_start();

// Incluir la conexión a la base de datos (redirigirá al setup si no hay DB)
require_once __DIR__ . '/../db.php';

// Si ya está logueado, redirigir al panel
if (isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// Verificar si existen usuarios registrados
$user_count = 0;
try {
    $user_count = $pdo->query("SELECT COUNT(*) FROM `users`")->fetchColumn();
} catch (PDOException $e) {
    $error = 'Error de conexión: ' . $e->getMessage();
}

$register_mode = ($user_count === 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($register_mode) {
        // --- MODO REGISTRO (SIN USUARIOS EN BD) ---
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
            $error = 'Todos los campos son obligatorios.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'El correo electrónico no es válido.';
        } elseif (strlen($password) < 6) {
            $error = 'La contraseña debe tener al menos 6 caracteres.';
        } elseif ($password !== $confirm_password) {
            $error = 'Las contraseñas no coinciden.';
        } else {
            try {
                $hashed_pass = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO `users` (username, password, email) VALUES (?, ?, ?)");
                $stmt->execute([$username, $hashed_pass, $email]);
                
                // Iniciar sesión automáticamente
                $_SESSION['admin_logged'] = true;
                $_SESSION['admin_id'] = $pdo->lastInsertId();
                $_SESSION['admin_username'] = $username;
                $_SESSION['admin_email'] = $email;
                
                header('Location: index.php');
                exit;
            } catch (PDOException $e) {
                $error = 'Error al registrar administrador: ' . $e->getMessage();
            }
        }
    } else {
        // --- MODO LOGIN NORMAL ---
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $error = 'Por favor, ingrese el usuario y la contraseña.';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `username` = ? LIMIT 1");
                $stmt->execute([$username]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($password, $user['password'])) {
                    // Login exitoso
                    session_regenerate_id(true);
                    $_SESSION['admin_logged'] = true;
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    $_SESSION['admin_email'] = $user['email'];
                    
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Usuario o contraseña incorrectos.';
                }
            } catch (PDOException $e) {
                $error = 'Error en el servidor: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $register_mode ? 'Crear Administrador' : 'Iniciar Sesión'; ?> - Fundación Visión de Futuro</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --primary: #9b98d5;
            --primary-hover: #8986c2;
            --secondary: #86d2f1;
            --light: #ffffff;
            --text-main: #334155;
            --text-muted: #64748b;
            --border: #cbd5e1;
            --danger: #ef4444;
            --card-bg: rgba(255, 255, 255, 0.95);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at 10% 20%, rgba(30, 41, 59, 0.95) 0%, rgba(15, 23, 42, 0.95) 90%), url('../logo.jpeg');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-container {
            background-color: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
            border-top: 5px solid var(--primary);
            backdrop-filter: blur(10px);
        }
        
        .login-header {
            padding: 30px 30px 20px 30px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .login-header h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 6px;
        }
        
        .login-header p {
            color: var(--text-muted);
            font-size: 13px;
        }
        
        .login-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 18px;
        }
        
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #475569;
            margin-bottom: 6px;
        }
        
        .form-group input {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
            background: #f8fafc;
        }
        
        .form-group input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(155, 152, 213, 0.2);
            background: var(--light);
        }
        
        .alert {
            background-color: #fee2e2;
            border: 1px solid #fca5a5;
            color: #b91c1c;
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            line-height: 1.4;
        }
        
        .btn-submit {
            display: block;
            width: 100%;
            background-color: var(--primary);
            color: #1e293b;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            text-align: center;
        }
        
        .btn-submit:hover {
            background-color: var(--primary-hover);
        }
        
        .footer-links {
            text-align: center;
            margin-top: 20px;
        }
        
        .footer-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 12px;
            transition: color 0.2s;
        }
        
        .footer-links a:hover {
            color: var(--primary);
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-header">
            <h1>Fundación Visión de Futuro</h1>
            <p><?php echo $register_mode ? 'Crear Cuenta de Administrador' : 'Panel de Control - Login'; ?></p>
        </div>
        
        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert">
                    <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($register_mode): ?>
                <!-- FORMULARIO DE REGISTRO CUANDO NO HAY USUARIOS -->
                <div style="background-color: rgba(134, 210, 241, 0.1); border: 1px solid rgba(134, 210, 241, 0.3); padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 12.5px; color: #0f766e; line-height: 1.4;">
                    ℹ️ <strong>¡Sin cuentas activas!</strong> Crea la primera cuenta de administrador para empezar a gestionar la web.
                </div>
                
                <form action="login.php" method="POST">
                    <div class="form-group">
                        <label for="username">Nombre de Usuario</label>
                        <input type="text" id="username" name="username" placeholder="Elige un usuario (ej. admin)" required autofocus>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" placeholder="correo@visiondefuturo.org" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Contraseña (Mínimo 6 caracteres)</label>
                        <input type="password" id="password" name="password" placeholder="Crea una contraseña segura" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirmar Contraseña</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Repite la contraseña" required>
                    </div>
                    
                    <button type="submit" class="btn-submit" style="background-color: var(--secondary); color: #0f172a;">Crear Admin e Iniciar Sesión</button>
                </form>
            <?php else: ?>
                <!-- FORMULARIO DE LOGIN NORMAL -->
                <form action="login.php" method="POST">
                    <div class="form-group">
                        <label for="username">Usuario</label>
                        <input type="text" id="username" name="username" placeholder="Ingresa tu usuario" required autofocus>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required>
                    </div>
                    
                    <button type="submit" class="btn-submit">Iniciar Sesión</button>
                </form>
            <?php endif; ?>
            
            <div class="footer-links">
                <a href="../index.php">← Volver a la Landing Page</a>
            </div>
        </div>
    </div>

</body>
</html>
