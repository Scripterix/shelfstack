# ADR-0001: Lokalna architektura ShelfStack

- Status: zaakceptowana
- Data: 2026-10-17
- Środowisko: lokalny XAMPP

## Kontekst

Istniejący projekt jest ćwiczeniem akademickim z nazwą NBL, starym schematem tabel i częściowo demonstracyjnym przepływem zamówienia. Naprawiona wersja działa lokalnie, lecz nie stanowi jeszcze docelowej aplikacji.

Nie planujemy publikowania projektu w internecie. Możemy swobodnie zastąpić stary kod, strukturę bazy, dane demonstracyjne i interfejs bez utrzymywania zgodności wstecznej.

## Decyzja

Budujemy własną lokalną aplikację **ShelfStack** jako jeden monolit PHP renderowany po stronie serwera. Będzie działała w XAMPP i korzystała z MariaDB. Istniejący kod służy jako materiał pomocniczy, a nie jako architektura, którą trzeba zachować.

### Struktura aplikacji

Aplikacja będzie miała publiczną część kliencką oraz chroniony katalog `/admin`. Wspólne elementy obejmą konfigurację, połączenie z bazą, sesję, autoryzację, ochronę CSRF, komunikaty i komponenty układu.

Nie tworzymy osobnego SPA ani REST API. PHP generuje widoki, a JavaScript obsługuje wyłącznie poprawę interakcji, która nie zastępuje logiki serwerowej.

### Czysty model danych

Stary prefiks `NBL_` zostanie usunięty. Lokalny schemat będzie tworzony od zera przez `database.sql` i obejmie tabele:

- `users`,
- `categories`,
- `publishers`,
- `books`,
- `orders`,
- `order_items`,
- `book_requests`.

Nie przygotowujemy migracji zachowującej stare dane. Plik SQL ma umożliwiać szybkie wyczyszczenie i ponowne utworzenie bazy z kontrolowanymi danymi demonstracyjnymi.

### Użytkownicy i role

Klienci i administratorzy będą przechowywani w tabeli `users`. Konto zawiera unikalny `nickname`, opcjonalne imię i nazwisko, hash hasła oraz rolę `customer` albo `admin`. Logowanie odbywa się przez nickname. Publiczna rejestracja zawsze utworzy klienta. Administrator będzie zakładany przez dane startowe lub lokalne narzędzie, nigdy przez publiczny formularz.

Lokalne dane startowe utworzą konta:

| Nick | Imię i nazwisko | Rola | Hasło testowe |
| --- | --- | --- | --- |
| `User1` | John Dee | `customer` | `JD1234#` |
| `User2` | Alice Chain | `customer` | `AC1234#` |
| `Admin` | — | `admin` | `Admin1234#` |

W bazie będą zapisane tylko hashe tych haseł.

Sesja przechowa identyfikator użytkownika, natomiast jego aktualna rola będzie sprawdzana przez aplikację przed udostępnieniem chronionej funkcji.

### Koszyk i zamówienia

Koszyk będzie przechowywany w sesji jako identyfikatory książek i liczby sztuk. Serwer pobierze aktualne dane produktów i sam obliczy cenę.

Zamówienie oraz wszystkie pozycje zostaną zapisane w jednej transakcji. `order_items` zachowa tytuł i cenę jednostkową z chwili zamówienia, aby późniejsze zmiany katalogu nie zmieniały historii.

### Prezentacja produktu i brak książki

Książka jest produktem z tytułem, autorem, opisem, kategorią, wydawcą, ceną, lokalną okładką i stanem magazynowym. Stan większy od zera pozwala dodać produkt do koszyka. Stan `0` pokazuje komunikat „Brak w magazynie” oraz akcję „Zamów książkę”.

Ta sama akcja będzie dostępna, gdy wyszukiwanie nie znajdzie tytułu. Tabela `book_requests` zapisze klienta, opcjonalny identyfikator istniejącej książki, wpisany tytuł, autora, notatkę i status. Administrator obsłuży zgłoszenie przez statusy `new`, `ordered`, `available` albo `rejected`.

### Bezpieczeństwo lokalnej aplikacji

Lokalne uruchomienie nie wyłącza podstawowych zabezpieczeń. Hasła będą tworzone przez `password_hash()`, zapytania pozostaną parametryzowane, identyfikator sesji będzie regenerowany po logowaniu, a formularze zmieniające stan otrzymają token CSRF. Uprawnienia będą sprawdzane po stronie serwera.

Nie projektujemy konfiguracji HTTPS, infrastruktury produkcyjnej, zarządzania sekretami w hostingu ani integracji z zewnętrznymi dostawcami.

## Konsekwencje

### Korzyści

- możemy usunąć techniczny i wizualny bagaż starego ćwiczenia,
- schemat bazy będzie prosty i dopasowany do nowych funkcji,
- środowisko da się odtworzyć jednym importem SQL,
- jedna aplikacja PHP pozostaje łatwa do uruchomienia i debugowania w XAMPP,
- backend klienta i administratora korzysta z tych samych reguł biznesowych.

### Ograniczenia

- dane służą do lokalnych testów i mogą być usuwane przy przebudowie schematu,
- koszyk anonimowego użytkownika znika po wygaśnięciu sesji,
- aplikacja nie będzie przygotowana do publicznego hostingu bez osobnego przeglądu konfiguracji i bezpieczeństwa,
- brak API oznacza brak niezależnego klienta mobilnego lub osobnego frontendu.

## Odrzucone warianty

### Stopniowa modernizacja starego modelu NBL

Utrzymywałaby nazwy, zależności i kompromisy projektu ćwiczeniowego. Ponieważ działamy lokalnie i dane nie wymagają zachowania, czysta przebudowa będzie prostsza.

### Oddzielne aplikacje klienta i administratora

Powodowałyby powielenie konfiguracji, sesji i dostępu do danych. Logiczny podział tras w jednym projekcie wystarczy dla tego zakresu.

### SPA z osobnym API

Wymagałoby dodatkowego routingu, autoryzacji API i procesu budowania. Nie daje istotnej korzyści dla lokalnej aplikacji o tej wielkości.

### Pełny framework PHP

Framework nie jest potrzebny do obecnego zakresu. Decyzję można zmienić tylko wtedy, gdy projekt przestanie być małą aplikacją lokalną.

## Powiązane dokumenty

- [Plan przebudowy](../PLAN.md)
