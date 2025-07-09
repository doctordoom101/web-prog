<?php
// Autoload classes
spl_autoload_register(function ($class) {
    $paths = [
        '../controllers/' . $class . '.php',
        '../models/' . $class . '.php',
        '../config/' . $class . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Simple routing
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

try {
    switch ($page) {
        case 'users':
            $controller = new UserController();
            switch ($action) {
                case 'index':
                    $controller->index();
                    break;
                case 'show':
                    $controller->show($id);
                    break;
                case 'create':
                    $controller->create();
                    break;
                case 'edit':
                    $controller->edit($id);
                    break;
                case 'delete':
                    $controller->delete($id);
                    break;
                default:
                    $controller->index();
            }
            break;
        
        case 'home':
        default:
            // Redirect to users page as default
            header('Location: index.php?page=users');
            exit();
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>