<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Championship.php';
require_once __DIR__ . '/../models/Player.php';

class ChampionshipController extends BaseController
{
    public function index()
    {
        $this->requireAdmin();
        $model = new Championship($this->pdo);
        $championships = $model->all();
        $this->render('championships/index', compact('championships'));
    }

    public function create()
    {
        $this->requireAdmin();
        $this->render('championships/create');
    }

    public function store()
    {
        $this->requireAdmin();
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'start_date' => $_POST['start_date'] ?? null,
            'category' => $_POST['category'] ?? null,
            'modality' => $_POST['modality'] ?? null,
            'participants' => (int)($_POST['participants'] ?? 0),
            'format' => 'groups + elimination',
            'best_of_sets' => (int)($_POST['best_of_sets'] ?? 5),
            'set_points' => 11,
        ];

        if (!$data['name'] || !$data['location'] || !$data['start_date'] || $data['participants'] < 2) {
            $error = 'Completa todos los campos obligatorios (mínimo 2 participantes).';
            $this->render('championships/create', compact('error'));
            return;
        }

        $model = new Championship($this->pdo);
        $model->create($data);
        $this->redirect('/championships');
    }

    public function generateGroups()
    {
        $this->requireAdmin();
        $championshipId = (int)$_POST['championship_id'];
        $groupSize = max(3, min(6, (int)$_POST['group_size']));
        $playerModel = new Player($this->pdo);
        $players = $playerModel->all();
        $championship = new Championship($this->pdo);
        if (count($players) === 0) {
            $error = 'No hay jugadores inscritos para generar grupos.';
            $championships = $championship->all();
            $this->render('championships/index', compact('championships', 'error'));
            return;
        }
        $championship->generateGroups($championshipId, $players, $groupSize);
        $this->redirect('/championships');
    }
}
