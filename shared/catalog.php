<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

/**
 * Local catalogue built from the books photographed for ShelfStack.
 *
 * @return array<string, array<string, mixed>>
 */
function book_catalogue(): array
{
    static $books;

    if ($books !== null) {
        return $books;
    }

    $books = [
        'php5-programowanie' => [
            'id' => 1,
            'slug' => 'php5-programowanie',
            'title' => 'PHP5. Programowanie',
            'edition' => '',
            'authors' => ['Rasmus Lerdorf', 'Kevin Tatroe', 'Peter MacIntyre'],
            'publisher' => "O'Reilly / Helion",
            'isbn' => '978-83-246-0613-9',
            'price' => 64.90,
            'category' => 'PHP',
            'tags' => ['PHP', 'backend', 'WWW', 'programowanie obiektowe'],
            'cover' => 'img/b-img/lerdorf-php-5-avers.jpg',
            'back_cover' => 'img/b-img/lerddorf-pph-5-revers.jpg',
            'short_description' => 'Kompleksowy podręcznik PHP 5 obejmujący składnię, OOP, bazy danych, XML i bezpieczeństwo aplikacji.',
            'description' => 'Książka przedstawia PHP 5 od podstaw języka aż po bardziej zaawansowane techniki tworzenia aplikacji internetowych. Omawia strukturę i składnię PHP, przetwarzanie tekstu, programowanie obiektowe, komunikację HTTP, bazy danych, XML, grafikę oraz bezpieczeństwo aplikacji.',
            'stock' => 1,
        ],
        'php-obiekty-wzorce-narzedzia' => [
            'id' => 2,
            'slug' => 'php-obiekty-wzorce-narzedzia',
            'title' => 'PHP. Obiekty, wzorce, narzędzia',
            'edition' => 'Wydanie IV',
            'authors' => ['Matt Zandstra'],
            'publisher' => 'Apress / Helion',
            'isbn' => '978-83-246-9178-4',
            'price' => 79.00,
            'category' => 'PHP / OOP',
            'tags' => ['PHP', 'OOP', 'wzorce projektowe', 'testy', 'software engineering'],
            'cover' => 'img/b-img/php-wzorce-avers.jpg',
            'back_cover' => 'img/b-img/php-wzorce-revers.jpg',
            'short_description' => 'Przewodnik po obiektowym PHP, wzorcach projektowych i narzędziach wspierających tworzenie większych aplikacji.',
            'description' => 'Książka koncentruje się na programowaniu obiektowym w PHP i projektowaniu dobrze zorganizowanych aplikacji. Omawia klasy i obiekty, wzorce projektowe, dobre praktyki, PEAR, PHPUnit, dokumentację oraz testy jednostkowe.',
            'stock' => 1,
        ],
        'php-mysql-tworzenie-stron-www' => [
            'id' => 3,
            'slug' => 'php-mysql-tworzenie-stron-www',
            'title' => 'PHP i MySQL. Tworzenie stron WWW. Vademecum profesjonalisty',
            'edition' => 'Wydanie trzecie',
            'authors' => ['Luke Welling', 'Laura Thomson'],
            'publisher' => 'Helion',
            'isbn' => '83-7361-784-1',
            'price' => 99.00,
            'category' => 'PHP / MySQL',
            'tags' => ['PHP', 'MySQL', 'backend', 'bazy danych', 'WWW'],
            'cover' => 'img/b-img/php-vademecum-avers.jpg',
            'back_cover' => 'img/b-img/php-vademecum-revers.jpg',
            'short_description' => 'Praktyczny podręcznik budowania dynamicznych stron WWW przy użyciu PHP i MySQL.',
            'description' => 'Obszerne vademecum tworzenia dynamicznych aplikacji internetowych w PHP i MySQL. Obejmuje PHP, programowanie obiektowe, projektowanie baz danych, formularze, operacje na danych, personalizację witryn i bezpieczeństwo aplikacji WWW.',
            'stock' => 1,
        ],
    ];

    return $books;
}

/** @return array<string, mixed>|null */
function find_book(string $slug): ?array
{
    return book_catalogue()[$slug] ?? null;
}

function format_price(float $price): string
{
    return number_format($price, 2, ',', ' ') . ' zł';
}

