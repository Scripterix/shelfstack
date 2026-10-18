# Plan przebudowy ShelfStack

## Założenie

Budujemy własną aplikację **ShelfStack** od nowa, wykorzystując istniejący projekt jedynie jako działający punkt startowy i źródło prostych przykładów. Nazwa łączy domową półkę (`shelf`) ze stosem znanym z programowania (`stack`). Nie zachowujemy uczelnianego charakteru, nazwy NBL, starego modelu danych ani układu ekranów.

Aplikacja działa wyłącznie lokalnie w XAMPP pod adresem `http://localhost/phpsandbox/php-login/`. Nie przygotowujemy hostingu, konfiguracji produkcyjnej, płatności ani usług zewnętrznych.

## Cel

Powstaje kompletna lokalna aplikacja demonstracyjna z dwoma obszarami:

- frontendem klienta do przeglądania książek, obsługi konta, koszyka i składania zamówień,
- panelem administratora do zarządzania katalogiem oraz obsługi zamówień.

Interfejs ma być spójny, responsywny i wyraźnie zaprojektowany jako jedna aplikacja. Katalog ma prezentować książki jak produkty w małym sklepie, a nie jak techniczną tabelę rekordów.

## Zasady przebudowy

- Możemy zastępować istniejący kod zamiast utrzymywać zgodność ze starymi rozwiązaniami.
- Tworzymy czysty schemat bazy bez prefiksu `NBL_`.
- `database.sql` ma odtwarzać całą lokalną bazę wraz z danymi demonstracyjnymi.
- Zachowujemy prosty stos: PHP 8.2, MariaDB, HTML, CSS i niewielki JavaScript.
- Nie dodajemy frameworka ani procesu budowania frontendu, jeśli nie rozwiązuje konkretnego problemu.
- Logika autoryzacji, ceny zamówienia i zapis danych zawsze są wykonywane po stronie serwera.

## Lokalne konta startowe

| Rola | Nick | Imię i nazwisko | Hasło lokalne |
| --- | --- | --- | --- |
| Klient | `User1` | John Dee | `JD1234#` |
| Klient | `User2` | Alice Chain | `AC1234#` |
| Administrator | `Admin` | — | `Admin1234#` |

Logowanie odbywa się przez `Nick`. W `database.sql` znajdą się wyłącznie hashe haseł wygenerowane przez `password_hash()`. Jawne hasła są dokumentowane tylko jako dane do lokalnego testowania.

## Zakres funkcjonalny

### Klient

- rejestracja, logowanie i wylogowanie,
- katalog z prezentacją produktów oraz szczegóły książki,
- okładka lub lokalny placeholder, tytuł, autor, kategoria, wydawca, cena i dostępność,
- filtrowanie po kategorii i wyszukiwanie,
- koszyk przechowywany w sesji,
- zmiana liczby sztuk i usuwanie pozycji,
- formularz zamówienia,
- potwierdzenie i historia własnych zamówień,
- zgłoszenie prośby o zamówienie książki niedostępnej lub nieobecnej w katalogu.

### Administrator

- osobne wejście do panelu `/admin`,
- pulpit z podsumowaniem katalogu i zamówień,
- dodawanie, edycja i ukrywanie książek,
- zarządzanie kategoriami oraz wydawcami,
- przegląd zamówień i zmiana ich statusu,
- lista próśb klientów o sprowadzenie książki i zmiana statusu zgłoszenia,
- dostęp wyłącznie dla użytkownika z rolą `admin`.

### UX

- wspólny system kolorów, typografii, odstępów i komponentów,
- czytelna nawigacja zależna od roli użytkownika,
- licznik pozycji koszyka w nagłówku,
- czytelne oznaczenia „Dostępna”, „Brak w magazynie” i „Na zamówienie”,
- walidacja przy polach i zrozumiałe komunikaty,
- widoki pustych danych, błędu, powodzenia i braku wyników,
- responsywność od szerokości 360 px,
- obsługa klawiatury, etykiety formularzy i prawidłowy kontrast.

## Etapy realizacji

### Etap 1 — czysty fundament

- [x] Przyjąć nazwę **ShelfStack**; kierunek wizualny oprzeć na domowej półce programisty.
- [ ] Zastąpić stary schemat tabelami `users`, `categories`, `publishers`, `books`, `orders`, `order_items` i `book_requests`.
- [ ] Dodać do książek autora, opis produktu, lokalną ścieżkę okładki, cenę, stan magazynowy i widoczność.
- [ ] Dodać lokalne dane demonstracyjne oraz dokładnie trzy ustalone konta startowe: `User1`, `User2` i `Admin`.
- [ ] Uporządkować katalogi aplikacji i wspólny kod startowy.
- [ ] Dodać pomocnicze funkcje dla sesji, przekierowań, CSRF i komunikatów jednorazowych.

Rezultat: `database.sql` tworzy od zera kompletną bazę, a aplikacja ma stabilny fundament dla nowych ekranów.

