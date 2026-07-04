<?php $step = $step ?? 'form'; $error = $error ?? ''; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación - Fundación Visión de Futuro</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root{--primary:#9b98d5;--secondary:#86d2f1;--dark:#0f172a;--text-muted:#64748b;--border:#cbd5e1;--card-bg:rgba(255,255,255,0.97);}
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px;}
        .setup-container{background:var(--card-bg);border-radius:16px;box-shadow:0 25px 50px rgba(0,0,0,0.4);width:100%;max-width:560px;overflow:hidden;border-top:5px solid var(--primary);}
        .setup-header{padding:30px;text-align:center;border-bottom:1px solid #e2e8f0;}
        .setup-header h1{font-family:'Outfit',sans-serif;font-size:22px;font-weight:700;color:#1e293b;margin-bottom:6px;}
        .setup-header p{color:var(--text-muted);font-size:13px;}
        .setup-body{padding:30px;}
        .form-group{margin-bottom:16px;}
        .form-group label{display:block;font-size:13px;font-weight:500;color:#475569;margin-bottom:5px;}
        .form-group input{width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:8px;font-size:14px;outline:none;background:#f8fafc;}
        .form-group input:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(155,152,213,0.2);}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
        .alert{background:#fee2e2;border:1px solid #fca5a5;color:#b91c1c;padding:12px;border-radius:8px;font-size:13px;margin-bottom:18px;}
        .success-box{background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:20px;border-radius:12px;text-align:center;}
        .success-box h2{font-family:'Outfit',sans-serif;margin-bottom:8px;}
        .btn-submit{display:block;width:100%;background:var(--primary);color:#1e293b;border:none;padding:13px;border-radius:8px;font-size:15px;font-weight:700;cursor:pointer;margin-top:10px;}
        .btn-submit:hover{background:#8986c2;}
        .section-title{font-family:'Outfit',sans-serif;font-size:14px;font-weight:600;color:var(--primary);margin:20px 0 12px;padding-bottom:6px;border-bottom:1px solid #e2e8f0;}
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-header">
            <h1>Asistente de Instalación</h1>
            <p>Fundación Visión de Futuro</p>
        </div>
        <div class="setup-body">
            <?php if ($step === 'success'): ?>
                <div class="success-box">
                    <h2>Instalación Completada</h2>
                    <p style="color:#065f46;font-size:14px;">La base de datos y las tablas se han creado correctamente. Ya puedes iniciar sesión.</p>
                    <a href="/admin.php?route=login" style="display:inline-block;margin-top:16px;padding:10px 24px;background:#065f46;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;">Ir al Login</a>
                </div>
            <?php else: ?>
                <?php if ($error): ?><div class="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <form action="/admin.php?route=setup" method="POST">
                    <div class="section-title">Base de Datos</div>
                    <div class="form-row">
                        <div class="form-group"><label>Servidor</label><input type="text" name="db_host" value="localhost" required></div>
                        <div class="form-group"><label>Nombre de BD</label><input type="text" name="db_name" value="landing_db" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Usuario BD</label><input type="text" name="db_user" value="root" required></div>
                        <div class="form-group"><label>Contraseña BD</label><input type="password" name="db_pass" placeholder="(vacío si no tiene)"></div>
                    </div>
                    <div class="section-title">Cuenta Administrador</div>
                    <div class="form-row">
                        <div class="form-group"><label>Usuario</label><input type="text" name="admin_user" value="admin" required></div>
                        <div class="form-group"><label>Correo</label><input type="email" name="admin_email" value="admin@visiondefuturo.org" required></div>
                    </div>
                    <div class="form-group"><label>Contraseña (Mínimo 6 caracteres)</label><input type="password" name="admin_pass" required></div>
                    <button type="submit" class="btn-submit">Instalar Ahora</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
