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
            'name' => trim($_POST['name'] ?? ''),
            'document' => trim($_POST['document'] ?? ''),
            'birthdate' => $_POST['birthdate'] ?? null,
            'club' => trim($_POST['club'] ?? ''),
            'category' => $_POST['category'] ?? null,
            'gender' => $_POST['gender'] ?? null,
            'email' => filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL),
            'phone' => trim($_POST['phone'] ?? ''),
            'ranking' => $_POST['ranking'] ?? '',
        ];

        if (!$data['name'] || !$data['document'] || !$data['birthdate'] || !$data['club'] || !$data['category'] || !$data['gender'] || !$data['email'] || !$data['phone']) {
            $error = 'Todos los campos obligatorios deben completarse correctamente.';
            $this->render('players/register', compact('error'));
            return;
        }

        $playerModel->create($data);
        $success = true;
        $this->render('players/register', compact('success'));
    }

    public function index()
    {
        $this->requireAdmin();
        $playerModel = new Player($this->pdo);
        $players = $playerModel->all();
        $this->render('players/index', compact('players'));
    }

    public function updateRanking()
    {
        $this->requireAdmin();
        $playerModel = new Player($this->pdo);
        foreach ($_POST['ranking'] as $playerId => $ranking) {
            $rankingValue = $ranking !== '' ? (int)$ranking : null;
            $playerModel->updateRanking($playerId, $rankingValue);
        }
        $this->redirect('/players');
    }
}
