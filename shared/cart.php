<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/catalog.php';

/** @return array<string, int> */
function cart_contents(): array
{
    start_app_session();
    $cart = $_SESSION['cart'] ?? [];
    return is_array($cart) ? $cart : [];
}

function cart_count(): int
{
    return array_sum(cart_contents());
}

function add_book_to_cart(string $slug): string
{
    $book = find_book($slug);
    if ($book === null || (int) $book['stock'] < 1) {
        return 'missing';
    }

    start_app_session();
    if (isset($_SESSION['cart'][$slug])) {
        return 'already';
    }

    $_SESSION['cart'][$slug] = 1;
    return 'added';
}

function remove_book_from_cart(string $slug): void
{
    start_app_session();
    unset($_SESSION['cart'][$slug]);
}

function clear_cart(): void
{
    start_app_session();
    $_SESSION['cart'] = [];
}

/** @return array<int, array<string, mixed>> */
function cart_line_items(): array
{
    $items = [];
    foreach (cart_contents() as $slug => $quantity) {
        $book = find_book((string) $slug);
        if ($book === null || $quantity < 1) {
            continue;
        }

        $book['quantity'] = 1;
        $book['line_total'] = (float) $book['price'];
        $items[] = $book;
    }
    return $items;
}

function cart_total(): float
{
    return array_reduce(
        cart_line_items(),
        static fn (float $total, array $item): float => $total + (float) $item['line_total'],
        0.0
    );
}

/** @param array<int, array<string, mixed>> $items */
function set_shop_notice(string $type, string $message, array $items = []): void
{
    start_app_session();
    $_SESSION['shop_notice'] = [
        'type' => $type,
        'message' => $message,
        'items' => $items,
    ];
}

/** @return array<string, mixed>|null */
function pull_shop_notice(): ?array
{
    start_app_session();
    $notice = $_SESSION['shop_notice'] ?? null;
    unset($_SESSION['shop_notice']);
    return is_array($notice) ? $notice : null;
}

