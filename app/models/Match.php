<?php
require_once __DIR__ . '/BaseModel.php';

class Match extends BaseModel
{
    public function find($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM matches WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function recordResult($matchId, $sets)
    {
        $player1Sets = 0;
        $player2Sets = 0;
        foreach ($sets as $set) {
            [$p1, $p2] = $set;
            if (!$this->isValidSet($p1, $p2)) {
                throw new InvalidArgumentException('Resultado de set inválido según reglas ITTF');
            }
            if ($p1 > $p2) {
                $player1Sets++;
            } else {
                $player2Sets++;
            }
        }

        $stmt = $this->pdo->prepare('UPDATE matches SET player1_sets = :p1_sets, player2_sets = :p2_sets, sets_json = :sets, status = :status WHERE id = :id');
        $stmt->execute([
            'p1_sets' => $player1Sets,
            'p2_sets' => $player2Sets,
            'sets' => json_encode($sets),
            'status' => 'played',
            'id' => $matchId
        ]);
    }

    private function isValidSet($p1, $p2)
    {
        $maxScore = max($p1, $p2);
        $minScore = min($p1, $p2);
        if ($maxScore < 11) return false;
        return ($maxScore - $minScore) >= 2;
    }
}
