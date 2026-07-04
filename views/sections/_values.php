<section id="values" class="section values-section bg-light">
    <div class="container">
        <div class="section-header text-center scroll-reveal">
            <div class="section-tag">Pilares</div>
            <h2 class="section-title">Nuestros Valores</h2>
            <p class="section-desc text-muted">Los principios éticos que guían nuestras acciones diarias y definen nuestra identidad corporativa.</p>
        </div>
        <?php if (empty($values)): ?>
            <div class="text-center text-muted">Cargando valores...</div>
        <?php else: ?>
            <div class="grid grid-4 gap-md">
                <?php foreach ($values as $val): ?>
                    <div class="value-card scroll-reveal">
                        <div class="value-check"><i data-lucide="check-circle-2"></i></div>
                        <h3 class="value-title"><?= htmlspecialchars($val['title']) ?></h3>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
