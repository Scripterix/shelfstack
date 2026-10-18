<?php
declare(strict_types=1);

require_once __DIR__ . '/../shared/page_components.php';
require_once __DIR__ . '/../shared/connection.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . app_url('admin/book_list.php'));
    exit;
}

$input = [
    'isbn' => $_POST['isbn'] ?? '',
    'title' => $_POST['title'] ?? '',
    'year' => $_POST['year'] ?? '',
    'price' => $_POST['price'] ?? '',
    'category' => $_POST['category'] ?? '',
    'publisher' => $_POST['publisher'] ?? '',
];
$errors = [];

if (!csrf_is_valid($_POST['csrf_token'] ?? null)) {
    $errors[] = 'Your session expired. Please submit the form again.';
}

validate_generic_data($input, $errors);

$year = filter_var($input['year'], FILTER_VALIDATE_INT);
$price = filter_var($input['price'], FILTER_VALIDATE_FLOAT);
$category = filter_var($input['category'], FILTER_VALIDATE_INT);
$publisher = filter_var($input['publisher'], FILTER_VALIDATE_INT);

if ($year === false || $year < 1000 || $year > ((int) date('Y') + 1)) {
    $errors[] = 'Enter a valid publication year.';
}
if ($price === false || $price < 0) {
    $errors[] = 'Enter a valid non-negative price.';
}
if (mb_strlen($input['isbn']) > 20) {
    $errors[] = 'The ISBN is too long.';
}

if ($errors === []) {
    try {
        $connection = getConnection();

        $categoryCheck = $connection->prepare('SELECT 1 FROM NBL_category WHERE catID = :id');
        $categoryCheck->execute(['id' => $category]);
        $publisherCheck = $connection->prepare('SELECT 1 FROM NBL_publisher WHERE pubID = :id');
        $publisherCheck->execute(['id' => $publisher]);

        if (!$categoryCheck->fetchColumn()) {
            $errors[] = 'The selected category does not exist.';
        }
        if (!$publisherCheck->fetchColumn()) {
            $errors[] = 'The selected publisher does not exist.';
        }

        if ($errors === []) {
            $statement = $connection->prepare(
                'UPDATE NBL_books
                 SET bookTitle = :title, bookYear = :year, bookPrice = :price,
                     catID = :category, pubID = :publisher
                 WHERE bookISBN = :isbn'
            );
            $statement->execute([
                'title' => $input['title'],
                'year' => $year,
                'price' => number_format((float) $price, 2, '.', ''),
                'category' => $category,
                'publisher' => $publisher,
                'isbn' => $input['isbn'],
            ]);

            if ($statement->rowCount() === 0) {
                $exists = $connection->prepare('SELECT 1 FROM NBL_books WHERE bookISBN = :isbn');
                $exists->execute(['isbn' => $input['isbn']]);
                if (!$exists->fetchColumn()) {
                    $errors[] = 'The requested book does not exist.';
                }
            }
        }
    } catch (PDOException) {
        http_response_code(500);
        $errors[] = 'Could not update the book in the database.';
    }
}

if ($errors === []) {
    header('Location: ' . app_url('admin/book_list.php?updated=1'));
    exit;
}

http_response_code(http_response_code() === 500 ? 500 : 422);
echo display_header('Update failed');
echo display_navbar();
echo error_container($errors, app_url('admin/book_edit.php?id=' . rawurlencode((string) $input['isbn'])));
echo display_footer();
