<?php
session_start();

require_once __DIR__ . '/helpers.php';

const USER_STORE = 'users.json';
const HASH_OPTIONS = ['cost' => 12];

function availableRoles(): array
{
    return ['Visitante', 'Arbitro', 'Jugador', 'Administrador'];
}

function loadUsers(): array
{
    $path = storage_path(USER_STORE);
    $users = [];

    if (file_exists($path)) {
        $json = file_get_contents($path);
        $decoded = json_decode($json, true);
        $users = is_array($decoded) ? $decoded : [];
    }

    $users = ensureRootUser($users, availableRoles());
    saveUsers($users);

    return $users;
}

function saveUsers(array $users): void
{
    $path = storage_path(USER_STORE);
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    file_put_contents($path, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function ensureRootUser(array $users, array $roles): array
{
    $rootUsername = 'eisla';
    $rootEmail = 'eisla@local.test';
    $existing = array_filter($users, function ($user) use ($rootUsername, $rootEmail) {
        return ($user['username'] ?? '') === $rootUsername || ($user['email'] ?? '') === $rootEmail;
    });

    if (!empty($existing)) {
        return $users;
    }

    $users[] = [
        'id' => count($users) + 1,
        'nombre' => 'Super Administrador',
        'username' => $rootUsername,
        'email' => $rootEmail,
        'password_hash' => '$2y$12$V3vU5mR2SQeE/ABIBPyb/e.qcyA/p1WuelpOGEirv4lay4AzTphB6',
        'rol' => 'Administrador',
        'created_at' => date(DATE_ATOM),
    ];

    return $users;
}

function findUserById(array $users, int $id): ?array
{
    foreach ($users as $user) {
        if ((int) ($user['id'] ?? 0) === $id) {
            return $user;
        }
    }

    return null;
}

function findUserByIdentifier(array $users, string $identifier): ?array
{
    foreach ($users as $user) {
        if (strcasecmp($user['username'] ?? '', $identifier) === 0 || strcasecmp($user['email'] ?? '', $identifier) === 0) {
            return $user;
        }
    }

    return null;
}

// Si el archivo se incluye para reutilizar funciones, no ejecutar la lógica de routing.
if (basename(__FILE__) !== basename($_SERVER['SCRIPT_FILENAME'])) {
    return;
}

$action = $_POST['action'] ?? ($_GET['action'] ?? null);
$users = loadUsers();
$roles = availableRoles();

if ($action === 'logout') {
    session_destroy();
    header('Location: page-login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: page-login.php');
    exit;
}

if ($action === 'login') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($identifier === '' || $password === '') {
        $_SESSION['auth_error'] = 'Por favor ingresa tu usuario/correo y contraseña.';
        header('Location: page-login.php');
        exit;
    }

    $user = findUserByIdentifier($users, $identifier);
    if (!$user || !password_verify($password, $user['password_hash'])) {
        $_SESSION['auth_error'] = 'Credenciales inválidas. Verifica tus datos e intenta nuevamente.';
        header('Location: page-login.php');
        exit;
    }

    $_SESSION['user'] = [
        'id' => $user['id'],
        'nombre' => $user['nombre'] ?? $user['username'],
        'username' => $user['username'] ?? '',
        'email' => $user['email'] ?? '',
        'rol' => $user['rol'] ?? 'Visitante',
    ];

    $_SESSION['auth_success'] = 'Bienvenido de nuevo, ' . ($_SESSION['user']['nombre'] ?: $_SESSION['user']['username']) . '.';
    header('Location: panel-principal.php');
    exit;
}

if ($action === 'register') {
    $nombre = trim($_POST['nombre'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? '';

    if ($nombre === '' || $username === '' || $email === '' || $password === '' || $rol === '') {
        $_SESSION['auth_error'] = 'Todos los campos son obligatorios.';
        header('Location: page-register.php');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['auth_error'] = 'Ingresa un correo electrónico válido.';
        header('Location: page-register.php');
        exit;
    }

    if (!in_array($rol, $roles, true)) {
        $_SESSION['auth_error'] = 'Selecciona un tipo de usuario válido.';
        header('Location: page-register.php');
        exit;
    }

    foreach ($users as $user) {
        if (strcasecmp($user['username'] ?? '', $username) === 0) {
            $_SESSION['auth_error'] = 'El usuario ya existe, intenta con otro nombre de usuario.';
            header('Location: page-register.php');
            exit;
        }

        if (strcasecmp($user['email'] ?? '', $email) === 0) {
            $_SESSION['auth_error'] = 'Ya existe un registro con ese correo electrónico.';
            header('Location: page-register.php');
            exit;
        }
    }

    $users[] = [
        'id' => count($users) + 1,
        'nombre' => $nombre,
        'username' => $username,
        'email' => $email,
        'password_hash' => password_hash($password, PASSWORD_BCRYPT, HASH_OPTIONS),
        'rol' => $rol,
        'created_at' => date(DATE_ATOM),
    ];

    saveUsers($users);

    $_SESSION['auth_success'] = 'Usuario creado correctamente. Ya puedes iniciar sesión.';
    header('Location: page-login.php');
    exit;
}

$_SESSION['auth_error'] = 'Acción no válida.';
header('Location: page-login.php');
exit;
