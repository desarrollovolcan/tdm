<?php

declare(strict_types=1);

function db_config(): array
{
    return [
        'host' => 'localhost',
        'user' => 'root',
        'pass' => '',
        'name' => 'tdm',
    ];
}

function db(): ?mysqli
{
    static $conn = null;
    static $failed = false;

    if ($failed) {
        return null;
    }

    if ($conn instanceof mysqli) {
        return $conn;
    }

    $cfg = db_config();
    $conn = @new mysqli($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['name']);

    if ($conn->connect_errno) {
        $failed = true;
        return null;
    }

    $conn->set_charset('utf8mb4');
    $conn->query(
        "CREATE TABLE IF NOT EXISTS json_storage (
            file VARCHAR(190) PRIMARY KEY,
            payload LONGTEXT NOT NULL,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );

    return $conn;
}

function db_status(): array
{
    $cfg = db_config();
    $connection = db();

    if ($connection instanceof mysqli) {
        return [
            'connected' => true,
            'message' => 'Conectado a MySQL',
            'database' => $cfg['name'],
            'config_file' => __FILE__,
        ];
    }

    return [
        'connected' => false,
        'message' => 'Sin conexión a la base de datos',
        'database' => $cfg['name'],
        'config_file' => __FILE__,
    ];
}
