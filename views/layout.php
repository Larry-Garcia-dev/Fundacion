<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="logos/VisiónLogo_Morado.png">
    <title><?= htmlspecialchars($settings['hero_title'] ?? 'Fundación Visión de Futuro - Servicios Integrales') ?></title>
    <meta name="description" content="<?= htmlspecialchars(substr($settings['hero_subtitle'] ?? '', 0, 160)) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/hero.css">
    <link rel="stylesheet" href="assets/css/sections.css">
    <link rel="stylesheet" href="assets/css/gallery.css">
    <link rel="stylesheet" href="assets/css/charity.css">
    <link rel="stylesheet" href="assets/css/testimonials.css">
    <link rel="stylesheet" href="assets/css/contact.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/utilities.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary: <?= htmlspecialchars($settings['theme_color_primary'] ?? '#9b98d5') ?>;
            --teal: <?= htmlspecialchars($settings['theme_color_secondary'] ?? '#86d2f1') ?>;
            --dark: <?= htmlspecialchars($settings['theme_color_dark'] ?? '#1e293b') ?>;
        }
        .logo-img {
            height: <?= htmlspecialchars($settings['logo_size'] ?? '52') ?>px !important;
            transform: translate(<?= htmlspecialchars($settings['logo_offset_x'] ?? '0') ?>px, <?= htmlspecialchars($settings['logo_offset_y'] ?? '0') ?>px) !important;
        }
        .hero-section {
            background-image: url('<?= htmlspecialchars($settings['hero_bg_image'] ?? 'https://images.unsplash.com/photo-1593113598332-cd288d649433?auto=format&fit=crop&w=1920&q=80') ?>') !important;
        }
    </style>
</head>
<body>
    <header class="site-header" id="siteHeader">
        <div class="header-container">
            <a href="#hero" class="logo">
                <img src="<?= htmlspecialchars($settings['logo_path'] ?? 'logo1.png') ?>" alt="Logo" class="logo-img">
            </a>
            <button class="nav-toggle" id="navToggle" aria-label="Abrir Menú"><i data-lucide="menu"></i></button>
            <nav class="nav-menu" id="navMenu">
                <ul>
                    <li><a href="#hero" class="nav-link active">Inicio</a></li>
                    <li><a href="#about" class="nav-link">¿Quiénes Somos?</a></li>
                    <li><a href="#services" class="nav-link">Servicios</a></li>
                    <li><a href="#benefits" class="nav-link">¿Por qué elegirnos?</a></li>
                    <li><a href="#mision-vision" class="nav-link">Misión y Visión</a></li>
                    <li><a href="#values" class="nav-link">Valores</a></li>
                    <li><a href="#gallery" class="nav-link">Galería</a></li>
                    <li><a href="#charity-works" class="nav-link">Obras Sociales</a></li>
                    <?php if (!empty($settings['testimonials_enabled'])): ?>
                    <li><a href="#testimonials" class="nav-link">Testimonios</a></li>
                    <?php endif; ?>
                    <li><a href="#contact" class="btn btn-primary-nav">Contacto</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main><?= $content ?></main>
    <footer class="site-footer">
        <div class="footer-top">
            <div class="container footer-grid">
                <div class="footer-brand">
                    <div class="footer-logo-container">
                        <img src="<?= htmlspecialchars($settings['footer_logo_path'] ?? $settings['logo_path'] ?? 'logo1.png') ?>" alt="Logo" class="footer-logo-img">
                    </div>
                    <p class="text-muted-light mt-sm">Servicios Integrales comprometidos con la calidad de vida, alimentación inocua y proyectos de desarrollo social en el país.</p>
                </div>
                <div class="footer-links-col">
                    <h4>Enlaces rápidos</h4>
                    <ul>
                        <li><a href="#hero">Inicio</a></li>
                        <li><a href="#about">¿Quiénes Somos?</a></li>
                        <li><a href="#services">Servicios</a></li>
                        <li><a href="#gallery">Galería de Fotos</a></li>
                        <li><a href="#charity-works">Obras Sociales</a></li>
                        <?php if (!empty($settings['testimonials_enabled'])): ?>
                        <li><a href="#testimonials">Testimonios</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="footer-links-col">
                    <h4>Contacto</h4>
                    <p class="footer-contact-p"><i data-lucide="map-pin"></i> <?= htmlspecialchars($settings['footer_address'] ?? 'Ibagué – Tolima') ?></p>
                    <p class="footer-contact-p"><i data-lucide="phone"></i> <?= htmlspecialchars($settings['footer_phone'] ?? '') ?></p>
                    <p class="footer-contact-p"><i data-lucide="mail"></i> <a href="mailto:<?= htmlspecialchars($settings['footer_email'] ?? '') ?>"><?= htmlspecialchars($settings['footer_email'] ?? '') ?></a></p>
                </div>
                <div class="footer-links-col">
                    <h4>Redes Sociales</h4>
                    <div class="social-icons">
                        <?php if (!empty($settings['footer_facebook'])): ?>
                        <a href="<?= htmlspecialchars($settings['footer_facebook']) ?>" target="_blank" aria-label="Facebook"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['footer_instagram'])): ?>
                        <a href="<?= htmlspecialchars($settings['footer_instagram']) ?>" target="_blank" aria-label="Instagram"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['footer_twitter'])): ?>
                        <a href="<?= htmlspecialchars($settings['footer_twitter']) ?>" target="_blank" aria-label="Twitter"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['footer_linkedin'])): ?>
                        <a href="<?= htmlspecialchars($settings['footer_linkedin']) ?>" target="_blank" aria-label="LinkedIn"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container footer-bottom-container">
                <p>&copy; <?= date('Y') ?> Fundación Visión de Futuro - Servicios Integrales. Todos los derechos reservados.</p>
                <a href="admin/login.php" class="admin-link-footer"><i data-lucide="shield-alert"></i> Administrador</a>
            </div>
        </div>
    </footer>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/forms.js"></script>
    <script src="assets/js/testimonials.js"></script>
    <script src="assets/js/gallery.js"></script>
    <script src="assets/js/carousel.js"></script>
</body>
</html>
