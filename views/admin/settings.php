<?php
$page_title = 'Configuración de Textos y Apariencia';
$unread_count = $unread_messages ?? 0;
$s = $settings;
ob_start();
?>
<?php if (!empty($message)): ?><div class="alert alert-success"><i data-lucide="check-circle"></i><span><?= htmlspecialchars($message) ?></span></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="alert alert-danger"><i data-lucide="alert-circle"></i><span><?= htmlspecialchars($error) ?></span></div><?php endif; ?>

<?php
function logo_picker(string $key, array $presets, string $current): string {
    $h = '<div style="margin-bottom:16px;"><label style="font-weight:600;font-size:13px;display:block;margin-bottom:6px;">Logo prediseñado</label><div style="display:flex;gap:10px;flex-wrap:wrap;">';
    foreach ($presets as $path => $label) {
        $sel = ($current === $path) ? 'border-color:var(--primary);box-shadow:0 0 0 2px var(--primary);' : '';
        $bg = ($label === 'Blanco') ? '#e2e8f0' : '#fff';
        $url = '/admin.php?route=settings&select_logo=' . urlencode($key) . '&logo_value=' . urlencode($path);
        $h .= '<a href="' . htmlspecialchars($url) . '" style="display:flex;flex-direction:column;align-items:center;gap:4px;text-decoration:none;padding:8px;border-radius:8px;border:2px solid var(--border);' . $sel . '">';
        $h .= '<img src="../' . htmlspecialchars($path) . '" alt="' . htmlspecialchars($label) . '" style="height:40px;max-width:100px;object-fit:contain;background:' . $bg . ';border-radius:4px;padding:3px;">';
        $h .= '<span style="font-size:10px;font-weight:600;">' . htmlspecialchars($label) . '</span></a>';
    }
    return $h . '</div></div>';
}
?>

