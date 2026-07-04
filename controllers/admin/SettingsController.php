<?php
class SettingsController
{
    private const LOGO_KEYS = ['logo_path', 'hero_logo_path', 'contact_logo_path', 'footer_logo_path'];
    private const PRESET_LOGOS = [
        'logos/VisiónLogo_Blanco.png'  => 'Blanco',
        'logos/VisiónLogo_Full.png'    => 'Full Color',
        'logos/VisiónLogo_Morado.png'  => 'Morado',
        'logos/VisiónLogo_Negro.png'   => 'Negro',
    ];

    public function indexAction(): void
    {
        Auth::require();

        if (isset($_GET['select_logo'], $_GET['logo_value'])) {
            $this->handlePresetLogo();
            return;
        }

        $message = '';
        $error   = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->handlePost();
            $message = $result['message'];
            $error   = $result['error'];
        }

        Settings::clearCache();
        View::render('admin/settings', [
            'settings'      => Settings::all(),
            'preset_logos'  => self::PRESET_LOGOS,
            'message'       => $message,
            'error'         => $error,
        ]);
    }

    private function handlePost(): array
    {
        $error = '';
        try {
            $pairs = $_POST['settings'] ?? [];

            foreach (['logo_file', 'hero_logo_file', 'contact_logo_file', 'hero_bg_image_file', 'about_img_url_file'] as $input) {
                $settingKey = str_replace('_file', '', $input);
                if ($input === 'hero_bg_image_file') $settingKey = 'hero_bg_image';
                if ($input === 'about_img_url_file') $settingKey = 'about_img_url';

                if (isset($_FILES[$input]) && $_FILES[$input]['error'] !== UPLOAD_ERR_NO_FILE) {
                    $existing = Settings::get($settingKey);
                    $pairs[$settingKey] = Uploader::image($input, $existing);
                }
            }

            if (!isset($pairs['testimonials_enabled'])) {
                $pairs['testimonials_enabled'] = '0';
            }

            Settings::setMany($pairs);
            return ['message' => 'Configuraciones actualizadas correctamente.', 'error' => ''];
        } catch (Exception $e) {
            return ['message' => '', 'error' => $e->getMessage()];
        }
    }

    private function handlePresetLogo(): void
    {
        $key = $_GET['select_logo'];
        $val = $_GET['logo_value'];

        if (in_array($key, self::LOGO_KEYS) && array_key_exists($val, self::PRESET_LOGOS)) {
            Settings::set($key, $val);
        }

        header('Location: /admin.php?route=settings&saved=1');
        exit;
    }
}