### Etap 2 — konta i role

- [ ] Zbudować rejestrację klienta.
- [ ] Zbudować wspólny mechanizm logowania.
- [ ] Dodać kontrolę ról `customer` i `admin`.
- [ ] Ochronić trasy klienta i cały katalog `/admin`.
- [ ] Dodać bezpieczne wylogowanie i zmianę hasła.

Rezultat: oba typy użytkowników korzystają z jednej tabeli kont, ale mają oddzielne uprawnienia i interfejsy.

### Etap 3 — frontend klienta

- [ ] Zbudować nową stronę główną i katalog w formie kart produktów.
- [ ] Dodać wyszukiwanie, filtrowanie oraz stronę szczegółów książki.
- [ ] Dla książki ze stanem `0` wyświetlić akcję „Zamów książkę” zamiast dodawania do koszyka.
- [ ] Przy braku wyników wyszukiwania udostępnić formularz prośby o sprowadzenie tytułu.
- [ ] Zapisywać zgłoszenie w `book_requests`, opcjonalnie łącząc je z istniejącą książką.
- [ ] Zbudować koszyk obsługiwany po stronie serwera.
- [ ] Dodać finalizację zamówienia i ekran potwierdzenia.
- [ ] Zapisywać zamówienie oraz pozycje w jednej transakcji.
- [ ] Dodać historię zamówień zalogowanego klienta.

Rezultat: klient przechodzi pełną ścieżkę od katalogu do zamówienia zapisanego w lokalnej bazie.

### Etap 4 — panel administratora

- [ ] Zbudować nowy pulpit administratora.
- [ ] Dodać kompletne formularze zarządzania katalogiem.
- [ ] Dodać listę zamówień, szczegóły i zmianę statusu.
- [ ] Dodać kolejkę próśb o książki ze statusami `new`, `ordered`, `available` i `rejected`.
- [ ] Zabezpieczyć każdą operację zapisu tokenem CSRF i walidacją serwerową.

Rezultat: administrator może obsługiwać lokalny sklep bez edycji bazy ręcznie.

### Etap 5 — własny wygląd i UX

- [ ] Usunąć pozostałości starego interfejsu oraz stronę credits z projektu uczelnianego.
- [ ] Zbudować własny układ nagłówka, stopki, kart, formularzy, tabel i alertów.
- [ ] Dopracować nawigację mobilną, koszyk i panel użytkownika.
- [ ] Ujednolicić komunikaty i stany wszystkich ekranów.
- [ ] Sprawdzić dostępność i responsywność.

Rezultat: aplikacja wygląda jak jeden świadomie zaprojektowany produkt, a nie przerobione ćwiczenie.

### Etap 6 — test pełnego przepływu

- [ ] Odtworzyć bazę z `database.sql` na lokalnej MariaDB.
- [ ] Przetestować rejestrację, logowanie, role i wylogowanie.
- [ ] Przetestować katalog, koszyk i zamówienie z wieloma pozycjami.
- [ ] Przetestować prośbę o książkę ze stanu `0` oraz tytuł nieobecny w katalogu.
- [ ] Potwierdzić logowanie kont `User1`, `User2` i `Admin` oraz ich role.
- [ ] Sprawdzić, czy klient widzi tylko własne zamówienia.
- [ ] Sprawdzić blokadę panelu administratora dla klienta i gościa.
- [ ] Przetestować zarządzanie katalogiem oraz statusami zamówień.
- [ ] Uruchomić kontrolę składni PHP i JavaScript oraz testy HTTP.
- [ ] Potwierdzić brak nowych ostrzeżeń i błędów w logu Apache.

## Kryteria ukończenia

- Całą aplikację można uruchomić lokalnie po imporcie jednego pliku SQL.
- Konto klienta można utworzyć z poziomu interfejsu.
- Klient może złożyć zamówienie, a administrator widzi je w panelu.
- Niedostępnej książki nie można dodać do koszyka; klient może wysłać prośbę o jej sprowadzenie.
- Brak wyników wyszukiwania prowadzi do formularza prośby o nową książkę.
- Administrator widzi i obsługuje prośby klientów o książki.
- Cena jest liczona ponownie przez serwer i zapisywana historycznie przy pozycji zamówienia.
- Role i dostęp są sprawdzane po stronie serwera.
- Wszystkie operacje zapisu korzystają z CSRF, walidacji i zapytań parametryzowanych.
- Interfejs jest spójny i użyteczny na telefonie oraz komputerze.
- Główne przepływy działają bez błędów PHP i JavaScript.

## Poza zakresem

- publikowanie aplikacji w internecie,
- konfiguracja serwera produkcyjnego i domeny,
- prawdziwe płatności,
- wysyłka e-maili i odzyskiwanie hasła przez e-mail,
- integracje kurierskie,
- aplikacja mobilna i publiczne API,
- rozbudowana gospodarka magazynowa.
