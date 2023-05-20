<?php

// Load PHP configuration for tests
require_once __DIR__ . '/config.php';

// Define project root
define('PROJECT_ROOT', dirname(__DIR__));

/* Load .env configuration for testing */
$envDir = dirname(__DIR__);

if(file_exists($envDir . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($envDir);
    $dotenv->load();
}

if(! function_exists('env')) {
    function env(string $key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
}
