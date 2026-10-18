# ShelfStack

A local PHP 8.2 and MariaDB bookstore application built around books from a real developer's shelf. The legacy exercise is being replaced with an original customer storefront and administrator panel.

## Documentation

- [Local rebuild plan](docs/PLAN.md)
- [ADR-0001: ShelfStack architecture](docs/adr/0001-customer-and-admin-architecture.md)

## Run with XAMPP

1. Start **Apache** and **MySQL** in XAMPP.
2. Import `database.sql` in phpMyAdmin, or run from PowerShell:

   ```powershell
   Get-Content -Raw .\database.sql | C:\xampp\mysql\bin\mysql.exe -u root
   ```

3. Open <http://localhost/phpsandbox/php-login/>.

The local demo uses three fixed accounts and has no registration flow:

| Role | Nick | Name | Password |
| --- | --- | --- | --- |
| Customer | `User1` | John Dee | `JD1234#` |
| Customer | `User2` | Alice Chain | `AC1234#` |
| Administrator | `Admin` | Administrator | `Admin1234#` |

These credentials are intentionally visible in the interface because ShelfStack is a local exercise application. Customer orders are stored in MariaDB and displayed in the administrator dashboard.

## Database configuration

The XAMPP defaults are used automatically: database `php_bookstore`, user `root`, and an empty password. Override them with environment variables when needed:

- `BOOKSTORE_DB_HOST`
- `BOOKSTORE_DB_PORT`
- `BOOKSTORE_DB_NAME`
- `BOOKSTORE_DB_USER`
- `BOOKSTORE_DB_PASSWORD`
- `BOOKSTORE_BASE_PATH`
