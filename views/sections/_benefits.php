<section id="benefits" class="section benefits-section">
    <div class="container">
        <div class="section-header text-center scroll-reveal">
            <div class="section-tag">Diferenciales</div>
            <h2 class="section-title">¿Por qué elegirnos?</h2>
            <p class="section-desc text-muted">Nos diferenciamos por el rigor, el compromiso social y la excelencia operativa en cada proceso.</p>
        </div>
        <?php if (empty($benefits)): ?>
            <div class="text-center text-muted">Información en actualización.</div>
        <?php else: ?>
            <div class="grid grid-4 gap-md">
                <?php foreach ($benefits as $ben): ?>
                    <div class="benefit-block scroll-reveal">
                        <div class="benefit-icon-box">
                            <i data-lucide="<?= htmlspecialchars($ben['icon']) ?>"></i>
                        </div>
                        <h3 class="benefit-title"><?= htmlspecialchars($ben['title']) ?></h3>
                        <p class="benefit-desc text-muted"><?= htmlspecialchars($ben['description']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
