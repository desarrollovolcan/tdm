<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Institution.php';

class InstitutionController extends BaseController
{
    public function edit()
    {
        $this->requireAdmin();
        $institution = $this->institution;
        $this->render('institution/edit', compact('institution'));
    }

    public function update()
    {
        $this->requireAdmin();
        $institutionModel = new Institution($this->pdo);
        $institution = $institutionModel->getSettings();
        $data = [
            'id' => $institution['id'],
            'name' => trim($_POST['name'] ?? $institution['name']),
            'contact_email' => trim($_POST['contact_email'] ?? ''),
            'contact_phone' => trim($_POST['contact_phone'] ?? ''),
            'website' => trim($_POST['website'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
        ];
        $institutionModel->updateSettings($data);

        if (!empty($_FILES['logo']['tmp_name'])) {
            $this->handleLogoUpload($institutionModel, $institution['id']);
        }

        $this->institution = $institutionModel->getSettings();
        $success = true;
        $institution = $this->institution;
        $this->render('institution/edit', compact('institution', 'success'));
    }

    private function handleLogoUpload(Institution $institutionModel, $id)
    {
        $file = $_FILES['logo'];
        $allowed = ['image/png' => 'png', 'image/jpeg' => 'jpg'];
        if (!isset($allowed[$file['type']])) {
            return;
        }
        $ext = $allowed[$file['type']];
        $uploadDir = __DIR__ . '/../../public/uploads';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }
        $targetName = 'institution-logo.' . $ext;
        $targetPath = $uploadDir . '/' . $targetName;
        if (is_uploaded_file($file['tmp_name'])) {
            move_uploaded_file($file['tmp_name'], $targetPath);
            $publicPath = '/uploads/' . $targetName;
            $institutionModel->updateLogoPath($id, $publicPath);
        }
    }
}
