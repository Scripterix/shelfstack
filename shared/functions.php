<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function start_app_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
        'path' => '/',
    ]);
    session_start();
}

function check_session(): bool
{
    start_app_session();
    return ($_SESSION['logged-in'] ?? false) === true
        && isset($_SESSION['user_id'], $_SESSION['role'], $_SESSION['username'])
        && in_array($_SESSION['role'], ['customer', 'admin'], true);
}

function is_admin(): bool
{
    return check_session() && ($_SESSION['role'] ?? '') === 'admin';
}

function is_customer(): bool
{
    return check_session() && ($_SESSION['role'] ?? '') === 'customer';
}

function current_user_id(): ?int
{
    return check_session() ? (int) $_SESSION['user_id'] : null;
}

function require_login(): void
{
    if (!check_session()) {
        header('Location: ' . app_url('auth/login_form.php'));
        exit;
    }
}

function require_admin(): void
{
    if (!is_admin()) {
        if (check_session()) {
            header('Location: ' . app_url('shop.php'));
            exit;
        }

        header('Location: ' . app_url('auth/login_form.php'));
        exit;
    }
}

function set_flash_message(string $type, string $message): void
{
    start_app_session();
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message,
    ];
}

/** @return array{type: string, message: string}|null */
function pull_flash_message(): ?array
{
    start_app_session();
    $flash = $_SESSION['flash_message'] ?? null;
    unset($_SESSION['flash_message']);

    if (!is_array($flash) || !isset($flash['type'], $flash['message'])) {
        return null;
    }

    return [
        'type' => (string) $flash['type'],
        'message' => (string) $flash['message'],
    ];
}

function csrf_token(): string
{
    start_app_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_is_valid(?string $token): bool
{
    start_app_session();
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function error_container(array $errors, ?string $backUrl = null): string
{
    $items = '';
    foreach ($errors as $error) {
        $message = $error instanceof Throwable ? $error->getMessage() : (string) $error;
        $items .= '<li>' . e($message) . "</li>\n";
    }

    $backUrl ??= app_url();

    return '<div class="container mt-5">'
        . '<div class="alert alert-danger" role="alert">'
        . '<h2 class="h4">Something went wrong</h2><ul class="mb-3">' . $items . '</ul>'
        . '<a class="btn btn-dark" href="' . e($backUrl) . '">Go back</a>'
        . '</div></div>' . "\n";
}

function validate_generic_data(array &$data, array &$errors): void
{
    foreach ($data as $key => $value) {
        $value = trim((string) $value);
        if ($value === '') {
            $errors[] = ucfirst($key) . ' is required.';
        } elseif (mb_strlen($value) > 200) {
            $errors[] = ucfirst($key) . ' is too long.';
        }
        $data[$key] = $value;
    }
}
