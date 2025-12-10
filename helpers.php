<?php

declare(strict_types=1);

/**
 * Simple JSON storage helpers for the demo environment.
 */
function storage_path(string $file): string
{
    return __DIR__ . '/database/' . $file;
}

function load_json(string $file): array
{
    $path = storage_path($file);
    if (!file_exists($path)) {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        file_put_contents($path, '[]', LOCK_EX);
        return [];
    }

    $json = file_get_contents($path);
    $data = json_decode($json, true);

    return is_array($data) ? $data : [];
}

function save_json(string $file, array $payload): bool
{
    $path = storage_path($file);
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $bytes = file_put_contents(
        $path,
        json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );

    return $bytes !== false;
}

function next_incremental_id(array $items, string $field = 'id'): int
{
    $current = 0;

    foreach ($items as $item) {
        if (isset($item[$field]) && is_numeric($item[$field])) {
            $current = max($current, (int) $item[$field]);
        }
    }

    return $current + 1;
}

function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        $_SESSION[$key] = $value;
        return null;
    }

    $message = $_SESSION[$key] ?? null;
    unset($_SESSION[$key]);

    return $message;
}

function find_by_id(array $items, string $id): ?array
{
    foreach ($items as $item) {
        if (($item['id'] ?? '') === $id) {
            return $item;
        }
    }

    return null;
}

