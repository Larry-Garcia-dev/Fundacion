/**
 * gallery.js — Gallery slideshow
 * - showSlide() — display slide by index with wrap-around
 * - changeSlide() — advance/retreat by direction
 * - setSlide() — jump to specific slide
 * - Auto-advance timer (startSlideTimer / resetSlideTimer)
 * - Dot indicator updates
 */
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
