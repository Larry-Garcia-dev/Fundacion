<section class="commitment-section bg-gradient-dark text-white">
    <div class="container text-center scroll-reveal">
        <div class="section-tag text-teal"><?= htmlspecialchars($settings['commitment_title'] ?? 'Nuestro compromiso') ?></div>
        <h2 class="section-title text-white"><?= htmlspecialchars($settings['commitment_subtitle'] ?? 'Más que prestar un servicio, construimos bienestar.') ?></h2>
        <p class="commitment-p text-muted-light"><?= htmlspecialchars($settings['commitment_content'] ?? '') ?></p>
        <a href="#contact" class="btn btn-teal btn-lg mt-md">Solicitar propuesta</a>
    </div>
</section>
