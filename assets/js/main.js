document.addEventListener('DOMContentLoaded', () => {
    // 1. Inicializar Iconos Lucide
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // 2. Efecto scroll en la cabecera (Header background change)
    const siteHeader = document.getElementById('siteHeader');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            siteHeader.classList.add('scrolled');
        } else {
            siteHeader.classList.remove('scrolled');
        }
    });

    // 3. Menú móvil (Toggle mobile menu)
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            
            // Cambiar icono de menú a cerrar y viceversa
            const icon = navToggle.querySelector('i');
            if (navMenu.classList.contains('active')) {
                icon.setAttribute('data-lucide', 'x');
            } else {
                icon.setAttribute('data-lucide', 'menu');
            }
            lucide.createIcons(); // Re-renderizar iconos
        });
    }

    // Cerrar menú móvil al hacer clic en un enlace
    const navLinks = document.querySelectorAll('.nav-menu a');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
                const icon = navToggle.querySelector('i');
                icon.setAttribute('data-lucide', 'menu');
                lucide.createIcons();
            }
        });
    });

    // 4. Scroll Spy (Activar enlace correspondiente en navegación al hacer scroll)
    const sections = document.querySelectorAll('section[id]');
    
    window.addEventListener('scroll', () => {
        const scrollY = window.pageYOffset;
        
        sections.forEach(current => {
            const sectionHeight = current.offsetHeight;
            const sectionTop = current.offsetTop - 140; // offset por cabecera
            const sectionId = current.getAttribute('id');
            
            if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                document.querySelector('.nav-menu a[href*=' + sectionId + ']')?.classList.add('active');
            } else {
                document.querySelector('.nav-menu a[href*=' + sectionId + ']')?.classList.remove('active');
            }
        });
    });

    // 5. Animación de revelación al hacer scroll (Scroll Reveal using IntersectionObserver)
    const revealElements = document.querySelectorAll('.scroll-reveal');
    
    if ('IntersectionObserver' in window) {
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target); // Dejar de observar una vez animado
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -50px 0px'
        });
        
        revealElements.forEach(el => revealObserver.observe(el));
    } else {
        // Fallback para navegadores antiguos
        revealElements.forEach(el => el.classList.add('revealed'));
    }

    // 6. Procesar Formulario de Contacto vía Fetch (AJAX)
    const contactForm = document.getElementById('contactForm');
    const contactAlert = document.getElementById('contactAlert');
    const btnSubmit = document.getElementById('btnSubmit');
    
    if (contactForm) {
        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault(); // Evitar recarga de página
            
            // UI States: Mostrar Spinner y desactivar botón
            const btnText = btnSubmit.querySelector('span');
            const spinner = btnSubmit.querySelector('.spinner');
            
            btnSubmit.disabled = true;
            if (spinner) spinner.style.display = 'inline-block';
            if (btnText) btnText.style.opacity = '0.5';
            
            // Limpiar alertas previas
            contactAlert.style.display = 'none';
            contactAlert.className = 'alert-box';
            contactAlert.innerHTML = '';
            
            const formData = new FormData(contactForm);
            
            try {
                const response = await fetch(contactForm.action, {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error('Respuesta del servidor no válida.');
                }
                
                const data = await response.json();
                
                if (data.success) {
                    // Éxito
                    contactAlert.classList.add('alert-box-success');
                    contactAlert.innerHTML = `<strong>¡Enviado!</strong> ${data.message}`;
                    contactAlert.style.display = 'block';
                    contactForm.reset(); // Limpiar campos del formulario
                } else {
                    // Errores de Validación o Base de Datos
                    contactAlert.classList.add('alert-box-error');
                    let errorHtml = `<strong>Error:</strong> ${data.message}`;
                    if (data.errors && data.errors.length > 0) {
                        errorHtml += '<ul>';
                        data.errors.forEach(err => {
                            errorHtml += `<li>${err}</li>`;
                        });
                        errorHtml += '</ul>';
                    }
                    contactAlert.innerHTML = errorHtml;
                    contactAlert.style.display = 'block';
                }
            } catch (err) {
                // Errores de Red o Conexión
                contactAlert.classList.add('alert-box-error');
                contactAlert.innerHTML = '<strong>Error de Conexión:</strong> No pudimos conectar con el servidor. Por favor, verifica tu conexión a internet e inténtalo de nuevo.';
                contactAlert.style.display = 'block';
                console.error(err);
            } finally {
                // UI States: Restaurar botón
                btnSubmit.disabled = false;
                if (spinner) spinner.style.display = 'none';
                if (btnText) btnText.style.opacity = '1';
                
                // Desplazarse suavemente a la alerta del formulario
                contactAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    }

    // --- LÓGICA DEL CARRUSEL DE OBRAS SOCIALES ---
    const carouselWrapper = document.getElementById('carouselWrapper');
    const carouselPrev = document.getElementById('carouselPrev');
    const carouselNext = document.getElementById('carouselNext');
    
    if (carouselWrapper && carouselPrev && carouselNext) {
        let workIndex = 0;
        const slidesCount = document.querySelectorAll('.carousel-slide-item').length;
        
        function updateCarousel() {
            // Desplazar el carrusel multiplicando el ancho del slide
            carouselWrapper.style.transform = `translateX(-${workIndex * 100}%)`;
            
            // Re-evaluar iconos de Lucide dentro del carrusel por si acaso
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
        
        carouselNext.addEventListener('click', () => {
            if (workIndex < slidesCount - 1) {
                workIndex++;
            } else {
                workIndex = 0; // Volver al inicio
            }
            updateCarousel();
        });
        
        carouselPrev.addEventListener('click', () => {
            if (workIndex > 0) {
                workIndex--;
            } else {
                workIndex = slidesCount - 1; // Ir al final
            }
            updateCarousel();
        });
    }
});

// --- LÓGICA DE LA GALERÍA DE FOTOS (SLIDESHOW GLOBAL) ---
let currentSlideIndex = 0;
let slideInterval;

function showSlide(index) {
    const slides = document.querySelectorAll('.gallery-slide');
    const dots = document.querySelectorAll('.slide-dots .dot');
    
    if (slides.length === 0) return;
    
    if (index >= slides.length) {
        currentSlideIndex = 0;
    } else if (index < 0) {
        currentSlideIndex = slides.length - 1;
    } else {
        currentSlideIndex = index;
    }
    
    slides.forEach((slide, i) => {
        slide.classList.remove('active');
        if (dots[i]) dots[i].classList.remove('active');
    });
    
    slides[currentSlideIndex].classList.add('active');
    if (dots[currentSlideIndex]) dots[currentSlideIndex].classList.add('active');
}

function changeSlide(direction) {
    showSlide(currentSlideIndex + direction);
    resetSlideTimer();
}

function setSlide(index) {
    showSlide(index);
    resetSlideTimer();
}

function startSlideTimer() {
    slideInterval = setInterval(() => {
        showSlide(currentSlideIndex + 1);
    }, 4500); // Cambiar cada 4.5 segundos
}

function resetSlideTimer() {
    clearInterval(slideInterval);
    startSlideTimer();
}

// Hacerlas globales para los eventos onclick de los botones HTML
window.changeSlide = changeSlide;
window.setSlide = setSlide;

// Iniciar carrusel al cargar
document.addEventListener('DOMContentLoaded', () => {
    showSlide(0);
    startSlideTimer();
});

// ==========================================================================
// TESTIMONIOS: Modal, Formulario, Carga de más, Preview de foto
// ==========================================================================
document.addEventListener('DOMContentLoaded', () => {

    // --- MODAL: Abrir / Cerrar ---
    const modal = document.getElementById('testimonialModal');
    const btnOpen = document.getElementById('btnOpenTestimonialForm');
    const btnClose = document.getElementById('btnCloseTestimonialForm');

    if (btnOpen && modal) {
        btnOpen.addEventListener('click', () => {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    }

    if (btnClose && modal) {
        btnClose.addEventListener('click', () => {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        });
    }

    // Cerrar modal al hacer clic fuera
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        });
    }

    // --- PHOTO UPLOAD: Preview y drag & drop ---
    const uploadArea = document.getElementById('photoUploadArea');
    const photoInput = document.getElementById('test_photo');
    const photoPreview = document.getElementById('photoPreview');
    const photoPreviewImg = document.getElementById('photoPreviewImg');
    const btnRemovePhoto = document.getElementById('btnRemovePhoto');

    if (uploadArea && photoInput) {
        uploadArea.addEventListener('click', () => photoInput.click());

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = 'var(--primary)';
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.style.borderColor = '';
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = '';
            if (e.dataTransfer.files.length) {
                photoInput.files = e.dataTransfer.files;
                showPhotoPreview(e.dataTransfer.files[0]);
            }
        });

        photoInput.addEventListener('change', () => {
            if (photoInput.files.length) showPhotoPreview(photoInput.files[0]);
        });
    }

    function showPhotoPreview(file) {
        if (!file || !file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreviewImg.src = e.target.result;
            photoPreview.style.display = 'inline-block';
            uploadArea.style.display = 'none';
            if (typeof lucide !== 'undefined') lucide.createIcons();
        };
        reader.readAsDataURL(file);
    }

    if (btnRemovePhoto && uploadArea && photoPreview && photoInput) {
        btnRemovePhoto.addEventListener('click', () => {
            photoInput.value = '';
            photoPreview.style.display = 'none';
            uploadArea.style.display = '';
            photoPreviewImg.src = '';
        });
    }

    // --- FORMULARIO: Envío AJAX ---
    const testForm = document.getElementById('testimonialForm');
    const testAlert = document.getElementById('testimonialAlert');
    const btnSubmitTest = document.getElementById('btnSubmitTestimonial');

    if (testForm) {
        testForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const btnText = btnSubmitTest.querySelector('span');
            const spinner = btnSubmitTest.querySelector('.spinner');

            btnSubmitTest.disabled = true;
            if (spinner) spinner.style.display = 'inline-block';
            if (btnText) btnText.style.opacity = '0.5';

            testAlert.style.display = 'none';
            testAlert.className = 'alert-box';

            const formData = new FormData(testForm);

            try {
                const res = await fetch('submit_testimonial.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await res.json();

                if (data.success) {
                    testAlert.classList.add('alert-box-success');
                    testAlert.innerHTML = '<strong>¡Enviado!</strong> ' + data.message;
                    testAlert.style.display = 'block';
                    testForm.reset();
                    // Reset photo preview
                    if (photoPreview) photoPreview.style.display = 'none';
                    if (uploadArea) uploadArea.style.display = '';
                    if (photoPreviewImg) photoPreviewImg.src = '';

                    // Cerrar modal tras 3 segundos
                    setTimeout(() => {
                        modal.style.display = 'none';
                        document.body.style.overflow = '';
                    }, 3000);
                } else {
                    testAlert.classList.add('alert-box-error');
                    let html = '<strong>Error:</strong> ' + data.message;
                    if (data.errors) {
                        html += '<ul>' + data.errors.map(e => '<li>' + e + '</li>').join('') + '</ul>';
                    }
                    testAlert.innerHTML = html;
                    testAlert.style.display = 'block';
                }
            } catch (err) {
                testAlert.classList.add('alert-box-error');
                testAlert.innerHTML = '<strong>Error de conexión:</strong> No se pudo enviar el testimonio.';
                testAlert.style.display = 'block';
            } finally {
                btnSubmitTest.disabled = false;
                if (spinner) spinner.style.display = 'none';
                if (btnText) btnText.style.opacity = '1';
            }
        });
    }

    // --- CARGAR MÁS testimonios (AJAX paginado) ---
    const btnLoadMore = document.getElementById('btnLoadMoreTestimonials');
    const grid = document.getElementById('testimonialsGrid');

    if (btnLoadMore && grid) {
        btnLoadMore.addEventListener('click', async () => {
            const page = parseInt(btnLoadMore.dataset.page) + 1;
            const totalPages = parseInt(btnLoadMore.dataset.totalPages);
            const spinner = btnLoadMore.querySelector('.spinner');
            const btnText = btnLoadMore.querySelector('span');
            const icon = btnLoadMore.querySelector('i');

            if (page > totalPages) return;

            btnLoadMore.disabled = true;
            if (spinner) spinner.style.display = 'inline-block';
            if (btnText) btnText.style.opacity = '0.5';
            if (icon) icon.style.display = 'none';

            try {
                const res = await fetch('get_testimonials.php?page=' + page);
                const data = await res.json();

                if (data.success && data.data.length) {
                    data.data.forEach(t => {
                        const card = document.createElement('div');
                        card.className = 'testimonial-card scroll-reveal revealed';

                        let avatarHtml = '';
                        if (t.photo_url) {
                            avatarHtml = '<img src="' + t.photo_url + '" alt="' + t.name + '" class="testimonial-avatar" loading="lazy">';
                        } else {
                            const genderClass = t.gender || 'other';
                            const iconName = genderClass === 'other' ? 'circle-user-round' : 'user-round';
                            avatarHtml = '<div class="testimonial-avatar-default ' + genderClass + '"><i data-lucide="' + iconName + '"></i></div>';
                        }

                        const date = new Date(t.created_at);
                        const dateStr = date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });

                        card.innerHTML =
                            '<div class="testimonial-quote-icon"><i data-lucide="message-circle-quote"></i></div>' +
                            '<p class="testimonial-text">' + t.message + '</p>' +
                            '<div class="testimonial-author">' +
                                avatarHtml +
                                '<div class="testimonial-author-info">' +
                                    '<span class="testimonial-name">' + t.name + '</span>' +
                                    '<span class="testimonial-date">' + dateStr + '</span>' +
                                '</div>' +
                            '</div>';

                        grid.appendChild(card);
                    });

                    btnLoadMore.dataset.page = page;

                    if (typeof lucide !== 'undefined') lucide.createIcons();

                    // Ocultar botón si ya no hay más páginas
                    if (page >= totalPages) {
                        document.getElementById('testimonialsLoadMore').style.display = 'none';
                    }
                }
            } catch (err) {
                console.error('Error cargando testimonios:', err);
            } finally {
                btnLoadMore.disabled = false;
                if (spinner) spinner.style.display = 'none';
                if (btnText) btnText.style.opacity = '1';
                if (icon) icon.style.display = '';
            }
        });
    }
});
