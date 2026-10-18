<?php
declare(strict_types=1);

require_once __DIR__ . '/../shared/page_components.php';
require_once __DIR__ . '/../shared/connection.php';

require_admin();

$books = [];
$errors = [];

try {
    $statement = getConnection()->query(
        'SELECT b.bookISBN, b.bookTitle, b.bookYear, b.bookPrice, c.catDesc
         FROM NBL_books b
         INNER JOIN NBL_category c ON c.catID = b.catID
         ORDER BY b.bookTitle ASC'
    );
    $books = $statement->fetchAll();
} catch (PDOException) {
    http_response_code(500);
    $errors[] = 'Could not load books from the database.';
}

echo display_header('Book list');
echo display_navbar();
?>
<main class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1>Book list</h1>
            <p class="mb-0">Select a book to edit its details.</p>
        </div>
    </div>

    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success" role="status">The book was updated successfully.</div>
    <?php endif; ?>

    <?php if ($errors !== []): ?>
        <?= error_container($errors, app_url()) ?>
    <?php elseif ($books === []): ?>
        <div class="alert alert-info">No books were found.</div>
    <?php else: ?>
        <div class="table-responsive-sm">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Title</th>
                        <th scope="col">Year</th>
                        <th scope="col">Category</th>
                        <th scope="col">Price</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($books as $book): ?>
                    <?php $editUrl = app_url('admin/book_edit.php?id=' . rawurlencode((string) $book['bookISBN'])); ?>
                    <tr>
                        <td><a href="<?= e($editUrl) ?>"><?= e($book['bookTitle']) ?></a></td>
                        <td><?= e($book['bookYear']) ?></td>
                        <td><?= e($book['catDesc']) ?></td>
                        <td>£<?= e(number_format((float) $book['bookPrice'], 2)) ?></td>
                        <td><a class="btn btn-primary btn-sm" href="<?= e($editUrl) ?>">Edit</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>
<?= display_footer() ?>
