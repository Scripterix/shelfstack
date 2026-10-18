<?php
declare(strict_types=1);

require_once __DIR__ . '/../shared/page_components.php';
require_once __DIR__ . '/../shared/connection.php';

require_admin();

$isbn = trim((string) ($_GET['id'] ?? ''));
$book = null;
$categories = [];
$publishers = [];
$errors = [];

if ($isbn === '' || mb_strlen($isbn) > 20) {
    $errors[] = 'A valid book ISBN is required.';
} else {
    try {
        $connection = getConnection();

        $statement = $connection->prepare('SELECT * FROM NBL_books WHERE bookISBN = :isbn LIMIT 1');
        $statement->execute(['isbn' => $isbn]);
        $book = $statement->fetch();

        if ($book === false) {
            $book = null;
            $errors[] = 'The requested book does not exist.';
        } else {
            $categories = $connection->query('SELECT catID, catDesc FROM NBL_category ORDER BY catDesc')->fetchAll();
            $publishers = $connection->query('SELECT pubID, pubName, location FROM NBL_publisher ORDER BY pubName')->fetchAll();
        }
    } catch (PDOException) {
        http_response_code(500);
        $errors[] = 'Could not load the book from the database.';
    }
}

echo display_header('Edit a book');
echo display_navbar();

if ($errors !== [] || $book === null) {
    echo error_container($errors, app_url('admin/book_list.php'));
    echo display_footer();
    exit;
}

$updateUrl = e(app_url('admin/book_update.php'));
$listUrl = e(app_url('admin/book_list.php'));
$token = e(csrf_token());
?>
<main class="container mt-5">
    <div class="col-lg-6 col-md-8 offset-lg-3 offset-md-2">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h2">Edit a book</h1>
            <a class="btn btn-outline-secondary" href="<?= $listUrl ?>">Go back</a>
        </div>
        <form action="<?= $updateUrl ?>" method="post">
            <input type="hidden" name="csrf_token" value="<?= $token ?>">
            <div class="form-group">
                <label for="isbn">ISBN</label>
                <input id="isbn" class="form-control" name="isbn" readonly value="<?= e($book['bookISBN']) ?>">
            </div>
            <div class="form-group">
                <label for="title">Title</label>
                <input id="title" class="form-control" name="title" maxlength="200" required value="<?= e($book['bookTitle']) ?>">
            </div>
            <div class="form-group">
                <label for="year">Year</label>
                <input id="year" class="form-control" type="number" min="1000" max="<?= date('Y') + 1 ?>" name="year" required value="<?= e($book['bookYear']) ?>">
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" class="custom-select" name="category" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= e($category['catID']) ?>" <?= (string) $category['catID'] === (string) $book['catID'] ? 'selected' : '' ?>><?= e($category['catDesc']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="publisher">Publisher</label>
                <select id="publisher" class="custom-select" name="publisher" required>
                    <?php foreach ($publishers as $publisher): ?>
                        <option value="<?= e($publisher['pubID']) ?>" <?= (string) $publisher['pubID'] === (string) $book['pubID'] ? 'selected' : '' ?>><?= e($publisher['pubName'] . ' — ' . $publisher['location']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input id="price" class="form-control" type="number" step="0.01" min="0" name="price" required value="<?= e($book['bookPrice']) ?>">
            </div>
            <button class="btn btn-dark btn-block" type="submit">Update</button>
        </form>
    </div>
</main>
<?= display_footer() ?>
