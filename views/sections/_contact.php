<section id="contact" class="section contact-section">
    <div class="container">
        <div class="grid grid-2 gap-xl">
            <!-- Texto CTA -->
            <div class="contact-info scroll-reveal">
                <div class="section-tag">Contacto</div>
                <h2 class="section-title"><?= htmlspecialchars($settings['together_title'] ?? '¿Trabajemos juntos?') ?></h2>
                <p class="contact-desc text-muted"><?= htmlspecialchars($settings['together_content'] ?? 'Estamos preparados para desarrollar soluciones integrales que aporten valor a instituciones públicas y privadas.') ?></p>

                <div class="contact-methods">
                    <div class="method-item">
                        <div class="method-icon"><i data-lucide="phone"></i></div>
                        <div class="method-details">
                            <span>Llámanos</span>
                            <a href="tel:<?= htmlspecialchars($settings['footer_phone'] ?? '') ?>"><?= htmlspecialchars($settings['footer_phone'] ?? '') ?></a>
                        </div>
                    </div>
                    <div class="method-item">
                        <div class="method-icon"><i data-lucide="mail"></i></div>
                        <div class="method-details">
                            <span>Escríbenos</span>
                            <a href="mailto:<?= htmlspecialchars($settings['footer_email'] ?? '') ?>"><?= htmlspecialchars($settings['footer_email'] ?? '') ?></a>
                        </div>
                    </div>
                    <div class="method-item">
                        <div class="method-icon"><i data-lucide="map-pin"></i></div>
                        <div class="method-details">
                            <span>Ubicación</span>
                            <p><?= htmlspecialchars($settings['footer_address'] ?? 'Ibagué – Tolima') ?></p>
                        </div>
                    </div>
                </div>

                <div class="contact-logo">
                    <img src="<?= htmlspecialchars($settings['contact_logo_path'] ?? 'logo1.png') ?>" alt="Fundación Visión de Futuro" class="contact-logo-img">
                </div>
            </div>

            <!-- Formulario de Contacto -->
            <div class="contact-form-container scroll-reveal">
                <h3 class="form-title">Enviar una propuesta / Solicitud</h3>
                <p class="form-subtitle text-muted">Completa el formulario y te responderemos en menos de 24 horas hábiles.</p>
                <div id="contactAlert" class="alert-box" style="display: none;"></div>
                <form id="contactForm" action="api/contact.php" method="POST">
                    <div class="form-group">
                        <label for="name">Nombre Completo *</label>
                        <div class="input-with-icon">
                            <i data-lucide="user"></i>
                            <input type="text" id="name" name="name" required placeholder="Tu nombre y apellido">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico *</label>
                        <div class="input-with-icon">
                            <i data-lucide="mail"></i>
                            <input type="email" id="email" name="email" required placeholder="correo@ejemplo.com">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phone">Teléfono (Opcional)</label>
                        <div class="input-with-icon">
                            <i data-lucide="phone"></i>
                            <input type="tel" id="phone" name="phone" placeholder="Ej. 300 123 4567">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="message">Mensaje / Detalle de tu requerimiento *</label>
                        <textarea id="message" name="message" rows="4" required placeholder="¿Cómo podemos ayudarte? Describe los servicios o proyecto social de tu interés..."></textarea>
                    </div>
                    <button type="submit" id="btnSubmit" class="btn btn-primary btn-block btn-loading-state">
                        <span><?= htmlspecialchars($settings['together_btn'] ?? 'Solicitar una propuesta') ?></span>
                        <div class="spinner" style="display: none;"></div>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
