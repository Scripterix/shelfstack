<?php
declare(strict_types=1);

require_once __DIR__ . '/connection.php';

/** @param array<int, array<string, mixed>> $items */
function create_order(int $userId, array $items): int
{
    if ($items === []) {
        throw new InvalidArgumentException('Cannot create an empty order.');
    }

    $total = array_reduce(
        $items,
        static fn (float $sum, array $item): float => $sum + ((float) $item['price'] * (int) ($item['quantity'] ?? 1)),
        0.0
    );

    $connection = getConnection();
    $connection->beginTransaction();

    try {
        $orderStatement = $connection->prepare(
            'INSERT INTO orders (user_id, status, total_amount) VALUES (:user_id, :status, :total_amount)'
        );
        $orderStatement->execute([
            'user_id' => $userId,
            'status' => 'ordered',
            'total_amount' => number_format($total, 2, '.', ''),
        ]);

        $orderId = (int) $connection->lastInsertId();
        $itemStatement = $connection->prepare(
            'INSERT INTO order_items
                (order_id, product_slug, title, isbn, unit_price, quantity)
             VALUES
                (:order_id, :product_slug, :title, :isbn, :unit_price, :quantity)'
        );

        foreach ($items as $item) {
            $itemStatement->execute([
                'order_id' => $orderId,
                'product_slug' => $item['slug'],
                'title' => $item['title'],
                'isbn' => $item['isbn'],
                'unit_price' => number_format((float) $item['price'], 2, '.', ''),
                'quantity' => (int) ($item['quantity'] ?? 1),
            ]);
        }

        $connection->commit();
        return $orderId;
    } catch (Throwable $error) {
        if ($connection->inTransaction()) {
            $connection->rollBack();
        }
        throw $error;
    }
}

/** @return array<int, array<string, mixed>> */
function admin_orders(): array
{
    $connection = getConnection();
    $orders = $connection->query(
        "SELECT o.id, o.status, o.total_amount, o.created_at,
                u.id AS user_id, u.nickname, u.display_name
         FROM orders o
         INNER JOIN users u ON u.id = o.user_id
         ORDER BY o.created_at DESC, o.id DESC"
    )->fetchAll();

    if ($orders === []) {
        return [];
    }

    $itemStatement = $connection->prepare(
        'SELECT product_slug, title, isbn, unit_price, quantity
         FROM order_items
         WHERE order_id = :order_id
         ORDER BY id'
    );

    foreach ($orders as &$order) {
        $itemStatement->execute(['order_id' => $order['id']]);
        $order['items'] = $itemStatement->fetchAll();
    }
    unset($order);

    return $orders;
}

/** @return array<int, array<string, mixed>> */
function customer_summaries(): array
{
    return getConnection()->query(
        "SELECT u.id, u.nickname, u.display_name,
                COUNT(DISTINCT o.id) AS order_count,
                COALESCE(SUM(o.total_amount), 0) AS order_total
         FROM users u
         LEFT JOIN orders o ON o.user_id = u.id
         WHERE u.role = 'customer'
         GROUP BY u.id, u.nickname, u.display_name
         ORDER BY u.id"
    )->fetchAll();
}

