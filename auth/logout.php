<?php
declare(strict_types=1);

require_once __DIR__ . '/../shared/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_is_valid($_POST['csrf_token'] ?? null)) {
    http_response_code(400);
    exit('Nieprawidłowe żądanie wylogowania.');
}

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $parameters = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires' => time() - 42000,
        'path' => $parameters['path'],
        'domain' => $parameters['domain'],
        'secure' => $parameters['secure'],
        'httponly' => $parameters['httponly'],
        'samesite' => $parameters['samesite'] ?? 'Lax',
    ]);
}
session_destroy();

session_id('');
start_app_session();
session_regenerate_id(true);
set_flash_message('success', 'Wylogowano pomyślnie.');

header('Location: ' . app_url(), true, 303);
exit;
