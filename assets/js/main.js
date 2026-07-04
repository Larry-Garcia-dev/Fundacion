/**
 * main.js — Core functionality
 * - Lucide icons initialization
 * - Header background change on scroll
 * - Mobile nav toggle
 * - Active nav link highlighting on scroll (scroll spy)
 * - Scroll reveal animations (IntersectionObserver)
 */
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
            if (navMenu && navMenu.classList.contains('active')) {
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
});
