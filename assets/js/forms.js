/**
 * forms.js — Contact form AJAX
 * - Contact form submit handler
 * - Form validation display
 * - AJAX POST to submit_contact.php
 * - Success/error alert display
 * - Form reset after success
 * - Loading state for submit button
 */
document.addEventListener('DOMContentLoaded', () => {
    const contactForm = document.getElementById('contactForm');
    const contactAlert = document.getElementById('contactAlert');
    const btnSubmit = document.getElementById('btnSubmit');

    if (!contactForm) return;

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
});
