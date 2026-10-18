<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/cart.php';

start_app_session();

function display_header(string $title): string
{
    $pageTitle = e($title === '' ? APP_NAME : $title . ' | ' . APP_NAME);
    $bootstrapUrl = e(app_url('css/bootstrap.css'));
    $stylesUrl = e(app_url('css/app.css'));
    $navigationScriptUrl = e(app_url('js/navigation.js'));

    return <<<HTML
<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="theme-color" content="#111827">
    <meta name="description" content="ShelfStack — lokalna księgarnia z książkami z półki programisty.">
    <title>{$pageTitle}</title>
    <link rel="stylesheet" href="{$bootstrapUrl}">
    <link rel="stylesheet" href="{$stylesUrl}">
    <script src="{$navigationScriptUrl}" defer></script>
</head>
<body>
HTML;
}

function display_footer(): string
{
    return display_footer_without_closing() . display_page_closings();
}

function display_footer_without_closing(): string
{
    $homeUrl = e(app_url());
    $year = date('Y');

    return <<<HTML
<footer class="site-footer">
    <div class="container footer-grid">
        <a class="footer-brand" href="{$homeUrl}">
            <span class="brand-mark" aria-hidden="true">{ }</span>
            <span>ShelfStack</span>
        </a>
        <p class="footer-copy">Książki z prawdziwej półki programisty.</p>
        <p class="footer-meta">PHP · MariaDB · localhost · {$year}</p>
    </div>
</footer>
HTML;
}

function display_page_closings(): string
{
    return "\n</body>\n</html>\n";
}

function nav_link(string $url, string $label, bool $active, ?string $badge = null): string
{
    $class = $active ? 'nav-link is-active' : 'nav-link';
    $current = $active ? ' aria-current="page"' : '';
    $badgeMarkup = $badge === null ? '' : '<span class="nav-badge">' . e($badge) . '</span>';
    return '<a class="' . $class . '" href="' . e($url) . '"' . $current . '>' . e($label) . $badgeMarkup . '</a>';
}

function display_navbar(): string
{
    $homeUrl = app_url();
    $shopUrl = app_url('shop.php');
    $cartUrl = app_url('cart.php');
    $adminUrl = app_url('admin/index.php');
    $requestPath = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '');
    $basePath = rtrim(app_base_path(), '/');

    $homeActive = rtrim($requestPath, '/') === $basePath || str_ends_with($requestPath, '/index.php');
    $shopActive = str_ends_with($requestPath, '/shop.php') || str_ends_with($requestPath, '/product.php');
    $cartActive = str_ends_with($requestPath, '/cart.php');
    $adminActive = str_contains($requestPath, '/admin/');

    $homeLink = nav_link($homeUrl, 'Strona główna', $homeActive);
    $shopLink = nav_link($shopUrl, 'Sklep', $shopActive);
    $cartLink = nav_link($cartUrl, 'Koszyk', $cartActive, (string) cart_count());
    $adminLink = '';

    if (check_session()) {
        if (is_admin()) {
            $adminLink = nav_link($adminUrl, 'Panel admina', $adminActive);
        }
        $logoutUrl = e(app_url('auth/logout.php'));
        $token = e(csrf_token());
        $username = e($_SESSION['username'] ?? 'Użytkownik');
        $authControl = <<<HTML
<span class="user-chip" title="Zalogowany użytkownik"><span class="status-dot" aria-hidden="true"></span>{$username}</span>
<form method="post" action="{$logoutUrl}" class="nav-form">
    <input type="hidden" name="csrf_token" value="{$token}">
    <button class="nav-button nav-button-ghost" type="submit">Wyloguj</button>
</form>
HTML;
    } else {
        $loginUrl = e(app_url('auth/login_form.php'));
        $authControl = '<a class="nav-button" href="' . $loginUrl . '">Zaloguj się <span aria-hidden="true">→</span></a>';
    }

    return <<<HTML
<header class="site-header">
    <nav class="container site-nav" aria-label="Główna nawigacja">
        <a class="site-brand" href="{$homeUrl}" aria-label="ShelfStack — strona główna">
            <span class="brand-mark" aria-hidden="true">{ }</span>
            <span class="brand-copy"><strong>ShelfStack</strong><small>lokalna księgarnia</small></span>
        </a>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-menu">
            <span class="sr-only">Otwórz nawigację</span>
            <span class="nav-toggle-lines" aria-hidden="true"><i></i><i></i><i></i></span>
        </button>
        <div class="nav-links" id="site-menu">
            <div class="nav-primary">{$homeLink}{$shopLink}{$cartLink}{$adminLink}</div>
            <div class="nav-account">{$authControl}</div>
        </div>
    </nav>
</header>
HTML;
}
