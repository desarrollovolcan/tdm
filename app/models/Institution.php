<?php
require_once __DIR__ . '/BaseModel.php';

class Institution extends BaseModel
{
    public function getSettings()
    {
        $stmt = $this->pdo->query('SELECT * FROM institution_settings LIMIT 1');
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$settings) {
            $this->pdo->exec("INSERT INTO institution_settings (name) VALUES ('Institución organizadora')");
            $stmt = $this->pdo->query('SELECT * FROM institution_settings LIMIT 1');
            $settings = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $settings;
    }

    public function updateSettings($data)
    {
        $stmt = $this->pdo->prepare('UPDATE institution_settings SET name = :name, contact_email = :contact_email, contact_phone = :contact_phone, website = :website, address = :address WHERE id = :id');
        $stmt->execute([
            'name' => $data['name'],
            'contact_email' => $data['contact_email'],
            'contact_phone' => $data['contact_phone'],
            'website' => $data['website'],
            'address' => $data['address'],
            'id' => $data['id'],
        ]);
    }

    public function updateLogoPath($id, $path)
    {
        $stmt = $this->pdo->prepare('UPDATE institution_settings SET logo_path = :logo_path WHERE id = :id');
        $stmt->execute(['logo_path' => $path, 'id' => $id]);
    }
}
