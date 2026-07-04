<section id="charity-works" class="section charity-section bg-light">
    <div class="container">
        <div class="section-header text-center scroll-reveal">
            <div class="section-tag">Obras Sociales</div>
            <h2 class="section-title">Obras de Caridad y Proyectos Realizados</h2>
            <p class="section-desc text-muted">Conoce el impacto positivo y la ayuda social que brindamos diariamente a las comunidades vulnerables.</p>
        </div>

        <?php if (empty($works)): ?>
            <div class="text-center text-muted">Próximamente más proyectos sociales.</div>
        <?php else: ?>
            <div class="carousel-container scroll-reveal">
                <div class="carousel-wrapper" id="carouselWrapper">
                    <?php foreach ($works as $index => $work): ?>
                        <div class="carousel-slide-item <?= $index === 0 ? 'active' : '' ?>">
                            <div class="work-card">
                                <div class="work-img-box">
                                    <img src="<?= htmlspecialchars($work['image_url']) ?>" alt="<?= htmlspecialchars($work['title']) ?>">
                                </div>
                                <div class="work-content">
                                    <div class="work-date">
                                        <i data-lucide="calendar"></i> <?= date('d/m/Y', strtotime($work['created_at'])) ?>
                                    </div>
                                    <h3 class="work-card-title"><?= htmlspecialchars($work['title']) ?></h3>
                                    <p class="work-card-desc"><?= htmlspecialchars($work['description']) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (count($works) > 1): ?>
                    <div class="carousel-nav-controls">
                        <button class="carousel-btn prev-btn" id="carouselPrev" aria-label="Anterior"><i data-lucide="chevron-left"></i></button>
                        <button class="carousel-btn next-btn" id="carouselNext" aria-label="Siguiente"><i data-lucide="chevron-right"></i></button>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
