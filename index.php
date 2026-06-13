<?php
define('APP_PATH', __DIR__);

require_once __DIR__ . '/config/database.php';

// PSR-4 autoloader: App\ → app/
spl_autoload_register(function (string $class): void {
    if (strpos($class, 'App\\') !== 0) return;
    $file = APP_PATH . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    if (file_exists($file)) require $file;
});

$db = (new Database())->getConnection();

$router = new App\Core\Router();
require APP_PATH . '/config/routes.php';
$router->dispatch($db);
