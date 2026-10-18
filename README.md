# ShelfStack

ShelfStack is a **local bookstore demo** built with **PHP 8.2, MariaDB, HTML, CSS, and JavaScript**. It includes a customer-facing catalogue, book details, a session-based shopping cart, demo checkout, and an administrator dashboard.

The project is intended for **learning and portfolio demonstration**, not public deployment or real payments.

## Features

- Book catalogue with product pages and local cover images
- Shopping cart and demo orders stored in MariaDB
- Two customer accounts and one administrator account
- PHP sessions, role-based access checks, and CSRF protection on implemented forms
- Administrator overview of customers and orders, plus legacy book-editing screens
- Polish-language storefront and navigation

> **Current limitations:** The storefront catalogue and the legacy SQL book editor may use different data sources. The schema also contains both legacy and newer user tables. These are tracked in [Issue #1](https://github.com/Scripterix/shelfstack/issues/1) and [Issue #2](https://github.com/Scripterix/shelfstack/issues/2). Some planned features in `docs/PLAN.md` are not implemented.

## Run locally with XAMPP

**Prerequisites:** PHP 8.2-compatible XAMPP with Apache and MySQL/MariaDB.

1. Clone the repository into `C:\xampp\htdocs\phpsandbox\php-login` (or adjust the configured base path to your location).
2. Start **Apache** and **MySQL** in XAMPP.
3. Import `database.sql` in phpMyAdmin. Alternatively, from PowerShell in the project directory:

   ```powershell
   Get-Content -Raw .\database.sql | C:\xampp\mysql\bin\mysql.exe -u root
   ```

4. Open [http://localhost/phpsandbox/php-login/](http://localhost/phpsandbox/php-login/).
5. Sign in using one of the demo accounts below.

### Demo accounts

| Role | Username | Display name | Password |
| --- | --- | --- | --- |
| Customer | `User1` | John Dee | `JD1234#` |
| Customer | `User2` | Alice Chain | `AC1234#` |
| Administrator | `Admin` | Administrator | `Admin1234#` |

These credentials are **deliberately public test credentials**. The SQL seed stores password hashes, not plaintext passwords. **Do not expose this demo with unchanged credentials to the internet or reuse these passwords elsewhere.**

For a two-role test, open the customer session in one browser and the administrator session in another browser or an isolated browser profile.

## Database configuration

The default local settings are database `php_bookstore`, user `root`, and an empty password (as commonly used in XAMPP). The application supports these environment variables:

- `BOOKSTORE_DB_HOST`
- `BOOKSTORE_DB_PORT`
- `BOOKSTORE_DB_NAME`
- `BOOKSTORE_DB_USER`
- `BOOKSTORE_DB_PASSWORD`
- `BOOKSTORE_BASE_PATH`

These defaults are for **local development only**.

## Project notes

- [Rebuild plan (Polish)](docs/PLAN.md) — original scope and roadmap; **not a checklist of completed features**.
- [Architecture decision record (Polish)](docs/adr/0001-customer-and-admin-architecture.md) — intended direction, not necessarily the final implementation.
- [Open issues](https://github.com/Scripterix/shelfstack/issues) — known inconsistencies and follow-up work.

## Origins and attribution

ShelfStack is a substantially redesigned and extended derivative of the educational [PHP_Bookstore project by automatasz](https://github.com/automatasz/PHP_Bookstore). The original project supplied the starting code; ShelfStack adds its own storefront, product presentation, cart, orders, UI, and administrative work.

## License

This repository includes an [MIT license](LICENSE) for contributions whose rights are held by the ShelfStack author. **The original PHP_Bookstore repository does not declare a project-wide license**, so this MIT notice should not be interpreted as granting rights to third-party code retained from that project. Review upstream permissions before redistributing or commercially reusing inherited portions.
