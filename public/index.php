<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/ChampionshipController.php';
require_once __DIR__ . '/../app/controllers/PlayerController.php';
require_once __DIR__ . '/../app/controllers/MatchController.php';
require_once __DIR__ . '/../app/controllers/InstitutionController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($uri === '/') {
    if (isset($_SESSION['user'])) {
        $controller = new DashboardController($pdo);
        $controller->index();
    } else {
        header('Location: /login');
    }
    exit;
}

if ($method === 'GET' && $uri === '/dashboard') {
    (new DashboardController($pdo))->index();
    exit;
}

if ($method === 'GET' && preg_match('#^/championships/(\\d+)$#', $uri, $matches)) {
    (new ChampionshipController($pdo))->show((int)$matches[1]);
    exit;
}

if ($method === 'GET' && preg_match('#^/championships/(\\d+)/book/pdf$#', $uri, $matches)) {
    (new ChampionshipController($pdo))->exportPdf((int)$matches[1]);
    exit;
}

if ($method === 'GET' && preg_match('#^/championships/(\\d+)/book/excel$#', $uri, $matches)) {
    (new ChampionshipController($pdo))->exportExcel((int)$matches[1]);
    exit;
}

if ($method === 'GET' && preg_match('#^/matches/(\\d+)$#', $uri, $matches)) {
    (new MatchController($pdo))->show((int)$matches[1]);
    exit;
}

switch ([$method, $uri]) {
    case ['GET', '/login']:
        (new AuthController($pdo))->loginForm();
        break;
    case ['POST', '/login']:
        (new AuthController($pdo))->login();
        break;
    case ['GET', '/logout']:
        (new AuthController($pdo))->logout();
        break;
    case ['GET', '/championships']:
        (new ChampionshipController($pdo))->index();
        break;
    case ['GET', '/championships/create']:
        (new ChampionshipController($pdo))->create();
        break;
    case ['POST', '/championships']:
        (new ChampionshipController($pdo))->store();
        break;
    case ['POST', '/championships/generate-groups']:
        (new ChampionshipController($pdo))->generateGroups();
        break;
    case ['GET', '/institution']:
        (new InstitutionController($pdo))->edit();
        break;
    case ['POST', '/institution']:
        (new InstitutionController($pdo))->update();
        break;
    case ['GET', '/register']:
        (new PlayerController($pdo))->registerForm();
        break;
    case ['POST', '/register']:
        (new PlayerController($pdo))->store();
        break;
    case ['GET', '/players']:
        (new PlayerController($pdo))->index();
        break;
    case ['POST', '/players/ranking']:
        (new PlayerController($pdo))->updateRanking();
        break;
    case ['POST', '/matches/result']:
        (new MatchController($pdo))->storeResult();
        break;
    default:
        http_response_code(404);
        echo 'Ruta no encontrada';
}
