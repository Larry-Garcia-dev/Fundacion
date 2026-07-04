<?php
// Front Controller Público
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Settings.php';
require_once __DIR__ . '/core/View.php';
require_once __DIR__ . '/core/Uploader.php';
require_once __DIR__ . '/database/migrations.php';
require_once __DIR__ . '/models/Service.php';
require_once __DIR__ . '/models/Benefit.php';
require_once __DIR__ . '/models/OrgValue.php';
require_once __DIR__ . '/models/Gallery.php';
require_once __DIR__ . '/models/CharityWork.php';
require_once __DIR__ . '/models/Testimonial.php';
require_once __DIR__ . '/models/ContactMessage.php';

// Conectar y migrar
$pdo = Database::connect();
Migration::runAll($pdo);

// Cargar datos
$settings     = Settings::all();
$services     = (new ServiceModel())->all();
$benefits     = (new BenefitModel())->all();
$values       = (new OrgValueModel())->all();
$gallery      = (new GalleryModel())->all();
$works        = (new CharityWorkModel())->all();
$testModel    = new TestimonialModel();
$testimonials = $testModel->approved(6, 0);
$test_total   = $testModel->count('approved');

// Renderizar secciones
$sections = [
    '_hero', '_about', '_services', '_benefits', '_commitment',
    '_mission_vision', '_values', '_gallery', '_charity',
    '_testimonials', '_contact',
];

$data = compact('settings', 'services', 'benefits', 'values', 'gallery', 'works', 'testimonials', 'test_total');

ob_start();
foreach ($sections as $section) {
    View::partial("sections/$section", $data);
}
$content = ob_get_clean();

View::render('layout', array_merge($data, ['content' => $content]));
