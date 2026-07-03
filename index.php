<?php
// Cargar conexión a la base de datos (redirigirá al setup si no está configurado)
require_once __DIR__ . '/db.php';

// Obtener todas las configuraciones/textos
$settings = [];
try {
    $stmt = $pdo->query("SELECT * FROM `settings`");
    while ($row = $stmt->fetch()) {
        $settings[$row['key_name']] = $row['value_text'];
    }
    
    // Cargar Servicios
    $services_stmt = $pdo->query("SELECT * FROM `services` ORDER BY `display_order` ASC, `id` ASC");
    $services = $services_stmt->fetchAll();
    
    // Cargar Beneficios (¿Por qué elegirnos?)
    $benefits_stmt = $pdo->query("SELECT * FROM `benefits` ORDER BY `display_order` ASC, `id` ASC");
    $benefits = $benefits_stmt->fetchAll();
    
    // Cargar Valores
    $values_stmt = $pdo->query("SELECT * FROM `values` ORDER BY `display_order` ASC, `id` ASC");
    $values = $values_stmt->fetchAll();
} catch (PDOException $e) {
    // Si falla la consulta, mostrar mensaje seguro
    die("Error al cargar la página. Por favor, asegúrese de completar la instalación en <a href='admin/setup.php'>admin/setup.php</a>");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- SEO & Metadata -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="logos/VisiónLogo_Morado.png">
    <title><?php echo htmlspecialchars($settings['hero_title'] ?? 'Fundación Visión de Futuro - Servicios Integrales'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars(substr($settings['hero_subtitle'] ?? '', 0, 160)); ?>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Custom Theme Colors and Backgrounds -->
    <style>
        :root {
            --primary: <?php echo htmlspecialchars($settings['theme_color_primary'] ?? '#9b98d5'); ?>;
            --teal: <?php echo htmlspecialchars($settings['theme_color_secondary'] ?? '#86d2f1'); ?>;
            --dark: <?php echo htmlspecialchars($settings['theme_color_dark'] ?? '#1e293b'); ?>;
        }
        
        .logo-img {
            height: <?php echo htmlspecialchars($settings['logo_size'] ?? '52'); ?>px !important;
            transform: translate(
                <?php echo htmlspecialchars($settings['logo_offset_x'] ?? '0'); ?>px,
                <?php echo htmlspecialchars($settings['logo_offset_y'] ?? '0'); ?>px
            ) !important;
        }
        
        .hero-section {
            background-image: url('<?php echo htmlspecialchars($settings['hero_bg_image'] ?? "https://images.unsplash.com/photo-1593113598332-cd288d649433?auto=format&fit=crop&w=1920&q=80"); ?>') !important;
        }
    </style>
    
    <!-- Lucide Icons (Premium outline icons) -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

    <!-- Header / Barra de Navegación -->
    <header class="site-header" id="siteHeader">
        <div class="header-container">
            <a href="#hero" class="logo">
                <img src="<?php echo htmlspecialchars($settings['logo_path'] ?? 'logo1.png'); ?>" alt="Logo" class="logo-img">
            </a>
            
            <!-- Botón del menú móvil -->
            <button class="nav-toggle" id="navToggle" aria-label="Abrir Menú">
                <i data-lucide="menu"></i>
            </button>
            
            <nav class="nav-menu" id="navMenu">
                <ul>
                    <li><a href="#hero" class="nav-link active">Inicio</a></li>
                    <li><a href="#about" class="nav-link">¿Quiénes Somos?</a></li>
                    <li><a href="#services" class="nav-link">Servicios</a></li>
                    <li><a href="#benefits" class="nav-link">¿Por qué elegirnos?</a></li>
                    <li><a href="#mision-vision" class="nav-link">Misión y Visión</a></li>
                    <li><a href="#values" class="nav-link">Valores</a></li>
                    <li><a href="#gallery" class="nav-link">Galería</a></li>
                    <li><a href="#charity-works" class="nav-link">Obras Sociales</a></li>
                    <?php if (!empty($settings['testimonials_enabled'])): ?>
                    <li><a href="#testimonials" class="nav-link">Testimonios</a></li>
                    <?php endif; ?>
                    <li><a href="#contact" class="btn btn-primary-nav">Contacto</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <!-- SECCIÓN 1: HERO -->
        <section id="hero" class="hero-section">
            <div class="hero-bg-overlay"></div>
            <div class="container hero-container">
                <div class="hero-content">
                    <span class="hero-badge animate-fade-in">Salud & Desarrollo Social</span>
                    <h1 class="animate-fade-in-up"><?php echo htmlspecialchars($settings['hero_title'] ?? 'Comprometidos con el bienestar, la calidad y el desarrollo social.'); ?></h1>
                    <p class="hero-lead animate-fade-in-up delay-1"><?php echo htmlspecialchars($settings['hero_subtitle'] ?? ''); ?></p>
                    <div class="hero-actions animate-fade-in-up delay-2">
                        <a href="#contact" class="btn btn-primary btn-lg"><?php echo htmlspecialchars($settings['hero_btn_primary'] ?? 'Solicitar información'); ?></a>
                        <a href="#services" class="btn btn-outline-light btn-lg"><?php echo htmlspecialchars($settings['hero_btn_secondary'] ?? 'Conoce nuestros servicios'); ?></a>
                    </div>
                </div>
                <div class="hero-logo animate-fade-in-up delay-1">
                    <img src="<?php echo htmlspecialchars($settings['hero_logo_path'] ?? 'logo1.png'); ?>" alt="Fundación Visión de Futuro" class="hero-logo-img">
                </div>
            </div>
            <div class="hero-scroll-indicator">
                <a href="#about" aria-label="Bajar a la sección nosotros">
                    <i data-lucide="chevron-down"></i>
                </a>
            </div>
        </section>

        <!-- SECCIÓN 2: ¿QUIÉNES SOMOS? -->
        <section id="about" class="section about-section">
            <div class="container">
                <div class="grid grid-2">
                    <div class="about-text scroll-reveal">
                        <div class="section-tag"><?php echo htmlspecialchars($settings['about_title'] ?? '¿Quiénes somos?'); ?></div>
                        <h2 class="section-title"><?php echo htmlspecialchars($settings['about_subtitle'] ?? 'Construimos bienestar a través de servicios integrales.'); ?></h2>
                        <p class="about-p main-p"><?php echo htmlspecialchars($settings['about_content_1'] ?? ''); ?></p>
                        <p class="about-p text-muted"><?php echo htmlspecialchars($settings['about_content_2'] ?? ''); ?></p>
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
                        <!-- Imagen premium representativa -->
                        <div class="image-box">
                            <img src="<?php echo htmlspecialchars($settings['about_img_url'] ?? 'https://images.unsplash.com/photo-1593113598332-cd288d649433?auto=format&fit=crop&w=800&q=80'); ?>" alt="Compromiso con la alimentación y nutrición" class="about-img">
                            <div class="experience-badge">
                                <i data-lucide="heart-handshake"></i>
                                <span>Vocación Social</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECCIÓN 3: NUESTROS SERVICIOS -->
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
                                    <i data-lucide="<?php echo htmlspecialchars($srv['icon']); ?>"></i>
                                </div>
                                <h3 class="card-title"><?php echo htmlspecialchars($srv['title']); ?></h3>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($srv['description']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- SECCIÓN 4: ¿POR QUÉ ELEGIRNOS? -->
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
                                    <i data-lucide="<?php echo htmlspecialchars($ben['icon']); ?>"></i>
                                </div>
                                <h3 class="benefit-title"><?php echo htmlspecialchars($ben['title']); ?></h3>
                                <p class="benefit-desc text-muted"><?php echo htmlspecialchars($ben['description']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- SECCIÓN 5: NUESTRO COMPROMISO (BANNER) -->
        <section class="commitment-section bg-gradient-dark text-white">
            <div class="container text-center scroll-reveal">
                <div class="section-tag text-teal"><?php echo htmlspecialchars($settings['commitment_title'] ?? 'Nuestro compromiso'); ?></div>
                <h2 class="section-title text-white"><?php echo htmlspecialchars($settings['commitment_subtitle'] ?? 'Más que prestar un servicio, construimos bienestar.'); ?></h2>
                <p class="commitment-p text-muted-light"><?php echo htmlspecialchars($settings['commitment_content'] ?? ''); ?></p>
                <a href="#contact" class="btn btn-teal btn-lg mt-md">Solicitar propuesta</a>
            </div>
        </section>

        <!-- SECCIÓN 6 & 7: MISIÓN Y VISIÓN -->
        <section id="mision-vision" class="section mission-vision-section">
            <div class="container">
                <div class="grid grid-2 gap-lg">
                    <!-- Misión -->
                    <div class="mv-card scroll-reveal">
                        <div class="mv-icon-box">
                            <i data-lucide="compass"></i>
                        </div>
                        <h2 class="mv-title"><?php echo htmlspecialchars($settings['mission_title'] ?? 'Nuestra Misión'); ?></h2>
                        <p class="mv-text text-muted"><?php echo htmlspecialchars($settings['mission_content'] ?? ''); ?></p>
                    </div>
                    
                    <!-- Visión -->
                    <div class="mv-card scroll-reveal">
                        <div class="mv-icon-box vision">
                            <i data-lucide="eye"></i>
                        </div>
                        <h2 class="mv-title"><?php echo htmlspecialchars($settings['vision_title'] ?? 'Nuestra Visión'); ?></h2>
                        <p class="mv-text text-muted"><?php echo htmlspecialchars($settings['vision_content'] ?? ''); ?></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECCIÓN 8: NUESTROS VALORES -->
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
                                <div class="value-check">
                                    <i data-lucide="check-circle-2"></i>
                                </div>
                                <h3 class="value-title"><?php echo htmlspecialchars($val['title']); ?></h3>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- SECCIÓN: GALERÍA DE FOTOS -->
        <section id="gallery" class="section gallery-section">
            <div class="container">
                <div class="section-header text-center scroll-reveal">
                    <div class="section-tag">Galería</div>
                    <h2 class="section-title">Nuestra Labor en Imágenes</h2>
                    <p class="section-desc text-muted">Fotografías reales de nuestras actividades, proyectos y las sonrisas de los niños que apoyamos.</p>
                </div>
                
                <?php
                // Cargar fotos de la galería
                try {
                    $gal_stmt = $pdo->query("SELECT * FROM `gallery` ORDER BY `display_order` ASC, `id` DESC");
                    $gallery = $gal_stmt->fetchAll();
                } catch (Exception $e) {
                    $gallery = [];
                }
                ?>

                <?php if (empty($gallery)): ?>
                    <div class="text-center text-muted">Galería en construcción.</div>
                <?php else: ?>
                    <!-- Slideshow Automático de Galería -->
                    <div class="gallery-slideshow-container scroll-reveal">
                        <?php foreach ($gallery as $index => $item): ?>
                            <div class="gallery-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['caption'] ?? 'Obra Social'); ?>">
                                <?php if (!empty($item['caption'])): ?>
                                    <div class="slide-caption">
                                        <p><?php echo htmlspecialchars($item['caption']); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        
                        <!-- Controles del Slider -->
                        <button class="slide-nav prev" onclick="changeSlide(-1)" aria-label="Anterior">&#10094;</button>
                        <button class="slide-nav next" onclick="changeSlide(1)" aria-label="Siguiente">&#10095;</button>
                        
                        <!-- Indicadores de punto -->
                        <div class="slide-dots">
                            <?php foreach ($gallery as $index => $item): ?>
                                <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>" onclick="setSlide(<?php echo $index; ?>)"></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- SECCIÓN: OBRAS DE CARIDAD -->
        <section id="charity-works" class="section charity-section bg-light">
            <div class="container">
                <div class="section-header text-center scroll-reveal">
                    <div class="section-tag">Obras Sociales</div>
                    <h2 class="section-title">Obras de Caridad y Proyectos Realizados</h2>
                    <p class="section-desc text-muted">Conoce el impacto positivo y la ayuda social que brindamos diariamente a las comunidades vulnerables.</p>
                </div>
                
                <?php
                // Cargar obras de caridad
                try {
                    $works_stmt = $pdo->query("SELECT * FROM `charity_works` ORDER BY `display_order` ASC, `id` DESC");
                    $works = $works_stmt->fetchAll();
                } catch (Exception $e) {
                    $works = [];
                }
                ?>

                <?php if (empty($works)): ?>
                    <div class="text-center text-muted">Próximamente más proyectos sociales.</div>
                <?php else: ?>
                    <!-- Blog Carousel de Proyectos -->
                    <div class="carousel-container scroll-reveal">
                        <div class="carousel-wrapper" id="carouselWrapper">
                            <?php foreach ($works as $index => $work): ?>
                                <div class="carousel-slide-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <div class="work-card">
                                        <div class="work-img-box">
                                            <img src="<?php echo htmlspecialchars($work['image_url']); ?>" alt="<?php echo htmlspecialchars($work['title']); ?>">
                                        </div>
                                        <div class="work-content">
                                            <div class="work-date"><i data-lucide="calendar"></i> <?php echo date('d/m/Y', strtotime($work['created_at'])); ?></div>
                                            <h3 class="work-card-title"><?php echo htmlspecialchars($work['title']); ?></h3>
                                            <p class="work-card-desc"><?php echo htmlspecialchars($work['description']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Controles de Navegación del carrusel de obras -->
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

        <!-- SECCIÓN: TESTIMONIOS -->
        <?php if (!empty($settings['testimonials_enabled'])): ?>
        <section id="testimonials" class="section testimonials-section">
            <div class="container">
                <div class="section-header text-center scroll-reveal">
                    <div class="section-tag">Testimonios</div>
                    <h2 class="section-title">Lo que dicen las personas que confían en nosotros</h2>
                    <p class="section-desc text-muted">Historias reales de quienes han sido parte de nuestra labor social y han vivido de cerca nuestro compromiso.</p>
                </div>

                <!-- Grid de Testimonios (carga inicial vía PHP, más vía AJAX) -->
                <div class="testimonials-grid" id="testimonialsGrid">
                    <?php
                    try {
                        $test_stmt = $pdo->prepare("SELECT name, gender, message, photo_url, created_at FROM `testimonials` WHERE `status` = 'approved' ORDER BY `created_at` DESC LIMIT 6");
                        $test_stmt->execute();
                        $testimonials = $test_stmt->fetchAll();
                        $test_total = $pdo->query("SELECT COUNT(*) FROM `testimonials` WHERE `status` = 'approved'")->fetchColumn();
                    } catch (Exception $e) {
                        $testimonials = [];
                        $test_total = 0;
                    }
                    ?>
                    <?php foreach ($testimonials as $t): ?>
                        <div class="testimonial-card scroll-reveal">
                            <div class="testimonial-quote-icon">
                                <i data-lucide="message-circle-quote"></i>
                            </div>
                            <p class="testimonial-text"><?php echo htmlspecialchars($t['message']); ?></p>
                            <div class="testimonial-author">
                                <?php if (!empty($t['photo_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($t['photo_url']); ?>" alt="<?php echo htmlspecialchars($t['name']); ?>" class="testimonial-avatar" loading="lazy">
                                <?php else: ?>
                                    <div class="testimonial-avatar-default <?php echo htmlspecialchars($t['gender']); ?>">
                                        <?php if ($t['gender'] === 'female'): ?>
                                            <i data-lucide="user-round"></i>
                                        <?php elseif ($t['gender'] === 'male'): ?>
                                            <i data-lucide="user-round"></i>
                                        <?php else: ?>
                                            <i data-lucide="circle-user-round"></i>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="testimonial-author-info">
                                    <span class="testimonial-name"><?php echo htmlspecialchars($t['name']); ?></span>
                                    <span class="testimonial-date"><?php echo date('d/m/Y', strtotime($t['created_at'])); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Botón Cargar Más -->
                <?php if ($test_total > 6): ?>
                    <div class="testimonials-load-more text-center" id="testimonialsLoadMore">
                        <button class="btn btn-outline-primary btn-lg" id="btnLoadMoreTestimonials" data-page="1" data-total-pages="<?php echo ceil($test_total / 6); ?>">
                            <i data-lucide="chevrons-down"></i>
                            <span>Cargar más testimonios</span>
                            <div class="spinner" style="display:none;"></div>
                        </button>
                    </div>
                <?php endif; ?>

                <!-- Botón para enviar testimonio -->
                <div class="testimonials-cta text-center scroll-reveal">
                    <p class="testimonials-cta-text">¿Quieres compartir tu experiencia con nosotros?</p>
                    <button class="btn btn-primary btn-lg" id="btnOpenTestimonialForm">
                        <i data-lucide="message-square-plus"></i>
                        Enviar mi testimonio
                    </button>
                </div>
            </div>

            <!-- MODAL: Formulario de Testimonio -->
            <div class="testimonial-modal-overlay" id="testimonialModal" style="display:none;">
                <div class="testimonial-modal">
                    <button class="testimonial-modal-close" id="btnCloseTestimonialForm" aria-label="Cerrar">
                        <i data-lucide="x"></i>
                    </button>
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
                                <button type="button" id="btnRemovePhoto" class="btn-remove-photo" aria-label="Quitar foto">
                                    <i data-lucide="x-circle"></i>
                                </button>
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

        <!-- SECCIÓN 9: ¿TRABAJEMOS JUNTOS? & FORMULARIO -->
        <section id="contact" class="section contact-section">
            <div class="container">
                <div class="grid grid-2 gap-xl">
                    <!-- Texto CTA -->
                    <div class="contact-info scroll-reveal">
                        <div class="section-tag">Contacto</div>
                        <h2 class="section-title"><?php echo htmlspecialchars($settings['together_title'] ?? '¿Trabajemos juntos?'); ?></h2>
                        <p class="contact-desc text-muted"><?php echo htmlspecialchars($settings['together_content'] ?? 'Estamos preparados para desarrollar soluciones integrales que aporten valor a instituciones públicas y privadas.'); ?></p>
                        
                        <div class="contact-methods">
                            <div class="method-item">
                                <div class="method-icon">
                                    <i data-lucide="phone"></i>
                                </div>
                                <div class="method-details">
                                    <span>Llámanos</span>
                                    <a href="tel:<?php echo htmlspecialchars($settings['footer_phone'] ?? SITE_PHONE); ?>"><?php echo htmlspecialchars($settings['footer_phone'] ?? SITE_PHONE); ?></a>
                                </div>
                            </div>
                            <div class="method-item">
                                <div class="method-icon">
                                    <i data-lucide="mail"></i>
                                </div>
                                <div class="method-details">
                                    <span>Escríbenos</span>
                                    <a href="mailto:<?php echo htmlspecialchars($settings['footer_email'] ?? SITE_EMAIL); ?>"><?php echo htmlspecialchars($settings['footer_email'] ?? SITE_EMAIL); ?></a>
                                </div>
                            </div>
                            <div class="method-item">
                                <div class="method-icon">
                                    <i data-lucide="map-pin"></i>
                                </div>
                                <div class="method-details">
                                    <span>Ubicación</span>
                                    <p><?php echo htmlspecialchars($settings['footer_address'] ?? 'Ibagué – Tolima'); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="contact-logo">
                            <img src="<?php echo htmlspecialchars($settings['contact_logo_path'] ?? 'logo1.png'); ?>" alt="Fundación Visión de Futuro" class="contact-logo-img">
                        </div>
                    </div>
                    
                    <!-- Formulario de Contacto -->
                    <div class="contact-form-container scroll-reveal">
                        <h3 class="form-title">Enviar una propuesta / Solicitud</h3>
                        <p class="form-subtitle text-muted">Completa el formulario y te responderemos en menos de 24 horas hábiles.</p>
                        
                        <!-- Caja de Alertas -->
                        <div id="contactAlert" class="alert-box" style="display: none;"></div>
                        
                        <form id="contactForm" action="submit_contact.php" method="POST">
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
                                <span><?php echo htmlspecialchars($settings['together_btn'] ?? 'Solicitar una propuesta'); ?></span>
                                <div class="spinner" style="display: none;"></div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="site-footer">
        <div class="footer-top">
            <div class="container footer-grid">
                <div class="footer-brand">
                    <div class="footer-logo-container">
                        <img src="<?php echo htmlspecialchars($settings['footer_logo_path'] ?? $settings['logo_path'] ?? 'logo1.png'); ?>" alt="Logo" class="footer-logo-img">
                    </div>
                    <p class="text-muted-light mt-sm">Servicios Integrales comprometidos con la calidad de vida, alimentación inocua y proyectos de desarrollo social en el país.</p>
                </div>
                
                <div class="footer-links-col">
                    <h4>Enlaces rápidos</h4>
                    <ul>
                        <li><a href="#hero">Inicio</a></li>
                        <li><a href="#about">¿Quiénes Somos?</a></li>
                        <li><a href="#services">Servicios</a></li>
                        <li><a href="#gallery">Galería de Fotos</a></li>
                        <li><a href="#charity-works">Obras Sociales</a></li>
                        <?php if (!empty($settings['testimonials_enabled'])): ?>
                        <li><a href="#testimonials">Testimonios</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="footer-links-col">
                    <h4>Contacto</h4>
                    <p class="footer-contact-p"><i data-lucide="map-pin"></i> <?php echo htmlspecialchars($settings['footer_address'] ?? 'Ibagué – Tolima'); ?></p>
                    <p class="footer-contact-p"><i data-lucide="phone"></i> <?php echo htmlspecialchars($settings['footer_phone'] ?? SITE_PHONE); ?></p>
                    <p class="footer-contact-p"><i data-lucide="mail"></i> <a href="mailto:<?php echo htmlspecialchars($settings['footer_email'] ?? SITE_EMAIL); ?>"><?php echo htmlspecialchars($settings['footer_email'] ?? SITE_EMAIL); ?></a></p>
                </div>
                
                <div class="footer-links-col">
                    <h4>Redes Sociales</h4>
                    <div class="social-icons">
                        <?php if (!empty($settings['footer_facebook'])): ?>
                            <a href="<?php echo htmlspecialchars($settings['footer_facebook']); ?>" target="_blank" aria-label="Facebook"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['footer_instagram'])): ?>
                            <a href="<?php echo htmlspecialchars($settings['footer_instagram']); ?>" target="_blank" aria-label="Instagram"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['footer_twitter'])): ?>
                            <a href="<?php echo htmlspecialchars($settings['footer_twitter']); ?>" target="_blank" aria-label="Twitter"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['footer_linkedin'])): ?>
                            <a href="<?php echo htmlspecialchars($settings['footer_linkedin']); ?>" target="_blank" aria-label="LinkedIn"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="container footer-bottom-container">
                <p>&copy; <?php echo date('Y'); ?> Fundación Visión de Futuro - Servicios Integrales. Todos los derechos reservados.</p>
                <a href="admin/login.php" class="admin-link-footer"><i data-lucide="shield-alert"></i> Administrador</a>
            </div>
        </div>
    </footer>

    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
</body>
</html>
