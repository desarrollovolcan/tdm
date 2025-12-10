<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/MatchModel.php';

class MatchController extends BaseController
{
    public function show($id)
    {
        $model = new MatchModel($this->pdo);
        $match = $model->find($id);
        $this->render('matches/show', compact('match'));
    }

    public function storeResult()
    {
        $this->requireOfficial();
        $matchId = (int)$_POST['match_id'];
        $setsInput = $_POST['set'] ?? [];
        $sets = [];
        foreach ($setsInput as $row) {
            if ($row['p1'] === '' || $row['p2'] === '') {
                continue;
            }
            $sets[] = [(int)$row['p1'], (int)$row['p2']];
        }
        $model = new MatchModel($this->pdo);
        try {
            $model->recordResult($matchId, $sets);
            $success = true;
        } catch (InvalidArgumentException $e) {
            $error = $e->getMessage();
        }
        $match = $model->find($matchId);
        $this->render('matches/show', compact('match', 'success', 'error'));
    }
}
