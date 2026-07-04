<section id="gallery" class="section gallery-section">
    <div class="container">
        <div class="section-header text-center scroll-reveal">
            <div class="section-tag">Galería</div>
            <h2 class="section-title">Nuestra Labor en Imágenes</h2>
            <p class="section-desc text-muted">Fotografías reales de nuestras actividades, proyectos y las sonrisas de los niños que apoyamos.</p>
        </div>

        <?php if (empty($gallery)): ?>
            <div class="text-center text-muted">Galería en construcción.</div>
        <?php else: ?>
            <div class="gallery-slideshow-container scroll-reveal">
                <?php foreach ($gallery as $index => $item): ?>
                    <div class="gallery-slide <?= $index === 0 ? 'active' : '' ?>">
                        <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['caption'] ?? 'Obra Social') ?>">
                        <?php if (!empty($item['caption'])): ?>
                            <div class="slide-caption">
                                <p><?= htmlspecialchars($item['caption']) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <button class="slide-nav prev" onclick="changeSlide(-1)" aria-label="Anterior">&#10094;</button>
                <button class="slide-nav next" onclick="changeSlide(1)" aria-label="Siguiente">&#10095;</button>

                <div class="slide-dots">
                    <?php foreach ($gallery as $index => $item): ?>
                        <span class="dot <?= $index === 0 ? 'active' : '' ?>" onclick="setSlide(<?= $index ?>)"></span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
