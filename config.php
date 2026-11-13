<?php
declare(strict_types=1);

// Load .env manually so the project does not require Composer.
$envFile = dirname(__DIR__) . '/.env';
if (is_file($envFile) && is_readable($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim(trim($value), "\"'");
        if ($name !== '' && getenv($name) === false) {
            putenv($name . '=' . $value);
            $_ENV[$name] = $value;
        }
    }
}

define('NEWS_API_KEY', (string)(getenv('NEWS_API_KEY') ?: ''));
define('NEWS_API_BASE_URL', 'https://newsapi.org/v2');
define('NEWS_API_COUNTRY', (string)(getenv('NEWS_API_COUNTRY') ?: 'us'));
