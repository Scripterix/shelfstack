<?php
declare(strict_types=1);

require_once __DIR__ . '/shared/connection.php';
require_once __DIR__ . '/shared/functions.php';

$jsonRequested = isset($_GET['useJSON']);

try {
    $offer = getConnection()->query(
        'SELECT o.bookTitle, c.catDesc, o.bookPrice
         FROM NBL_special_offers o
         INNER JOIN NBL_category c ON o.catID = c.catID
         ORDER BY RAND()
         LIMIT 1'
    )->fetch();

    if ($offer === false) {
        http_response_code(404);
        $offer = null;
    }

    if ($jsonRequested) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($offer ?? ['error' => 'Brak dostępnych ofert.'], JSON_THROW_ON_ERROR);
        exit;
    }

    header('Content-Type: text/html; charset=UTF-8');
    if ($offer === null) {
        echo '<p>Brak dostępnych ofert.</p>';
        exit;
    }

    echo '<div class="border rounded p-2 mb-3">'
        . '<strong>' . e($offer['bookTitle']) . '</strong><br>'
        . '<span>Kategoria: ' . e($offer['catDesc']) . '</span><br>'
        . '<span>Cena: ' . e(number_format((float) $offer['bookPrice'], 2)) . ' zł</span>'
        . '</div>';
} catch (PDOException) {
    http_response_code(503);
    if ($jsonRequested) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => 'Oferty są chwilowo niedostępne.']);
    } else {
        echo '<p>Oferty są chwilowo niedostępne.</p>';
    }
}
