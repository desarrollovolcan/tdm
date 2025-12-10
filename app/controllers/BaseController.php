<?php
class BaseController
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    protected function render($view, $params = [])
    {
        extract($params);
        include __DIR__ . '/../views/layouts/header.php';
        include __DIR__ . '/../views/' . $view . '.php';
        include __DIR__ . '/../views/layouts/footer.php';
    }

    protected function redirect($path)
    {
        header('Location: ' . $path);
        exit;
    }

    protected function requireAuth()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }
    }

    protected function requireAdmin()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $this->redirect('/login');
        }
    }

    protected function requireOfficial()
    {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'referee'])) {
            $this->redirect('/login');
        }
    }
}
