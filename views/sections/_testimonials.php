<?php if (!empty($settings['testimonials_enabled'])): ?>
<section id="testimonials" class="section testimonials-section">
    <div class="container">
        <div class="section-header text-center scroll-reveal">
            <div class="section-tag">Testimonios</div>
            <h2 class="section-title">Lo que dicen las personas que confían en nosotros</h2>
            <p class="section-desc text-muted">Historias reales de quienes han sido parte de nuestra labor social y han vivido de cerca nuestro compromiso.</p>
        </div>

        <div class="testimonials-grid" id="testimonialsGrid">
            <?php foreach ($testimonials as $t): ?>
                <div class="testimonial-card scroll-reveal">
                    <div class="testimonial-quote-icon"><i data-lucide="message-circle-quote"></i></div>
                    <p class="testimonial-text"><?= htmlspecialchars($t['message']) ?></p>
                    <div class="testimonial-author">
                        <?php if (!empty($t['photo_url'])): ?>
                            <img src="<?= htmlspecialchars($t['photo_url']) ?>" alt="<?= htmlspecialchars($t['name']) ?>" class="testimonial-avatar" loading="lazy">
                        <?php else: ?>
                            <div class="testimonial-avatar-default <?= htmlspecialchars($t['gender']) ?>">
                                <i data-lucide="<?= $t['gender'] === 'other' ? 'circle-user-round' : 'user-round' ?>"></i>
                            </div>
                        <?php endif; ?>
                        <div class="testimonial-author-info">
                            <span class="testimonial-name"><?= htmlspecialchars($t['name']) ?></span>
                            <span class="testimonial-date"><?= date('d/m/Y', strtotime($t['created_at'])) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($test_total > 6): ?>
            <div class="testimonials-load-more text-center" id="testimonialsLoadMore">
                <button class="btn btn-outline-primary btn-lg" id="btnLoadMoreTestimonials" data-page="1" data-total-pages="<?= ceil($test_total / 6) ?>">
                    <i data-lucide="chevrons-down"></i>
                    <span>Cargar más testimonios</span>
                    <div class="spinner" style="display:none;"></div>
                </button>
            </div>
        <?php endif; ?>

        <div class="testimonials-cta text-center scroll-reveal">
            <p class="testimonials-cta-text">¿Quieres compartir tu experiencia con nosotros?</p>
            <button class="btn btn-primary btn-lg" id="btnOpenTestimonialForm">
                <i data-lucide="message-square-plus"></i> Enviar mi testimonio
            </button>
        </div>
    </div>

    <!-- MODAL: Formulario de Testimonio -->
    <div class="testimonial-modal-overlay" id="testimonialModal" style="display:none;">
        <div class="testimonial-modal">
            <button class="testimonial-modal-close" id="btnCloseTestimonialForm" aria-label="Cerrar"><i data-lucide="x"></i></button>
            <h3 class="testimonial-modal-title">Comparte tu testimonio</h3>
            <p class="testimonial-modal-sub text-muted">Tu opinión nos importa y ayuda a otros a conocer nuestra labor.</p>
            <div id="testimonialAlert" class="alert-box" style="display:none;"></div>
            <form id="testimonialForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="test_name">Nombre completo *</label>
                    <div class="input-with-icon">
                        <i data-lucide="user"></i>
                        <input type="text" id="test_name" name="name" required placeholder="Tu nombre y apellido">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="test_email">Correo electrónico *</label>
                        <div class="input-with-icon">
                            <i data-lucide="mail"></i>
                            <input type="email" id="test_email" name="email" required placeholder="correo@ejemplo.com">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="test_phone">Celular</label>
                        <div class="input-with-icon">
                            <i data-lucide="phone"></i>
                            <input type="tel" id="test_phone" name="phone" placeholder="300 123 4567">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="test_gender">Género</label>
                    <select id="test_gender" name="gender">
                        <option value="female">Femenino</option>
                        <option value="male">Masculino</option>
                        <option value="other">Prefiero no decir</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="test_message">Tu testimonio *</label>
                    <textarea id="test_message" name="message" rows="4" required placeholder="Cuéntanos tu experiencia con la fundación..."></textarea>
                </div>
                <div class="form-group">
                    <label for="test_photo">Foto (opcional)</label>
                    <div class="photo-upload-area" id="photoUploadArea">
                        <i data-lucide="camera"></i>
                        <span>Haz clic o arrastra una foto aquí</span>
                        <input type="file" id="test_photo" name="photo" accept="image/jpeg,image/png,image/gif,image/webp" style="display:none;">
                    </div>
                    <div class="photo-preview" id="photoPreview" style="display:none;">
                        <img id="photoPreviewImg" src="" alt="Vista previa">
                        <button type="button" id="btnRemovePhoto" class="btn-remove-photo" aria-label="Quitar foto"><i data-lucide="x-circle"></i></button>
                    </div>
                    <span class="form-help">JPG, PNG, GIF o WEBP. Máximo 5MB.</span>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-loading-state" id="btnSubmitTestimonial">
                    <span>Enviar testimonio</span>
                    <div class="spinner" style="display:none;"></div>
                </button>
            </form>
        </div>
    </div>
</section>
<?php endif; ?>
