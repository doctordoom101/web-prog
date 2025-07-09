<?php
require_once 'controllers/BaseController.php';
require_once 'models/User.php';

class UserController extends BaseController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function index() {
        $users = $this->userModel->getAllUsers();
        $this->render('users/index', ['users' => $users]);
    }
    
    public function show($id) {
        $user = $this->userModel->getUserById($id);
        if (!$user) {
            $this->redirect('index.php?page=users');
        }
        $this->render('users/show', ['user' => $user]);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            
            if ($this->validateUser($name, $email, $phone)) {
                if ($this->userModel->createUser($name, $email, $phone)) {
                    $this->redirect('index.php?page=users&message=created');
                } else {
                    $error = 'Gagal menambahkan user';
                }
            } else {
                $error = 'Data tidak valid';
            }
        }
        
        $this->render('users/create', ['error' => $error ?? null]);
    }
    
    public function edit($id) {
        $user = $this->userModel->getUserById($id);
        if (!$user) {
            $this->redirect('index.php?page=users');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            
            if ($this->validateUser($name, $email, $phone)) {
                if ($this->userModel->updateUser($id, $name, $email, $phone)) {
                    $this->redirect('index.php?page=users&message=updated');
                } else {
                    $error = 'Gagal mengupdate user';
                }
            } else {
                $error = 'Data tidak valid';
            }
        }
        
        $this->render('users/edit', ['user' => $user, 'error' => $error ?? null]);
    }
    
    public function delete($id) {
        if ($this->userModel->deleteUser($id)) {
            $this->redirect('index.php?page=users&message=deleted');
        } else {
            $this->redirect('index.php?page=users&message=error');
        }
    }
    
    private function validateUser($name, $email, $phone) {
        return !empty($name) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($phone);
    }
}
?>