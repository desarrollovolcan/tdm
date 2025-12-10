<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Championship.php';
require_once __DIR__ . '/../models/Player.php';

class ChampionshipController extends BaseController
{
    public function index()
    {
        $model = new Championship($this->pdo);
        $championships = $model->all();
        $this->render('championships/index', compact('championships'));
    }

    public function create()
    {
        $this->ensureAdmin();
        $this->render('championships/create');
    }

    public function store()
    {
        $this->ensureAdmin();
        $data = [
            'name' => htmlspecialchars($_POST['name']),
            'location' => htmlspecialchars($_POST['location']),
            'start_date' => $_POST['start_date'],
            'category' => $_POST['category'],
            'modality' => $_POST['modality'],
            'participants' => (int)$_POST['participants'],
            'format' => 'groups + elimination',
            'best_of_sets' => (int)$_POST['best_of_sets'],
            'set_points' => 11,
        ];
        $model = new Championship($this->pdo);
        $model->create($data);
        $this->redirect('/championships');
    }

    public function generateGroups()
    {
        $this->ensureAdmin();
        $championshipId = (int)$_POST['championship_id'];
        $groupSize = (int)$_POST['group_size'];
        $playerModel = new Player($this->pdo);
        $players = $playerModel->all();
        $championship = new Championship($this->pdo);
        $championship->generateGroups($championshipId, $players, $groupSize);
        $this->redirect('/championships');
    }

    private function ensureAdmin()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $this->redirect('/login');
        }
    }
}
