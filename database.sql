CREATE DATABASE IF NOT EXISTS php_bookstore
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE php_bookstore;

CREATE TABLE IF NOT EXISTS NBL_category (
    catID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    catDesc VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS NBL_publisher (
    pubID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pubName VARCHAR(150) NOT NULL,
    location VARCHAR(150) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS NBL_books (
    bookISBN VARCHAR(20) PRIMARY KEY,
    bookTitle VARCHAR(200) NOT NULL,
    bookYear SMALLINT UNSIGNED NOT NULL,
    catID INT UNSIGNED NOT NULL,
    pubID INT UNSIGNED NOT NULL,
    bookPrice DECIMAL(10,2) UNSIGNED NOT NULL,
    CONSTRAINT fk_books_category FOREIGN KEY (catID) REFERENCES NBL_category(catID),
    CONSTRAINT fk_books_publisher FOREIGN KEY (pubID) REFERENCES NBL_publisher(pubID)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS NBL_special_offers (
    offerID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bookTitle VARCHAR(200) NOT NULL,
    catID INT UNSIGNED NOT NULL,
    bookPrice DECIMAL(10,2) UNSIGNED NOT NULL,
    CONSTRAINT fk_offers_category FOREIGN KEY (catID) REFERENCES NBL_category(catID)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS NBL_users (
    userID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    passwordHash VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

INSERT IGNORE INTO NBL_category (catID, catDesc) VALUES
    (1, 'Programming'),
    (2, 'Databases'),
    (3, 'Web development');

INSERT IGNORE INTO NBL_publisher (pubID, pubName, location) VALUES
    (1, 'O\'Reilly Media', 'Sebastopol'),
    (2, 'No Starch Press', 'San Francisco'),
    (3, 'Manning', 'Shelter Island');

INSERT IGNORE INTO NBL_books (bookISBN, bookTitle, bookYear, catID, pubID, bookPrice) VALUES
    ('9781492056812', 'Learning SQL', 2020, 2, 1, 39.99),
    ('9781718500945', 'The Linux Command Line', 2021, 1, 2, 34.99),
    ('9781617295959', 'PHP in Action', 2022, 3, 3, 44.90);

INSERT IGNORE INTO NBL_special_offers (offerID, bookTitle, catID, bookPrice) VALUES
    (1, 'Learning SQL', 2, 29.99),
    (2, 'The Linux Command Line', 1, 24.99);

INSERT INTO NBL_users (username, passwordHash)
VALUES ('Admin', '$2y$10$kAhkw1FxlBeiG.k1KH8EI.YNqfdYr3X/u7QbLrRgCE2skesQlo1py')
ON DUPLICATE KEY UPDATE
    username = VALUES(username),
    passwordHash = VALUES(passwordHash);

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nickname VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(120) NOT NULL,
    role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    status ENUM('ordered', 'preparing', 'ready') NOT NULL DEFAULT 'ordered',
    total_amount DECIMAL(10,2) UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    product_slug VARCHAR(160) NOT NULL,
    title VARCHAR(255) NOT NULL,
    isbn VARCHAR(30) NOT NULL,
    unit_price DECIMAL(10,2) UNSIGNED NOT NULL,
    quantity SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO users (id, nickname, display_name, role, password_hash) VALUES
    (1, 'User1', 'John Dee', 'customer', '$2y$10$53cj0ycboYjc4TTFv1q1u.L0sreUb/p7oDDs.tefvV1z3wJ6CjntW'),
    (2, 'User2', 'Alice Chain', 'customer', '$2y$10$.8GFexk5T7714kJ9F3YaKuj2q8HSssIWVXWpKaXS9gf5NyGhjEmNq'),
    (3, 'Admin', 'Administrator', 'admin', '$2y$10$kAhkw1FxlBeiG.k1KH8EI.YNqfdYr3X/u7QbLrRgCE2skesQlo1py')
ON DUPLICATE KEY UPDATE
    nickname = VALUES(nickname),
    display_name = VALUES(display_name),
    role = VALUES(role),
    password_hash = VALUES(password_hash);

INSERT INTO orders (id, user_id, status, total_amount, created_at) VALUES
    (1, 1, 'ordered', 143.90, '2026-10-15 18:30:00'),
    (2, 2, 'ordered', 99.00, '2026-10-16 11:15:00')
ON DUPLICATE KEY UPDATE
    user_id = VALUES(user_id),
    status = VALUES(status),
    total_amount = VALUES(total_amount),
    created_at = VALUES(created_at);

INSERT INTO order_items (id, order_id, product_slug, title, isbn, unit_price, quantity) VALUES
    (1, 1, 'php5-programowanie', 'PHP5. Programowanie', '978-83-246-0613-9', 64.90, 1),
    (2, 1, 'php-obiekty-wzorce-narzedzia', 'PHP. Obiekty, wzorce, narzędzia', '978-83-246-9178-4', 79.00, 1),
    (3, 2, 'php-mysql-tworzenie-stron-www', 'PHP i MySQL. Tworzenie stron WWW. Vademecum profesjonalisty', '83-7361-784-1', 99.00, 1)
ON DUPLICATE KEY UPDATE
    order_id = VALUES(order_id),
    product_slug = VALUES(product_slug),
    title = VALUES(title),
    isbn = VALUES(isbn),
    unit_price = VALUES(unit_price),
    quantity = VALUES(quantity);
