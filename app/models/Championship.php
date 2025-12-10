<?php
require_once __DIR__ . '/BaseModel.php';

class Championship extends BaseModel
{
    public function all()
    {
        $stmt = $this->pdo->query('SELECT * FROM championships ORDER BY start_date DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM championships WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $stmt = $this->pdo->prepare('INSERT INTO championships (name, location, start_date, category, modality, participants, format, best_of_sets, set_points) VALUES (:name, :location, :start_date, :category, :modality, :participants, :format, :best_of_sets, :set_points)');
        return $stmt->execute([
            'name' => $data['name'],
            'location' => $data['location'],
            'start_date' => $data['start_date'],
            'category' => $data['category'],
            'modality' => $data['modality'],
            'participants' => $data['participants'],
            'format' => $data['format'],
            'best_of_sets' => $data['best_of_sets'],
            'set_points' => $data['set_points'],
        ]);
    }

    public function groupsForChampionship($championshipId)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM groups WHERE championship_id = :id ORDER BY name');
        $stmt->execute(['id' => $championshipId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function matchesForChampionship($championshipId)
    {
        $stmt = $this->pdo->prepare('SELECT m.*, p1.name as player1_name, p2.name as player2_name FROM matches m JOIN players p1 ON m.player1_id = p1.id JOIN players p2 ON m.player2_id = p2.id WHERE championship_id = :id ORDER BY scheduled_at');
        $stmt->execute(['id' => $championshipId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function generateGroups($championshipId, $players, $groupSize = 4)
    {
        usort($players, function ($a, $b) {
            if ($a['ranking'] === $b['ranking']) {
                return strcmp($a['name'], $b['name']);
            }
            if ($a['ranking'] === null) return 1;
            if ($b['ranking'] === null) return -1;
            return $a['ranking'] <=> $b['ranking'];
        });

        $groupCount = max(1, ceil(count($players) / $groupSize));
        $groups = [];
        for ($i = 0; $i < $groupCount; $i++) {
            $groupName = chr(65 + $i);
            $stmt = $this->pdo->prepare('INSERT INTO groups (championship_id, name) VALUES (:championship_id, :name)');
            $stmt->execute(['championship_id' => $championshipId, 'name' => $groupName]);
            $groups[$i] = ['id' => $this->pdo->lastInsertId(), 'players' => []];
        }

        $direction = 1;
        $index = 0;
        foreach ($players as $player) {
            $groups[$index]['players'][] = $player['id'];
            $index += $direction;
            if ($index === $groupCount) {
                $index = $groupCount - 1;
                $direction = -1;
            } elseif ($index < 0) {
                $index = 0;
                $direction = 1;
            }
        }

        foreach ($groups as $g) {
            foreach ($g['players'] as $playerId) {
                $stmt = $this->pdo->prepare('INSERT INTO group_players (group_id, player_id) VALUES (:group_id, :player_id)');
                $stmt->execute(['group_id' => $g['id'], 'player_id' => $playerId]);
            }
        }

        $this->generateGroupMatches($groups, $championshipId);
        return $groups;
    }

    private function generateGroupMatches($groups, $championshipId)
    {
        foreach ($groups as $g) {
            $players = $g['players'];
            $count = count($players);
            for ($i = 0; $i < $count; $i++) {
                for ($j = $i + 1; $j < $count; $j++) {
                    $stmt = $this->pdo->prepare('INSERT INTO matches (championship_id, group_id, player1_id, player2_id, stage, scheduled_at) VALUES (:championship_id, :group_id, :player1_id, :player2_id, :stage, NOW())');
                    $stmt->execute([
                        'championship_id' => $championshipId,
                        'group_id' => $g['id'],
                        'player1_id' => $players[$i],
                        'player2_id' => $players[$j],
                        'stage' => 'group'
                    ]);
                }
            }
        }
    }

    public function generateBracket($championshipId, $qualifiers)
    {
        $size = 1;
        while ($size < count($qualifiers)) {
            $size *= 2;
        }
        $positions = [];
        for ($i = 0; $i < $size; $i++) {
            $seed = $i + 1;
            $opponent = $size - $i;
            if ($seed >= $opponent) {
                break;
            }
            $positions[] = [$seed, $opponent];
        }

        foreach ($positions as $pair) {
            $p1 = $qualifiers[$pair[0] - 1] ?? null;
            $p2 = $qualifiers[$pair[1] - 1] ?? null;
            if (!$p1 || !$p2) {
                continue;
            }
            $stmt = $this->pdo->prepare('INSERT INTO matches (championship_id, player1_id, player2_id, stage, scheduled_at) VALUES (:championship_id, :player1_id, :player2_id, :stage, NOW())');
            $stmt->execute([
                'championship_id' => $championshipId,
                'player1_id' => $p1,
                'player2_id' => $p2,
                'stage' => 'elimination'
            ]);
        }
    }
}
