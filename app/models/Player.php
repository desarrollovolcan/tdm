<?php
require_once __DIR__ . '/BaseModel.php';

class Player extends BaseModel
{
    public function all()
    {
        $stmt = $this->pdo->query('SELECT * FROM players ORDER BY ranking IS NULL, ranking ASC, name');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $stmt = $this->pdo->prepare('INSERT INTO players (name, document, birthdate, club, category, gender, email, phone, ranking) VALUES (:name, :document, :birthdate, :club, :category, :gender, :email, :phone, :ranking)');
        return $stmt->execute([
            'name' => $data['name'],
            'document' => $data['document'],
            'birthdate' => $data['birthdate'],
            'club' => $data['club'],
            'category' => $data['category'],
            'gender' => $data['gender'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'ranking' => $data['ranking'] !== '' ? $data['ranking'] : null
        ]);
    }

    public function updateRanking($playerId, $ranking)
    {
        $stmt = $this->pdo->prepare('UPDATE players SET ranking = :ranking WHERE id = :id');
        return $stmt->execute(['ranking' => $ranking, 'id' => $playerId]);
    }
}
