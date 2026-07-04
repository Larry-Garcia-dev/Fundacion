<?php
// Front Controller Admin
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/View.php';
require_once __DIR__ . '/core/Settings.php';
require_once __DIR__ . '/core/Uploader.php';
require_once __DIR__ . '/database/migrations.php';
require_once __DIR__ . '/models/Service.php';
require_once __DIR__ . '/models/Benefit.php';
require_once __DIR__ . '/models/OrgValue.php';
require_once __DIR__ . '/models/Gallery.php';
require_once __DIR__ . '/models/CharityWork.php';
require_once __DIR__ . '/models/Testimonial.php';
require_once __DIR__ . '/models/ContactMessage.php';
require_once __DIR__ . '/models/User.php';

// Enrutar
$route  = preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['route'] ?? 'dashboard');
$action = preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['action'] ?? 'index');

// Logout (no necesita BD ni controller)
if ($route === 'logout') {
    Auth::logout();
    header('Location: /admin.php?route=login');
    exit;
}

$map = [
    'dashboard'    => 'DashboardController',
    'login'        => 'LoginController',
    'setup'        => 'SetupController',
    'settings'     => 'SettingsController',
    'gallery'      => 'GalleryController',
    'charity'      => 'CharityController',
    'testimonials' => 'TestimonialsController',
    'messages'     => 'MessagesController',
    'services'     => 'ServicesController',
    'benefits'     => 'BenefitsController',
    'values'       => 'ValuesController',
];

if (!isset($map[$route])) {
    http_response_code(404);
    echo 'Página no encontrada.';
    exit;
}

// Conectar y migrar (solo para rutas que necesitan BD)
if ($route !== 'setup') {
    $pdo = Database::connect();
    Migration::runAll($pdo);
}

require_once __DIR__ . '/controllers/admin/' . $map[$route] . '.php';

$controller = new $map[$route]();
$method     = $action . 'Action';

if (!method_exists($controller, $method)) {
    $method = 'indexAction';
}

$controller->$method();
