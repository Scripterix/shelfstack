<?php
declare(strict_types=1);

require_once __DIR__ . '/../shared/page_components.php';
require_once __DIR__ . '/../shared/orders.php';
require_once __DIR__ . '/../shared/demo_accounts.php';

require_admin();

$orders = [];
$customers = [];
$errors = [];

try {
    $orders = admin_orders();
    $customers = customer_summaries();
} catch (PDOException) {
    http_response_code(500);
    $errors[] = 'Nie udało się wczytać zamówień i kont klientów.';
}

$credentials = demo_accounts();
$ordersByUser = [];
$revenue = 0.0;
foreach ($orders as $order) {
    $ordersByUser[(int) $order['user_id']][] = $order;
    $revenue += (float) $order['total_amount'];
}

echo display_header('Panel administratora');
echo display_navbar();
?>
<main class="container page-shell admin-page">
    <header class="admin-hero">
        <div>
            <span class="eyebrow">ShelfStack control room</span>
            <h1>Panel administratora</h1>
            <p>Zamówienia oraz dwa stałe konta klientów w jednym widoku.</p>
        </div>
        <div class="admin-identity">
            <span>Zalogowano jako</span>
            <strong><?= e($_SESSION['username'] ?? 'Admin') ?></strong>
        </div>
    </header>

    <nav class="admin-subnav" aria-label="Sekcje panelu">
        <a href="#overview">Podsumowanie</a>
        <a href="#customers">Konta klientów</a>
        <a href="#orders">Zamówienia</a>
        <a href="<?= e(app_url('shop.php')) ?>">Zobacz sklep ↗</a>
    </nav>

    <?php if ($errors !== []): ?>
        <?= error_container($errors, app_url()) ?>
    <?php else: ?>
        <section id="overview" class="admin-stats" aria-label="Podsumowanie sklepu">
            <article><span>Klienci demo</span><strong><?= count($customers) ?></strong><small>User1 + User2</small></article>
            <article><span>Zamówienia</span><strong><?= count($orders) ?></strong><small>Zapisane w MariaDB</small></article>
            <article><span>Książki</span><strong><?= count(book_catalogue()) ?></strong><small>Na lokalnej półce</small></article>
            <article><span>Wartość zamówień</span><strong><?= e(format_price($revenue)) ?></strong><small>Bez płatności online</small></article>
        </section>

        <section id="customers" class="admin-section">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Konta ćwiczeniowe</span>
                    <h2>Dwaj klienci</h2>
                </div>
                <p>Dane są jawne celowo — aplikacja działa lokalnie i nie ma rejestracji.</p>
            </div>

            <div class="admin-customer-grid">
                <?php foreach ($customers as $customer): ?>
                    <?php
                    $account = $credentials[$customer['nickname']] ?? null;
                    $customerOrders = $ordersByUser[(int) $customer['id']] ?? [];
                    ?>
                    <article class="admin-customer-card">
                        <header>
                            <span class="customer-avatar" aria-hidden="true"><?= e(strtoupper(substr((string) $customer['display_name'], 0, 1))) ?></span>
                            <div><p><?= e($customer['nickname']) ?></p><h3><?= e($customer['display_name']) ?></h3></div>
                        </header>
                        <dl class="credential-list">
                            <div><dt>Nick</dt><dd><code><?= e($account['nickname'] ?? $customer['nickname']) ?></code></dd></div>
                            <div><dt>Hasło</dt><dd><code><?= e($account['password'] ?? '—') ?></code></dd></div>
                            <div><dt>Zamówienia</dt><dd><?= e($customer['order_count']) ?></dd></div>
                            <div><dt>Łącznie</dt><dd><?= e(format_price((float) $customer['order_total'])) ?></dd></div>
                        </dl>
                        <div class="customer-order-preview">
                            <strong>Co zamówił użytkownik</strong>
                            <?php if ($customerOrders === []): ?>
                                <p>Brak zamówień.</p>
                            <?php else: ?>
                                <ul>
                                    <?php foreach ($customerOrders as $order): ?>
                                        <?php foreach ($order['items'] as $item): ?>
                                            <li><?= e($item['title']) ?></li>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="orders" class="admin-section">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Historia</span>
                    <h2>Zamówienia klientów</h2>
                </div>
                <p>Każde nowe zamówienie User1 lub User2 pojawi się tutaj automatycznie.</p>
            </div>

            <?php if ($orders === []): ?>
                <div class="empty-state"><h3>Brak zamówień</h3><p>Zaloguj się jako klient i złóż pierwsze zamówienie.</p></div>
            <?php else: ?>
                <div class="admin-order-list">
                    <?php foreach ($orders as $order): ?>
                        <article class="admin-order-card">
                            <header>
                                <div>
                                    <span class="order-number">Zamówienie #<?= e($order['id']) ?></span>
                                    <h3><?= e($order['display_name']) ?> <small>@<?= e($order['nickname']) ?></small></h3>
                                </div>
                                <div class="order-card-meta">
                                    <span class="order-status">Zamówiono</span>
                                    <time datetime="<?= e($order['created_at']) ?>"><?= e(date('d.m.Y, H:i', strtotime((string) $order['created_at']))) ?></time>
                                </div>
                            </header>
                            <ul>
                                <?php foreach ($order['items'] as $item): ?>
                                    <li>
                                        <div><strong><?= e($item['title']) ?></strong><small>ISBN <?= e($item['isbn']) ?></small></div>
                                        <span><?= e($item['quantity']) ?> × <?= e(format_price((float) $item['unit_price'])) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <footer><span>Razem</span><strong><?= e(format_price((float) $order['total_amount'])) ?></strong></footer>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</main>
<?= display_footer() ?>

