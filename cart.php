<?php
declare(strict_types=1);

require_once __DIR__ . '/shared/page_components.php';
require_once __DIR__ . '/shared/orders.php';

function cart_return_url(string $returnTo, string $slug): string
{
    if ($returnTo === 'shop') {
        return app_url('shop.php');
    }

    if ($returnTo === 'product:' . $slug && find_book($slug) !== null) {
        return app_url('product.php?book=' . rawurlencode($slug));
    }

    return app_url('cart.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    $slug = trim((string) ($_POST['book'] ?? ''));
    $returnTo = (string) ($_POST['return_to'] ?? 'cart');

    if (!csrf_is_valid($_POST['csrf_token'] ?? null)) {
        set_shop_notice('error', 'Sesja formularza wygasła. Spróbuj ponownie.');
    } elseif ($action === 'add') {
        $book = find_book($slug);
        $result = add_book_to_cart($slug);

        if ($result === 'added' && $book !== null) {
            set_shop_notice('success', 'Dodano do koszyka: ' . $book['title'] . '.');
        } elseif ($result === 'already' && $book !== null) {
            set_shop_notice('info', 'Ta książka jest już w koszyku: ' . $book['title'] . '.');
        } else {
            set_shop_notice('error', 'Nie udało się dodać tej książki do koszyka.');
        }

        header('Location: ' . cart_return_url($returnTo, $slug));
        exit;
    } elseif ($action === 'remove') {
        $book = find_book($slug);
        remove_book_from_cart($slug);
        set_shop_notice('info', $book === null ? 'Usunięto pozycję z koszyka.' : 'Usunięto z koszyka: ' . $book['title'] . '.');
    } elseif ($action === 'checkout') {
        $orderedItems = cart_line_items();
        if ($orderedItems === []) {
            set_shop_notice('error', 'Koszyk jest pusty. Dodaj książkę przed złożeniem zamówienia.');
        } elseif (!check_session()) {
            start_app_session();
            $_SESSION['login_return'] = 'cart';
            set_shop_notice('info', 'Zaloguj się jako User1 lub User2, aby przypisać zamówienie do klienta.');
            header('Location: ' . app_url('auth/login_form.php'));
            exit;
        } elseif (!is_customer()) {
            set_shop_notice('error', 'Administrator nie składa zamówień. Użyj konta User1 lub User2.');
        } else {
            try {
                $orderId = create_order((int) current_user_id(), $orderedItems);
                clear_cart();
                $customer = (string) ($_SESSION['display_name'] ?? $_SESSION['username']);
                $message = count($orderedItems) === 1 ? 'Zamówiono książkę.' : 'Zamówiono książki.';
                set_shop_notice(
                    'ordered',
                    $message . ' Zamówienie #' . $orderId . ' zapisano dla ' . $customer . '. Płatność i dostawa wkrótce :-)',
                    $orderedItems
                );
            } catch (Throwable) {
                set_shop_notice('error', 'Nie udało się zapisać zamówienia. Spróbuj ponownie.');
            }
        }
    } else {
        set_shop_notice('error', 'Nie rozpoznano operacji koszyka.');
    }

    header('Location: ' . app_url('cart.php'));
    exit;
}

$notice = pull_shop_notice();
$items = cart_line_items();
$token = csrf_token();

echo display_header('Koszyk');
echo display_navbar();
?>
<main class="container page-shell cart-page">
    <header class="cart-header">
        <div>
            <span class="eyebrow">Twoje zamówienie</span>
            <h1>Koszyk</h1>
            <p>Sprawdź wybrane książki. Ten lokalny sklep nie pobiera płatności.</p>
        </div>
        <a class="button-secondary-light" href="<?= e(app_url('shop.php')) ?>">← Kontynuuj zakupy</a>
    </header>

    <?php if ($notice !== null): ?>
        <section class="shop-alert shop-alert-<?= e((string) $notice['type']) ?> order-notice" role="status">
            <strong><?= e((string) $notice['message']) ?></strong>
            <?php if (!empty($notice['items'])): ?>
                <ul>
                    <?php foreach ($notice['items'] as $orderedBook): ?>
                        <li>
                            <span><?= e($orderedBook['title']) ?></span>
                            <small>ISBN <?= e($orderedBook['isbn']) ?> · <?= e(format_price((float) $orderedBook['price'])) ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <?php if ($items === []): ?>
        <section class="empty-state cart-empty">
            <span class="empty-state-code">[ ]</span>
            <h2>Twój koszyk jest pusty</h2>
            <p>Na półce czekają trzy książki o PHP i tworzeniu aplikacji.</p>
            <a class="button-primary" href="<?= e(app_url('shop.php')) ?>">Zobacz książki</a>
        </section>
    <?php else: ?>
        <div class="cart-layout">
            <section class="cart-items" aria-label="Pozycje w koszyku">
                <?php foreach ($items as $item): ?>
                    <article class="cart-item">
                        <a href="<?= e(app_url('product.php?book=' . rawurlencode((string) $item['slug']))) ?>">
                            <img src="<?= e(app_url((string) $item['cover'])) ?>" alt="Okładka <?= e($item['title']) ?>">
                        </a>
                        <div class="cart-item-copy">
                            <p class="product-category"><?= e($item['category']) ?></p>
                            <h2><a href="<?= e(app_url('product.php?book=' . rawurlencode((string) $item['slug']))) ?>"><?= e($item['title']) ?></a></h2>
                            <p><?= e(implode(', ', $item['authors'])) ?></p>
                            <small>ISBN <?= e($item['isbn']) ?></small>
                        </div>
                        <div class="cart-item-actions">
                            <strong><?= e(format_price((float) $item['price'])) ?></strong>
                            <form method="post">
                                <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="book" value="<?= e($item['slug']) ?>">
                                <button class="text-button" type="submit">Usuń</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>

            <aside class="cart-summary">
                <span class="eyebrow">Podsumowanie</span>
                <div class="summary-row"><span>Liczba książek</span><strong><?= cart_count() ?></strong></div>
                <div class="summary-row summary-total"><span>Razem</span><strong><?= e(format_price(cart_total())) ?></strong></div>
                <p>Zamówienie zostanie przypisane do zalogowanego klienta. Płatność i sposób dostawy dodamy w kolejnej wersji.</p>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
                    <input type="hidden" name="action" value="checkout">
                    <button class="button-primary checkout-button" type="submit"><?= is_customer() ? 'Zamawiam' : 'Zaloguj się i zamów' ?></button>
                </form>
            </aside>
        </div>
    <?php endif; ?>
</main>
<?= display_footer() ?>

