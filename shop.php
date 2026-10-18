<?php
declare(strict_types=1);

require_once __DIR__ . '/shared/page_components.php';

$books = book_catalogue();
$notice = pull_shop_notice();
$token = csrf_token();

echo display_header('Sklep');
echo display_navbar();
?>
<main class="container page-shell shop-page">
    <header class="catalogue-header">
        <div>
            <span class="eyebrow">Książki z naszej półki</span>
            <h1>Sklep</h1>
            <p>Trzy konkretne książki o PHP, sfotografowane i opisane dla ShelfStack.</p>
        </div>
        <div class="catalogue-counter"><strong><?= count($books) ?></strong><span>książki w katalogu</span></div>
    </header>

    <?php if ($notice !== null): ?>
        <div class="shop-alert shop-alert-<?= e((string) $notice['type']) ?>" role="status">
            <?= e((string) $notice['message']) ?>
            <?php if (($notice['type'] ?? '') === 'success'): ?>
                <a href="<?= e(app_url('cart.php')) ?>">Przejdź do koszyka →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <section class="product-grid" aria-label="Katalog książek">
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
                    <p class="product-description"><?= e($book['short_description']) ?></p>
                    <div class="product-card-footer">
                        <strong class="product-price"><?= e(format_price((float) $book['price'])) ?></strong>
                        <div class="product-actions">
                            <a class="button-secondary-light" href="<?= e($productUrl) ?>">Karta produktu</a>
                            <form method="post" action="<?= e(app_url('cart.php')) ?>">
                                <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="book" value="<?= e($book['slug']) ?>">
                                <input type="hidden" name="return_to" value="shop">
                                <button class="button-primary" type="submit">Do koszyka</button>
                            </form>
                        </div>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
</main>
<?= display_footer() ?>

