<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Player.php';

class PlayerController extends BaseController
{
    public function registerForm()
    {
        $this->render('players/register');
    }

    public function store()
    {
        $playerModel = new Player($this->pdo);
        $data = [
            'name' => htmlspecialchars($_POST['name']),
            'document' => htmlspecialchars($_POST['document']),
            'birthdate' => $_POST['birthdate'],
            'club' => htmlspecialchars($_POST['club']),
            'category' => $_POST['category'],
            'gender' => $_POST['gender'],
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
            'phone' => htmlspecialchars($_POST['phone']),
            'ranking' => $_POST['ranking'],
        ];
        $playerModel->create($data);
        $success = true;
        $this->render('players/register', compact('success'));
    }

    public function index()
    {
        $this->ensureAdmin();
        $playerModel = new Player($this->pdo);
        $players = $playerModel->all();
        $this->render('players/index', compact('players'));
    }

    public function updateRanking()
    {
        $this->ensureAdmin();
        $playerModel = new Player($this->pdo);
        foreach ($_POST['ranking'] as $playerId => $ranking) {
            $rankingValue = $ranking !== '' ? (int)$ranking : null;
            $playerModel->updateRanking($playerId, $rankingValue);
        }
        $this->redirect('/players');
    }

    private function ensureAdmin()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $this->redirect('/login');
        }
    }
}
