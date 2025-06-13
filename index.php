<?php
/**
 * Application Entry Point for XAMPP/LAMPP
 */

// Set error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration
require_once __DIR__ . '/config/app.php';

// Load core classes
require_once __DIR__ . '/core/Router.php';

// Get the route from URL
$route = $_GET['route'] ?? '/';

// Initialize router
$router = new Router();

// Define routes
$router->get('/', 'HomeController', 'index');
$router->post('/process', 'HomeController', 'processMessage');

$router->get('/auth/login', 'AuthController', 'login');
$router->post('/auth/login', 'AuthController', 'login');
$router->get('/auth/register', 'AuthController', 'register');
$router->post('/auth/register', 'AuthController', 'register');
$router->get('/auth/logout', 'AuthController', 'logout');

$router->get('/admin/setup', 'AdminController', 'setup');
$router->post('/admin/setup', 'AdminController', 'setup');
$router->get('/admin/dashboard', 'AdminController', 'dashboard');

// Dispatch the request
try {
    $router->dispatch($route);
} catch (Exception $e) {
    // Handle errors gracefully
    if (DEBUG_MODE) {
        echo "<h1>Error</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        echo "<h1>Oops! Algo deu errado.</h1>";
        echo "<p>Por favor, tente novamente mais tarde.</p>";
        echo "<a href='/chatfast/'>Voltar ao início</a>";
    }
    exit;
}