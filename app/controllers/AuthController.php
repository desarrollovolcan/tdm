<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends BaseController
{
    public function loginForm()
    {
        $this->render('auth/login');
    }

    public function login()
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        $userModel = new User($this->pdo);
        $user = $userModel->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'role' => $user['role'],
            ];
            $this->redirect('/');
        }
        $error = 'Credenciales inválidas';
        $this->render('auth/login', compact('error'));
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('/login');
    }
}
