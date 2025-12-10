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
}
