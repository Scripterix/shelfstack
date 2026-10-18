<?php
declare(strict_types=1);

require_once __DIR__ . '/shared/page_components.php';

$slug = trim((string) ($_GET['book'] ?? ''));
$book = find_book($slug);
$notice = pull_shop_notice();

if ($book === null) {
    http_response_code(404);
    echo display_header('Nie znaleziono książki');
    echo display_navbar();
    ?>
    <main class="container page-shell">
        <section class="empty-state">
            <span class="empty-state-code">404</span>
            <h1>Nie znaleźliśmy tej książki</h1>
            <p>Wróć do katalogu i wybierz jedną z pozycji dostępnych na półce.</p>
            <a class="button-primary" href="<?= e(app_url('shop.php')) ?>">Wróć do sklepu</a>
        </section>
    </main>
    <?= display_footer() ?>
    <?php
    exit;
}

$authors = implode(', ', $book['authors']);
$token = csrf_token();

echo display_header((string) $book['title']);
echo display_navbar();
?>
<main class="container page-shell product-page">
    <a class="back-link" href="<?= e(app_url('shop.php')) ?>">← Wróć do sklepu</a>

    <?php if ($notice !== null): ?>
        <div class="shop-alert shop-alert-<?= e((string) $notice['type']) ?>" role="status">
            <?= e((string) $notice['message']) ?>
            <?php if (($notice['type'] ?? '') === 'success'): ?>
                <a href="<?= e(app_url('cart.php')) ?>">Przejdź do koszyka →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <article class="product-detail">
        <div class="product-gallery">
            <figure>
                <img src="<?= e(app_url((string) $book['cover'])) ?>" alt="Przód książki <?= e($book['title']) ?>">
                <figcaption>Okładka</figcaption>
            </figure>
            <figure>
                <img src="<?= e(app_url((string) $book['back_cover'])) ?>" alt="Tył książki <?= e($book['title']) ?>" loading="lazy">
                <figcaption>Rewers</figcaption>
            </figure>
        </div>

        <div class="product-info">
            <span class="availability-badge availability-inline">Dostępna · 1 szt.</span>
            <p class="product-category"><?= e($book['category']) ?></p>
            <h1><?= e($book['title']) ?></h1>
            <?php if ($book['edition'] !== ''): ?><p class="product-edition"><?= e($book['edition']) ?></p><?php endif; ?>
            <p class="product-detail-authors"><?= e($authors) ?></p>
            <p class="product-detail-description"><?= e($book['description']) ?></p>

            <div class="product-buy-box">
                <strong class="product-detail-price"><?= e(format_price((float) $book['price'])) ?></strong>
                <form method="post" action="<?= e(app_url('cart.php')) ?>">
                    <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="book" value="<?= e($book['slug']) ?>">
                    <input type="hidden" name="return_to" value="product:<?= e($book['slug']) ?>">
                    <button class="button-primary" type="submit">Dodaj do koszyka</button>
                </form>
            </div>

            <dl class="product-meta">
                <div><dt>Autorzy</dt><dd><?= e($authors) ?></dd></div>
                <div><dt>Wydawnictwo</dt><dd><?= e($book['publisher']) ?></dd></div>
                <div><dt>ISBN</dt><dd><?= e($book['isbn']) ?></dd></div>
                <div><dt>Kategoria</dt><dd><?= e($book['category']) ?></dd></div>
            </dl>

            <div class="tag-list" aria-label="Tagi">
                <?php foreach ($book['tags'] as $tag): ?><span><?= e($tag) ?></span><?php endforeach; ?>
            </div>
        </div>
    </article>
</main>
<?= display_footer() ?>

