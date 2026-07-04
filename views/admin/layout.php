<?php
$page_title = $page_title ?? 'Panel';
$user = $user ?? Auth::user();
$unread_count = $unread_count ?? 0;
$pending_testimonials = $pending_testimonials ?? 0;
$route = $_GET['route'] ?? 'dashboard';

$color_primary = '#9b98d5'; $color_secondary = '#86d2f1'; $color_dark = '#1e293b';
try {
    $pdo = Database::connect();
    $stmt_c = $pdo->query("SELECT key_name, value_text FROM settings WHERE key_name IN ('theme_color_primary','theme_color_secondary','theme_color_dark')");
    while ($r = $stmt_c->fetch()) {
        if ($r['key_name'] === 'theme_color_primary') $color_primary = $r['value_text'];
        if ($r['key_name'] === 'theme_color_secondary') $color_secondary = $r['value_text'];
        if ($r['key_name'] === 'theme_color_dark') $color_dark = $r['value_text'];
    }
} catch (Exception $e) { /* use defaults */ }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - Fundación Visión de Futuro</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/admin-cards.css">
    <link rel="stylesheet" href="assets/css/admin-forms.css">
    <style>
        :root { --primary: <?= $color_primary ?>; --primary-hover: <?= $color_primary ?>cc; --secondary: <?= $color_secondary ?>; --secondary-hover: <?= $color_secondary ?>cc; --sidebar-bg: <?= $color_dark ?>; }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <div class="admin-bg-blobs"><div class="blob blob-1"></div><div class="blob blob-2"></div><div class="blob blob-3"></div></div>
    <div class="custom-cursor" id="customCursor"></div>
    <div class="custom-cursor-dot" id="customCursorDot"></div>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="sidebar-logo">
                <h2>Visión de Futuro</h2>
                <span>Servicios Integrales</span>
            </div>
            <nav class="sidebar-menu"><ul>
                <?php
                $menu = [
                    'dashboard'    => ['layout-dashboard', 'Resumen'],
                    'settings'     => ['sliders', 'Textos y Secciones'],
                    'gallery'      => ['image', 'Galería de Fotos'],
                    'charity'      => ['heart-handshake', 'Obras de Caridad'],
                    'testimonials' => ['message-circle', 'Testimonios'],
                    'services'     => ['utensils', 'Servicios'],
                    'benefits'     => ['award', '¿Por qué elegirnos?'],
                    'values'       => ['heart', 'Valores'],
                    'messages'     => ['mail', 'Bandeja de Entrada'],
                ];
                foreach ($menu as $key => [$icon, $label]):
                    $active = ($route === $key) ? 'active' : '';
                    $badge = '';
                    if ($key === 'messages' && $unread_count > 0) $badge = '<span class="unread-badge">' . $unread_count . '</span>';
                    if ($key === 'testimonials' && $pending_testimonials > 0) $badge = '<span class="unread-badge">' . $pending_testimonials . '</span>';
                ?>
                <li><a href="/admin.php?route=<?= $key ?>" class="<?= $active ?>">
                    <i data-lucide="<?= $icon ?>"></i><span><?= $label ?></span><?= $badge ?>
                </a></li>
                <?php endforeach; ?>
            </ul></nav>
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar"><?= strtoupper(substr($user['username'] ?? 'A', 0, 1)) ?></div>
                    <div class="user-details">
                        <p class="username"><?= htmlspecialchars($user['username'] ?? 'Admin') ?></p>
                        <p class="role">Administrador</p>
                    </div>
                </div>
                <a href="/admin.php?route=logout" class="btn-logout" title="Cerrar Sesión">
                    <i data-lucide="log-out"></i><span>Cerrar Sesión</span>
                </a>
            </div>
        </aside>
        <main class="admin-main">
            <header class="admin-topbar">
                <button class="sidebar-toggle" id="sidebarToggle"><i data-lucide="menu"></i></button>
                <div class="topbar-title"><h1><?= htmlspecialchars($page_title) ?></h1></div>
                <div class="topbar-actions">
                    <a href="/index.php" target="_blank" class="btn-view-site"><i data-lucide="external-link"></i><span>Ver Sitio Web</span></a>
                </div>
            </header>
            <div class="admin-content"><?= $content ?? '' ?></div>
        </main>
    </div>
    <script>
        lucide.createIcons();
        const sb = document.getElementById("sidebarToggle"), side = document.querySelector(".admin-sidebar");
        if (sb && side) sb.addEventListener("click", () => side.classList.toggle("mobile-open"));
        const cur = document.getElementById("customCursor"), dot = document.getElementById("customCursorDot");
        if (cur && dot && window.matchMedia("(pointer: fine)").matches) {
            let mx=0,my=0,cx=0,cy=0;
            document.addEventListener("mousemove", e => { mx=e.clientX; my=e.clientY; dot.style.left=mx+"px"; dot.style.top=my+"px"; });
            (function anim(){ cx+=(mx-cx)*0.15; cy+=(my-cy)*0.15; cur.style.left=cx+"px"; cur.style.top=cy+"px"; requestAnimationFrame(anim); })();
            document.querySelectorAll("a,button,input,select,textarea,.btn-icon,.stat-card,tr").forEach(el => {
                el.addEventListener("mouseenter", () => { cur.classList.add("cursor-hover"); dot.classList.add("cursor-dot-hover"); });
                el.addEventListener("mouseleave", () => { cur.classList.remove("cursor-hover"); dot.classList.remove("cursor-dot-hover"); });
            });
        } else { if(cur)cur.style.display="none"; if(dot)dot.style.display="none"; }
    </script>
</body>
</html>
