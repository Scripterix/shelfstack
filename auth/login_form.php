<?php
declare(strict_types=1);

require_once __DIR__ . '/../shared/page_components.php';
require_once __DIR__ . '/../shared/demo_accounts.php';

if (check_session()) {
    $target = is_admin() ? app_url('admin/index.php') : app_url('shop.php');
    header('Location: ' . $target);
    exit;
}

$actionUrl = app_url('auth/login_process.php');
$token = csrf_token();
$accounts = demo_accounts();

echo display_header('Logowanie');
echo display_navbar();
?>
<main class="container page-shell login-layout">
    <section class="auth-card" aria-labelledby="login-title">
        <span class="auth-icon" aria-hidden="true">login</span>
        <span class="eyebrow">ShelfStack demo</span>
        <h1 id="login-title">Zaloguj się</h1>
        <p class="auth-intro">Wybierz jedno ze stałych kont ćwiczeniowych. Rejestracja nie jest potrzebna.</p>
        <form method="post" action="<?= e($actionUrl) ?>">
            <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
            <div class="form-group">
                <label for="username">Nick</label>
                <input id="username" class="form-control" type="text" name="username" autocomplete="username" required>
            </div>
            <div class="form-group">
                <label for="password">Hasło</label>
                <input id="password" class="form-control" type="password" name="password" autocomplete="current-password" required>
            </div>
            <div class="auth-actions">
                <button class="button-primary" type="submit">Zaloguj się →</button>
                <a href="<?= e(app_url()) ?>" class="button-secondary-light">Wróć na stronę</a>
            </div>
        </form>
        <p class="auth-note">Wyłącznie lokalne konta demonstracyjne</p>
    </section>

    <section class="demo-accounts" aria-labelledby="demo-accounts-title">
        <div class="section-heading compact-heading">
            <div>
                <span class="eyebrow">Bez rejestracji</span>
                <h2 id="demo-accounts-title">Dane do logowania</h2>
            </div>
        </div>
        <div class="demo-account-grid">
            <?php foreach ($accounts as $account): ?>
                <article class="demo-account-card <?= $account['role'] === 'admin' ? 'demo-account-admin' : '' ?>">
                    <span class="account-role"><?= $account['role'] === 'admin' ? 'Administrator' : 'Klient' ?></span>
                    <h3><?= e($account['display_name']) ?></h3>
                    <dl>
                        <div><dt>Nick</dt><dd><code><?= e($account['nickname']) ?></code></dd></div>
                        <div><dt>Hasło</dt><dd><code><?= e($account['password']) ?></code></dd></div>
                    </dl>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?= display_footer() ?>

