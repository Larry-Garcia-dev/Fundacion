<?php
require_once __DIR__ . '/../db.php';

// Contraseña temporal
$new_password = 'admin'; 
$hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

try {
    // Intentar actualizar la contraseña para el usuario 'admin'
    $stmt = $pdo->prepare("UPDATE `users` SET `password` = ? WHERE `username` = 'admin'");
    $stmt->execute([$hashed_password]);
    
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <title>Contraseña Restablecida - Fundación Visión de Futuro</title>
        <style>
            body { font-family: sans-serif; background: #f8fafc; color: #0f172a; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
            .card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); text-align: center; max-width: 500px; }
            h1 { color: #10b981; margin-top: 0; }
            p { color: #475569; line-height: 1.6; }
            .password-box { background: #f1f5f9; padding: 12px; border-radius: 6px; font-family: monospace; font-size: 18px; font-weight: bold; margin: 20px 0; color: #0f172a; border: 1px dashed #cbd5e1; }
            .btn { display: inline-block; background: #2563eb; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='card'>
            <h1>¡Contraseña Restablecida!</h1>
            <p>La contraseña del usuario administrador <strong>admin</strong> ha sido cambiada con éxito.</p>
            <p>Tu contraseña temporal es:</p>
            <div class='password-box'>admin</div>
            <p style='color: #dc2626; font-size: 13px; margin-bottom: 20px;'><strong>Importante:</strong> Por motivos de seguridad, cambia esta contraseña inmediatamente al iniciar sesión y borra el archivo <code>admin/reset.php</code> de tu carpeta del proyecto.</p>
            <a href='login.php' class='btn'>Ir al Login</a>
        </div>
    </body>
    </html>";
} catch (PDOException $e) {
    echo "Error al restablecer la contraseña: " . $e->getMessage();
}
?>