<div class="settings-tabs-container">
    <div class="settings-tabs" style="display:flex;gap:8px;border-bottom:2px solid var(--border-light);padding-bottom:10px;overflow-x:auto;margin-bottom:20px;">
        <button class="btn btn-outline tab-btn active" onclick="switchTab(event,'tab-branding')" style="border-color:var(--primary);color:var(--primary);"><i data-lucide="palette"></i> Logo y Colores</button>
        <button class="btn btn-outline tab-btn" onclick="switchTab(event,'tab-hero')"><i data-lucide="airplay"></i> Hero</button>
        <button class="btn btn-outline tab-btn" onclick="switchTab(event,'tab-about')"><i data-lucide="users"></i> Quiénes Somos</button>
        <button class="btn btn-outline tab-btn" onclick="switchTab(event,'tab-mission')"><i data-lucide="compass"></i> Misión y Visión</button>
        <button class="btn btn-outline tab-btn" onclick="switchTab(event,'tab-cta')"><i data-lucide="help-circle"></i> Trabajemos Juntos</button>
        <button class="btn btn-outline tab-btn" onclick="switchTab(event,'tab-footer')"><i data-lucide="info"></i> Contacto y Footer</button>
    </div>

    <form action="/admin.php?route=settings" method="POST" class="admin-card" enctype="multipart/form-data">
        <!-- TAB: LOGO Y COLORES -->
        <div id="tab-branding" class="tab-content active-content">
            <h3 style="font-family:'Outfit';font-size:15px;margin-bottom:14px;color:var(--primary);">Logo Navbar</h3>
            <?= logo_picker('logo_path', $preset_logos, $s['logo_path'] ?? '') ?>
            <div class="form-grid">
                <div class="form-group"><label>Altura Logo (px)</label><input type="number" name="settings[logo_size]" value="<?= htmlspecialchars($s['logo_size'] ?? '72') ?>" min="20" max="250"></div>
                <div class="form-group"><label>Offset X (px)</label><input type="number" name="settings[logo_offset_x]" value="<?= htmlspecialchars($s['logo_offset_x'] ?? '0') ?>"></div>
                <div class="form-group"><label>Offset Y (px)</label><input type="number" name="settings[logo_offset_y]" value="<?= htmlspecialchars($s['logo_offset_y'] ?? '0') ?>"></div>
                <div class="form-group full-width"><label>Subir logo personalizado</label><input type="file" name="logo_file" accept="image/*"></div>
            </div>
            <hr style="margin:24px 0;border:none;border-bottom:1px solid var(--border-light);">
            <h3 style="font-family:'Outfit';font-size:15px;margin-bottom:14px;color:var(--primary);">Logo Hero</h3>
            <?= logo_picker('hero_logo_path', $preset_logos, $s['hero_logo_path'] ?? '') ?>
            <div class="form-group"><label>Subir logo Hero</label><input type="file" name="hero_logo_file" accept="image/*"></div>
            <hr style="margin:24px 0;border:none;border-bottom:1px solid var(--border-light);">
            <h3 style="font-family:'Outfit';font-size:15px;margin-bottom:14px;color:var(--primary);">Logo Contacto</h3>
            <?= logo_picker('contact_logo_path', $preset_logos, $s['contact_logo_path'] ?? '') ?>
            <div class="form-group"><label>Subir logo Contacto</label><input type="file" name="contact_logo_file" accept="image/*"></div>
            <hr style="margin:24px 0;border:none;border-bottom:1px solid var(--border-light);">
            <h3 style="font-family:'Outfit';font-size:15px;margin-bottom:14px;color:var(--primary);">Logo Footer</h3>
            <?= logo_picker('footer_logo_path', $preset_logos, $s['footer_logo_path'] ?? '') ?>
            <hr style="margin:24px 0;border:none;border-bottom:1px solid var(--border-light);">
            <h3 style="font-family:'Outfit';font-size:15px;margin-bottom:14px;color:var(--primary);">Secciones</h3>
            <div class="form-group"><label style="display:flex;align-items:center;gap:12px;cursor:pointer;font-weight:600;">
                <div class="toggle-switch"><input type="checkbox" name="settings[testimonials_enabled]" value="1" <?= !empty($s['testimonials_enabled']) ? 'checked' : '' ?>><span class="toggle-slider"></span></div>
                Mostrar sección de Testimonios
            </label></div>
            <hr style="margin:24px 0;border:none;border-bottom:1px solid var(--border-light);">
            <h3 style="font-family:'Outfit';font-size:15px;margin-bottom:14px;color:var(--primary);">Paleta de Colores</h3>
            <div class="form-grid" style="grid-template-columns:repeat(3,1fr);">
                <div class="form-group"><label>Color Principal</label><input type="color" name="settings[theme_color_primary]" value="<?= htmlspecialchars($s['theme_color_primary'] ?? '#9b98d5') ?>" style="height:45px;padding:4px;cursor:pointer;"></div>
                <div class="form-group"><label>Color Secundario</label><input type="color" name="settings[theme_color_secondary]" value="<?= htmlspecialchars($s['theme_color_secondary'] ?? '#86d2f1') ?>" style="height:45px;padding:4px;cursor:pointer;"></div>
                <div class="form-group"><label>Color Oscuro</label><input type="color" name="settings[theme_color_dark]" value="<?= htmlspecialchars($s['theme_color_dark'] ?? '#1e293b') ?>" style="height:45px;padding:4px;cursor:pointer;"></div>
            </div>
        </div>

        <!-- TAB: HERO -->
        <div id="tab-hero" class="tab-content" style="display:none;">
            <div class="form-grid">
                <div class="form-group full-width"><label>Título Principal</label><input type="text" name="settings[hero_title]" value="<?= htmlspecialchars($s['hero_title'] ?? '') ?>" required></div>
                <div class="form-group full-width"><label>Subtítulo</label><textarea name="settings[hero_subtitle]" rows="3" required><?= htmlspecialchars($s['hero_subtitle'] ?? '') ?></textarea></div>
                <div class="form-group"><label>Botón Principal</label><input type="text" name="settings[hero_btn_primary]" value="<?= htmlspecialchars($s['hero_btn_primary'] ?? '') ?>" required></div>
                <div class="form-group"><label>Botón Secundario</label><input type="text" name="settings[hero_btn_secondary]" value="<?= htmlspecialchars($s['hero_btn_secondary'] ?? '') ?>" required></div>
                <div class="form-group full-width"><label>Imagen de Fondo (URL)</label><input type="text" name="settings[hero_bg_image]" value="<?= htmlspecialchars($s['hero_bg_image'] ?? '') ?>" placeholder="https://..."></div>
                <div class="form-group full-width"><label>O subir imagen</label><input type="file" name="hero_bg_image_file" accept="image/*"></div>
            </div>
        </div>

        <!-- TAB: QUIÉNES SOMOS -->
        <div id="tab-about" class="tab-content" style="display:none;">
            <div class="form-grid">
                <div class="form-group"><label>Título</label><input type="text" name="settings[about_title]" value="<?= htmlspecialchars($s['about_title'] ?? '') ?>"></div>
                <div class="form-group"><label>Subtítulo</label><input type="text" name="settings[about_subtitle]" value="<?= htmlspecialchars($s['about_subtitle'] ?? '') ?>"></div>
                <div class="form-group full-width"><label>Párrafo 1</label><textarea name="settings[about_content_1]" rows="3"><?= htmlspecialchars($s['about_content_1'] ?? '') ?></textarea></div>
                <div class="form-group full-width"><label>Párrafo 2</label><textarea name="settings[about_content_2]" rows="3"><?= htmlspecialchars($s['about_content_2'] ?? '') ?></textarea></div>
                <div class="form-group full-width"><label>Imagen (URL)</label><input type="text" name="settings[about_img_url]" value="<?= htmlspecialchars($s['about_img_url'] ?? '') ?>"></div>
                <div class="form-group full-width"><label>O subir imagen</label><input type="file" name="about_img_url_file" accept="image/*"></div>
                <div class="form-group"><label>Título Compromiso</label><input type="text" name="settings[commitment_title]" value="<?= htmlspecialchars($s['commitment_title'] ?? '') ?>"></div>
                <div class="form-group"><label>Subtítulo Compromiso</label><input type="text" name="settings[commitment_subtitle]" value="<?= htmlspecialchars($s['commitment_subtitle'] ?? '') ?>"></div>
                <div class="form-group full-width"><label>Contenido Compromiso</label><textarea name="settings[commitment_content]" rows="3"><?= htmlspecialchars($s['commitment_content'] ?? '') ?></textarea></div>
            </div>
        </div>

        <!-- TAB: MISIÓN Y VISIÓN -->
        <div id="tab-mission" class="tab-content" style="display:none;">
            <div class="form-grid">
                <div><h3 style="font-family:'Outfit';font-size:15px;margin-bottom:14px;color:var(--primary);">Misión</h3>
                    <div class="form-group"><label>Título</label><input type="text" name="settings[mission_title]" value="<?= htmlspecialchars($s['mission_title'] ?? '') ?>"></div>
                    <div class="form-group"><label>Contenido</label><textarea name="settings[mission_content]" rows="5"><?= htmlspecialchars($s['mission_content'] ?? '') ?></textarea></div>
                </div>
                <div><h3 style="font-family:'Outfit';font-size:15px;margin-bottom:14px;color:var(--primary);">Visión</h3>
                    <div class="form-group"><label>Título</label><input type="text" name="settings[vision_title]" value="<?= htmlspecialchars($s['vision_title'] ?? '') ?>"></div>
                    <div class="form-group"><label>Contenido</label><textarea name="settings[vision_content]" rows="5"><?= htmlspecialchars($s['vision_content'] ?? '') ?></textarea></div>
                </div>
            </div>
        </div>

        <!-- TAB: CTA -->
        <div id="tab-cta" class="tab-content" style="display:none;">
            <div class="form-grid">
                <div class="form-group"><label>Título</label><input type="text" name="settings[together_title]" value="<?= htmlspecialchars($s['together_title'] ?? '') ?>"></div>
                <div class="form-group"><label>Texto Botón</label><input type="text" name="settings[together_btn]" value="<?= htmlspecialchars($s['together_btn'] ?? '') ?>"></div>
                <div class="form-group full-width"><label>Contenido</label><textarea name="settings[together_content]" rows="3"><?= htmlspecialchars($s['together_content'] ?? '') ?></textarea></div>
            </div>
        </div>

        <!-- TAB: FOOTER -->
        <div id="tab-footer" class="tab-content" style="display:none;">
            <div class="form-grid">
                <div class="form-group"><label>Dirección</label><input type="text" name="settings[footer_address]" value="<?= htmlspecialchars($s['footer_address'] ?? '') ?>"></div>
                <div class="form-group"><label>Teléfono</label><input type="text" name="settings[footer_phone]" value="<?= htmlspecialchars($s['footer_phone'] ?? '') ?>"></div>
                <div class="form-group full-width"><label>Correo</label><input type="email" name="settings[footer_email]" value="<?= htmlspecialchars($s['footer_email'] ?? '') ?>"></div>
                <div class="form-group"><label>Facebook</label><input type="text" name="settings[footer_facebook]" value="<?= htmlspecialchars($s['footer_facebook'] ?? '') ?>"></div>
                <div class="form-group"><label>Instagram</label><input type="text" name="settings[footer_instagram]" value="<?= htmlspecialchars($s['footer_instagram'] ?? '') ?>"></div>
                <div class="form-group"><label>Twitter</label><input type="text" name="settings[footer_twitter]" value="<?= htmlspecialchars($s['footer_twitter'] ?? '') ?>"></div>
                <div class="form-group"><label>LinkedIn</label><input type="text" name="settings[footer_linkedin]" value="<?= htmlspecialchars($s['footer_linkedin'] ?? '') ?>"></div>
            </div>
        </div>

        <div class="form-actions"><button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Guardar Cambios</button></div>
    </form>
</div>
<script>
function switchTab(e,id){document.querySelectorAll('.tab-content').forEach(c=>c.style.display='none');document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));document.getElementById(id).style.display='block';e.currentTarget.classList.add('active');}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
