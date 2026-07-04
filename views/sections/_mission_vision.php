<section id="mision-vision" class="section mission-vision-section">
    <div class="container">
        <div class="grid grid-2 gap-lg">
            <div class="mv-card scroll-reveal">
                <div class="mv-icon-box"><i data-lucide="compass"></i></div>
                <h2 class="mv-title"><?= htmlspecialchars($settings['mission_title'] ?? 'Nuestra Misión') ?></h2>
                <p class="mv-text text-muted"><?= htmlspecialchars($settings['mission_content'] ?? '') ?></p>
            </div>
            <div class="mv-card scroll-reveal">
                <div class="mv-icon-box vision"><i data-lucide="eye"></i></div>
                <h2 class="mv-title"><?= htmlspecialchars($settings['vision_title'] ?? 'Nuestra Visión') ?></h2>
                <p class="mv-text text-muted"><?= htmlspecialchars($settings['vision_content'] ?? '') ?></p>
            </div>
        </div>
    </div>
</section>
