<?php $error = $error ?? ''; $register_mode = $register_mode ?? false; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $register_mode ? 'Crear Administrador' : 'Iniciar Sesión' ?> - Fundación Visión de Futuro</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --bg-color:#0f172a; --primary:#9b98d5; --primary-hover:#8986c2; --secondary:#86d2f1; --light:#fff; --text-main:#334155; --text-muted:#64748b; --border:#cbd5e1; --danger:#ef4444; --card-bg:rgba(255,255,255,0.95); }
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Inter',sans-serif;background:radial-gradient(circle at 10% 20%,rgba(30,41,59,0.95) 0%,rgba(15,23,42,0.95) 90%),url('logo.jpeg');background-size:cover;background-position:center;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px;}
        .login-container{background-color:var(--card-bg);border-radius:16px;box-shadow:0 20px 25px -5px rgba(0,0,0,0.3);width:100%;max-width:420px;overflow:hidden;border-top:5px solid var(--primary);backdrop-filter:blur(10px);}
        .login-header{padding:30px 30px 20px;text-align:center;border-bottom:1px solid #e2e8f0;}
        .login-header h1{font-family:'Outfit',sans-serif;font-size:20px;font-weight:700;color:#1e293b;margin-bottom:6px;}
        .login-header p{color:var(--text-muted);font-size:13px;}
        .login-body{padding:30px;}
        .form-group{margin-bottom:18px;}
        .form-group label{display:block;font-size:13px;font-weight:500;color:#475569;margin-bottom:6px;}
        .form-group input{width:100%;padding:11px 14px;border:1px solid #cbd5e1;border-radius:8px;font-size:14px;outline:none;transition:all .2s;background:#f8fafc;}
        .form-group input:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(155,152,213,0.2);background:var(--light);}
        .alert{background-color:#fee2e2;border:1px solid #fca5a5;color:#b91c1c;padding:12px 15px;border-radius:8px;font-size:13px;margin-bottom:20px;}
        .btn-submit{display:block;width:100%;background-color:var(--primary);color:#1e293b;border:none;padding:12px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;transition:background .2s;text-align:center;}
        .btn-submit:hover{background-color:var(--primary-hover);}
        .footer-links{text-align:center;margin-top:20px;}
        .footer-links a{color:var(--text-muted);text-decoration:none;font-size:12px;}
        .footer-links a:hover{color:var(--primary);}
        .info-box{background:rgba(134,210,241,0.1);border:1px solid rgba(134,210,241,0.3);padding:12px;border-radius:8px;margin-bottom:20px;font-size:12.5px;color:#0f766e;line-height:1.4;}
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Fundación Visión de Futuro</h1>
            <p><?= $register_mode ? 'Crear Cuenta de Administrador' : 'Panel de Control - Login' ?></p>
        </div>
        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert"><strong>Error:</strong> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($register_mode): ?>
                <div class="info-box">
                    <strong>Sin cuentas activas.</strong> Crea la primera cuenta de administrador para empezar.
                </div>
                <form action="/admin.php?route=login" method="POST">
                    <div class="form-group"><label>Nombre de Usuario</label><input type="text" name="username" placeholder="Elige un usuario" required autofocus></div>
                    <div class="form-group"><label>Correo Electrónico</label><input type="email" name="email" placeholder="correo@visiondefuturo.org" required></div>
                    <div class="form-group"><label>Contraseña (Mínimo 6 caracteres)</label><input type="password" name="password" placeholder="Crea una contraseña" required></div>
                    <div class="form-group"><label>Confirmar Contraseña</label><input type="password" name="confirm_password" placeholder="Repite la contraseña" required></div>
                    <button type="submit" class="btn-submit" style="background:var(--secondary);color:#0f172a;">Crear Admin e Iniciar Sesión</button>
                </form>
            <?php else: ?>
                <form action="/admin.php?route=login" method="POST">
                    <div class="form-group"><label>Usuario</label><input type="text" name="username" placeholder="Ingresa tu usuario" required autofocus></div>
                    <div class="form-group"><label>Contraseña</label><input type="password" name="password" placeholder="Ingresa tu contraseña" required></div>
                    <button type="submit" class="btn-submit">Iniciar Sesión</button>
                </form>
            <?php endif; ?>
            <div class="footer-links"><a href="/index.php">Volver a la Landing Page</a></div>
        </div>
    </div>
</body>
</html>
