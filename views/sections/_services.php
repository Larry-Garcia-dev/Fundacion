<section id="services" class="section services-section bg-light">
    <div class="container">
        <div class="section-header text-center scroll-reveal">
            <div class="section-tag">Nuestros Servicios</div>
            <h2 class="section-title">Soluciones profesionales adaptadas a cada necesidad</h2>
            <p class="section-desc text-muted">Brindamos una gama integral de servicios de alta calidad, enfocados especialmente en el sector salud y proyectos sociales.</p>
        </div>
        <?php if (empty($services)): ?>
            <div class="text-center text-muted">Próximamente más información sobre nuestros servicios.</div>
        <?php else: ?>
            <div class="grid grid-3 gap-lg">
                <?php foreach ($services as $srv): ?>
                    <div class="card service-card scroll-reveal">
                        <div class="card-icon-box">
                            <i data-lucide="<?= htmlspecialchars($srv['icon']) ?>"></i>
                        </div>
                        <h3 class="card-title"><?= htmlspecialchars($srv['title']) ?></h3>
                        <p class="card-text text-muted"><?= htmlspecialchars($srv['description']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
