<section id="about" class="section about-section">
    <div class="container">
        <div class="grid grid-2">
            <div class="about-text scroll-reveal">
                <div class="section-tag"><?= htmlspecialchars($settings['about_title'] ?? '¿Quiénes somos?') ?></div>
                <h2 class="section-title"><?= htmlspecialchars($settings['about_subtitle'] ?? 'Construimos bienestar a través de servicios integrales.') ?></h2>
                <p class="about-p main-p"><?= htmlspecialchars($settings['about_content_1'] ?? '') ?></p>
                <p class="about-p text-muted"><?= htmlspecialchars($settings['about_content_2'] ?? '') ?></p>
                <div class="about-stats-mini">
                    <div class="stat-mini-item">
                        <span class="stat-num">100%</span>
                        <span class="stat-lbl">Inocuidad en alimentos</span>
                    </div>
                    <div class="stat-mini-item">
                        <span class="stat-num">10+</span>
                        <span class="stat-lbl">Años de impacto</span>
                    </div>
                </div>
            </div>
            <div class="about-image-wrapper scroll-reveal">
                <div class="image-box">
                    <img src="<?= htmlspecialchars($settings['about_img_url'] ?? 'https://images.unsplash.com/photo-1593113598332-cd288d649433?auto=format&fit=crop&w=800&q=80') ?>" alt="Compromiso con la alimentación y nutrición" class="about-img">
                    <div class="experience-badge">
                        <i data-lucide="heart-handshake"></i>
                        <span>Vocación Social</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
