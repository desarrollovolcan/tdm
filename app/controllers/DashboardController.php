<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Championship.php';
require_once __DIR__ . '/../models/Player.php';

class DashboardController extends BaseController
{
    public function index()
    {
        $this->requireAuth();
        $championshipModel = new Championship($this->pdo);
        $playerModel = new Player($this->pdo);

        $championships = $championshipModel->all();
        $players = $playerModel->all();

        $metrics = [
            'championships' => count($championships),
            'players' => count($players),
            'matches_played' => $this->countMatches('played'),
            'matches_pending' => $this->countMatches('scheduled'),
        ];

        $this->render('dashboard/index', compact('metrics', 'championships', 'players'));
    }

    private function countMatches($status)
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM matches WHERE status = :status');
        $stmt->execute(['status' => $status]);
        return (int)$stmt->fetchColumn();
    }

    private function requireAuth()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }
    }
}
