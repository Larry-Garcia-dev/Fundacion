/**
 * carousel.js — Charity works carousel
 * - Prev/next button handlers
 * - Auto-advance with wrap-around
 * - Transform-based slide movement
 * - Lucide icon re-render after slide change
 */
document.addEventListener('DOMContentLoaded', () => {
    const carouselWrapper = document.getElementById('carouselWrapper');
    const carouselPrev = document.getElementById('carouselPrev');
    const carouselNext = document.getElementById('carouselNext');

    if (!carouselWrapper || !carouselPrev || !carouselNext) return;

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
});
