/* testimonials.js — Modal, photo upload, AJAX submit, load more */
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('testimonialModal');
    const btnOpen = document.getElementById('btnOpenTestimonialForm');
    const btnClose = document.getElementById('btnCloseTestimonialForm');
    const uploadArea = document.getElementById('photoUploadArea');
    const photoInput = document.getElementById('test_photo');
    const photoPreview = document.getElementById('photoPreview');
    const photoPreviewImg = document.getElementById('photoPreviewImg');
    const btnRemovePhoto = document.getElementById('btnRemovePhoto');

    // --- MODAL: Abrir / Cerrar ---
    if (btnOpen && modal) {
        btnOpen.addEventListener('click', () => {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    }
    if (btnClose && modal) btnClose.addEventListener('click', () => { modal.style.display = 'none'; document.body.style.overflow = ''; });
    if (modal) modal.addEventListener('click', (e) => { if (e.target === modal) { modal.style.display = 'none'; document.body.style.overflow = ''; } });

    // --- PHOTO UPLOAD: Preview y drag & drop ---
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

    if (uploadArea && photoInput) {
        uploadArea.addEventListener('click', () => photoInput.click());
        uploadArea.addEventListener('dragover', (e) => { e.preventDefault(); uploadArea.style.borderColor = 'var(--primary)'; });
        uploadArea.addEventListener('dragleave', () => { uploadArea.style.borderColor = ''; });
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault(); uploadArea.style.borderColor = '';
            if (e.dataTransfer.files.length) { photoInput.files = e.dataTransfer.files; showPhotoPreview(e.dataTransfer.files[0]); }
        });
        photoInput.addEventListener('change', () => { if (photoInput.files.length) showPhotoPreview(photoInput.files[0]); });
    }
    if (btnRemovePhoto && uploadArea && photoPreview && photoInput) {
        btnRemovePhoto.addEventListener('click', () => {
            photoInput.value = ''; photoPreview.style.display = 'none';
            uploadArea.style.display = ''; photoPreviewImg.src = '';
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
            testAlert.style.display = 'none'; testAlert.className = 'alert-box';
            const formData = new FormData(testForm);
            try {
                const res = await fetch('submit_testimonial.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    testAlert.classList.add('alert-box-success');
                    testAlert.innerHTML = '<strong>¡Enviado!</strong> ' + data.message;
                    testAlert.style.display = 'block'; testForm.reset();
                    if (photoPreview) photoPreview.style.display = 'none';
                    if (uploadArea) uploadArea.style.display = '';
                    if (photoPreviewImg) photoPreviewImg.src = '';
                    setTimeout(() => { modal.style.display = 'none'; document.body.style.overflow = ''; }, 3000);
                } else {
                    testAlert.classList.add('alert-box-error');
                    let html = '<strong>Error:</strong> ' + data.message;
                    if (data.errors) html += '<ul>' + data.errors.map(e => '<li>' + e + '</li>').join('') + '</ul>';
                    testAlert.innerHTML = html; testAlert.style.display = 'block';
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
                            const gc = t.gender || 'other';
                            avatarHtml = '<div class="testimonial-avatar-default ' + gc + '"><i data-lucide="' + (gc === 'other' ? 'circle-user-round' : 'user-round') + '"></i></div>';
                        }
                        const dateStr = new Date(t.created_at).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
                        card.innerHTML = '<div class="testimonial-quote-icon"><i data-lucide="message-circle-quote"></i></div>' +
                            '<p class="testimonial-text">' + t.message + '</p>' +
                            '<div class="testimonial-author">' + avatarHtml +
                            '<div class="testimonial-author-info"><span class="testimonial-name">' + t.name + '</span>' +
                            '<span class="testimonial-date">' + dateStr + '</span></div></div>';
                        grid.appendChild(card);
                    });
                    btnLoadMore.dataset.page = page;
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                    if (page >= totalPages) document.getElementById('testimonialsLoadMore').style.display = 'none';
                }
            } catch (err) { console.error('Error cargando testimonios:', err); }
            finally {
                btnLoadMore.disabled = false;
                if (spinner) spinner.style.display = 'none';
                if (btnText) btnText.style.opacity = '1';
                if (icon) icon.style.display = '';
            }
        });
    }
});
