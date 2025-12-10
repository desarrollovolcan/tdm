<?php
require_once __DIR__ . '/../models/Institution.php';

class BaseController
{
    protected $pdo;
    protected $institution;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->institution = (new Institution($this->pdo))->getSettings();
    }

    protected function render($view, $params = [])
    {
        $institution = $this->institution;
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
