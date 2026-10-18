<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function getConnection(): PDO
{
    $host = env_value('BOOKSTORE_DB_HOST', '127.0.0.1');
    $port = env_value('BOOKSTORE_DB_PORT', '3306');
    $database = env_value('BOOKSTORE_DB_NAME', 'php_bookstore');
    $username = env_value('BOOKSTORE_DB_USER', 'root');
    $password = env_value('BOOKSTORE_DB_PASSWORD', '');

    $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

    return new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}
