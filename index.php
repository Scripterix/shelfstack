<?php
declare(strict_types=1);

require_once __DIR__ . '/shared/page_components.php';

$shopUrl = e(app_url('shop.php'));
$cartUrl = e(app_url('cart.php'));
$loggedIn = check_session();
$admin = is_admin();
$accountUrl = e($admin ? app_url('admin/index.php') : ($loggedIn ? app_url('shop.php') : app_url('auth/login_form.php')));
$accountLabel = $admin ? 'Otwórz panel administratora' : ($loggedIn ? 'Przejdź do sklepu' : 'Zaloguj się');
$books = book_catalogue();
$flash = pull_flash_message();

echo display_header('Strona główna');
echo display_navbar();
?>
<main class="container page-shell">
    <?php if ($flash !== null): ?>
        <div class="alert alert-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div>
    <?php endif; ?>

    <section class="hero" aria-labelledby="hero-title">
        <div class="hero-copy">
            <span class="eyebrow">Lokalna księgarnia · napędzana przez PHP</span>
            <h1 id="hero-title">Twoja półka, skompilowana.</h1>
            <p class="hero-lead">ShelfStack zmienia prawdziwą półkę programisty w małą lokalną księgarnię. Obejrzyj trzy sfotografowane książki o PHP i tworzeniu stron internetowych.</p>
            <div class="hero-actions">
                <a class="button-primary" href="<?= $shopUrl ?>">Przeglądaj półkę <span aria-hidden="true">→</span></a>
                <a class="button-secondary" href="<?= $accountUrl ?>"><?= e($accountLabel) ?></a>
            </div>
        </div>
        <div class="hero-visual" aria-label="Podgląd projektu ShelfStack">
            <div class="terminal-card">
                <div class="terminal-bar" aria-hidden="true"><i></i><i></i><i></i></div>
                <pre class="terminal-code"><span class="code-muted">// zawartość lokalnej półki</span>
<span class="code-violet">const</span> shelf = {
  books: <span class="code-mint">"wybrane"</span>,
  stack: [<span class="code-mint">"PHP"</span>, <span class="code-mint">"MariaDB"</span>],
  status: <span class="code-mint">"gotowe"</span>
};</pre>
            </div>
        </div>
    </section>

    <div class="section-heading">
        <div>
            <span class="eyebrow">Odkrywaj</span>
            <h2>Jedna półka, dwie strony sklepu</h2>
        </div>
        <p>Strefa klienta i panel admina tworzą jedną spójną, lokalną aplikację.</p>
    </div>

    <section class="feature-grid" aria-label="Obszary ShelfStack">
        <a class="feature-card" href="<?= $shopUrl ?>">
            <span class="feature-icon" aria-hidden="true">01</span>
            <h3>Przeglądaj książki</h3>
            <p>Poznaj trzy prawdziwe książki, obejrzyj ich okładki i otwórz pełne karty produktów.</p>
            <span class="feature-arrow">Otwórz sklep →</span>
        </a>
        <a class="feature-card" href="<?= $accountUrl ?>">
            <span class="feature-icon" aria-hidden="true">02</span>
            <h3>Zarządzaj półką</h3>
            <p>Zaloguj się jako administrator, aby edytować tytuły, ceny i kategorie książek.</p>
            <span class="feature-arrow"><?= e($accountLabel) ?> →</span>
        </a>
        <a class="feature-card" href="<?= $cartUrl ?>">
            <span class="feature-icon" aria-hidden="true">03</span>
            <h3>Złóż zamówienie</h3>
            <p>Dodaj książki do koszyka sesyjnego i złóż demonstracyjne zamówienie bez płatności online.</p>
            <span class="feature-arrow">Otwórz koszyk →</span>
        </a>
    </section>

    <div class="section-heading">
        <div>
            <span class="eyebrow">Książki z naszej półki</span>
            <h2>Nasze książki</h2>
        </div>
        <p>Trzy książki o PHP i MySQL z lokalnymi zdjęciami okładek.</p>
    </div>

    <section class="product-grid" aria-label="Nasze książki">
        <?php foreach ($books as $book): ?>
            <?php
            $productUrl = app_url('product.php?book=' . rawurlencode((string) $book['slug']));
            $authors = implode(', ', $book['authors']);
            ?>
            <article class="product-card">
                <a class="product-cover" href="<?= e($productUrl) ?>" aria-label="Zobacz: <?= e($book['title']) ?>">
                    <img src="<?= e(app_url((string) $book['cover'])) ?>" alt="Okładka książki <?= e($book['title']) ?>" loading="lazy">
                    <span class="availability-badge">Dostępna</span>
                </a>
                <div class="product-card-body">
                    <p class="product-category"><?= e($book['category']) ?></p>
                    <h2><a href="<?= e($productUrl) ?>"><?= e($book['title']) ?></a></h2>
                    <p class="product-authors"><?= e($authors) ?></p>
                    <div class="product-card-footer">
                        <strong class="product-price"><?= e(format_price((float) $book['price'])) ?></strong>
                        <a class="button-secondary-light" href="<?= e($productUrl) ?>">Zobacz książkę</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
</main>
<?= display_footer() ?>
