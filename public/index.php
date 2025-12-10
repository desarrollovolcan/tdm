<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/ChampionshipController.php';
require_once __DIR__ . '/../app/controllers/PlayerController.php';
require_once __DIR__ . '/../app/controllers/MatchController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($uri === '/' && isset($_SESSION['user'])) {
    $controller = new DashboardController($pdo);
    $controller->index();
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
