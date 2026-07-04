/**
 * admin.js — Admin panel functionality
 * - Tab switching (switchTab function)
 * - Sidebar toggle for mobile
 * - Custom cursor trail effect
 * - File name display on file input change (updateFileName function)
 */

// Tab switching — called via onclick="switchTab(event, 'tab-id')"
function switchTab(evt, tabId) {
    const contents = document.querySelectorAll('.tab-content');
    contents.forEach(content => {
        content.style.display = 'none';
    });

    const buttons = document.querySelectorAll('.tab-btn');
    buttons.forEach(btn => {
        btn.classList.remove('active');
    });

    document.getElementById(tabId).style.display = 'block';
    evt.currentTarget.classList.add('active');
}

// File name display on file input change
function updateFileName(input, spanId) {
    const span = document.getElementById(spanId);
    if (input.files && input.files.length > 0) {
        span.textContent = "Seleccionado: " + input.files[0].name;
        span.style.color = "var(--secondary)";
    } else {
        span.textContent = "Ningún archivo seleccionado";
        span.style.color = "var(--text-muted)";
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Inicializar iconos de Lucide
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Toggle Sidebar en Móviles
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.admin-sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('mobile-open');
        });
    }

    // --- Cursor Trail Personalizado ---
    const cursor = document.getElementById('customCursor');
    const cursorDot = document.getElementById('customCursorDot');

    if (cursor && cursorDot && window.matchMedia('(pointer: fine)').matches) {
        let mouseX = 0, mouseY = 0;
        let cursorX = 0, cursorY = 0;

        document.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;

            // Posicionamiento instantáneo de la yema central
            cursorDot.style.left = mouseX + 'px';
            cursorDot.style.top = mouseY + 'px';
        });

        function animateCursor() {
            // LERP (Linear Interpolation) para el aro externo
            let dx = mouseX - cursorX;
            let dy = mouseY - cursorY;
            cursorX += dx * 0.15;
            cursorY += dy * 0.15;

            cursor.style.left = cursorX + 'px';
            cursor.style.top = cursorY + 'px';

            requestAnimationFrame(animateCursor);
        }
        animateCursor();

        // Efecto hover expandido sobre enlaces e inputs
        const hoverables = document.querySelectorAll('a, button, input, select, textarea, .btn-icon, .stat-card, tr');
        hoverables.forEach(el => {
            el.addEventListener('mouseenter', () => {
                cursor.classList.add('cursor-hover');
                cursorDot.classList.add('cursor-dot-hover');
            });
            el.addEventListener('mouseleave', () => {
                cursor.classList.remove('cursor-hover');
                cursorDot.classList.remove('cursor-dot-hover');
            });
        });
    } else {
        if (cursor) cursor.style.display = 'none';
        if (cursorDot) cursorDot.style.display = 'none';
    }
});
