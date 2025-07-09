<?php
abstract class BaseController {
    protected function render($view, $data = []) {
        extract($data);
        
        ob_start();
        require_once 'views/' . $view . '.php';
        $content = ob_get_clean();
        
        require_once 'views/layouts/header.php';
        echo $content;
        require_once 'views/layouts/footer.php';
    }
    
    protected function redirect($url) {
        header("Location: " . $url);
        exit();
    }
    
    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}
?>