<section id="hero" class="hero-section">
    <div class="hero-bg-overlay"></div>
    <div class="container hero-container">
        <div class="hero-content">
            <span class="hero-badge animate-fade-in">Salud & Desarrollo Social</span>
            <h1 class="animate-fade-in-up"><?= htmlspecialchars($settings['hero_title'] ?? 'Comprometidos con el bienestar, la calidad y el desarrollo social.') ?></h1>
            <p class="hero-lead animate-fade-in-up delay-1"><?= htmlspecialchars($settings['hero_subtitle'] ?? '') ?></p>
            <div class="hero-actions animate-fade-in-up delay-2">
                <a href="#contact" class="btn btn-primary btn-lg"><?= htmlspecialchars($settings['hero_btn_primary'] ?? 'Solicitar información') ?></a>
                <a href="#services" class="btn btn-outline-light btn-lg"><?= htmlspecialchars($settings['hero_btn_secondary'] ?? 'Conoce nuestros servicios') ?></a>
            </div>
        </div>
        <div class="hero-logo animate-fade-in-up delay-1">
            <img src="<?= htmlspecialchars($settings['hero_logo_path'] ?? 'logo1.png') ?>" alt="Fundación Visión de Futuro" class="hero-logo-img">
        </div>
    </div>
    <div class="hero-scroll-indicator">
        <a href="#about" aria-label="Bajar a la sección nosotros"><i data-lucide="chevron-down"></i></a>
    </div>
</section>
