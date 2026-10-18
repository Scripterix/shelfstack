<?php
declare(strict_types=1);

require_once __DIR__ . '/../shared/page_components.php';
require_once __DIR__ . '/../shared/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('auth/login_form.php'));
    exit;
}

$username = trim((string) ($_POST['username'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$errors = [];

if (!csrf_is_valid($_POST['csrf_token'] ?? null)) {
    $errors[] = 'Sesja formularza wygasła. Spróbuj ponownie.';
}
if ($username === '' || $password === '') {
    $errors[] = 'Nick i hasło są wymagane.';
}

$user = null;
if ($errors === []) {
    try {
        $statement = getConnection()->prepare(
            'SELECT id, nickname, display_name, role, password_hash
             FROM users
             WHERE nickname = :nickname
             LIMIT 1'
        );
        $statement->execute(['nickname' => $username]);
        $candidate = $statement->fetch();

        if (!is_array($candidate) || !password_verify($password, (string) $candidate['password_hash'])) {
            $errors[] = 'Nieprawidłowy nick lub hasło.';
        } else {
            $user = $candidate;
        }
    } catch (PDOException) {
        http_response_code(500);
        $errors[] = 'Baza danych jest niedostępna. Sprawdź lokalną konfigurację.';
    }
}

if ($errors === [] && $user !== null) {
    start_app_session();
    session_regenerate_id(true);
    $_SESSION['logged-in'] = true;
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['username'] = (string) $user['nickname'];
    $_SESSION['display_name'] = (string) $user['display_name'];
    $_SESSION['role'] = (string) $user['role'];

    $returnTo = (string) ($_SESSION['login_return'] ?? '');
    unset($_SESSION['login_return']);

    if ($user['role'] === 'admin') {
        $target = app_url('admin/index.php');
    } elseif ($returnTo === 'cart') {
        $target = app_url('cart.php');
    } else {
        $target = app_url('shop.php');
    }

    header('Location: ' . $target);
    exit;
}

http_response_code(http_response_code() === 500 ? 500 : 422);
echo display_header('Logowanie nieudane');
echo display_navbar();
echo error_container($errors, app_url('auth/login_form.php'));
echo display_footer();

