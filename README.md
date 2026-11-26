# Mini-WMS: algoritmi pe un depozit adevărat

Mini-curs practic de 14 ore, cu tutorele Emil Molner: construim API-ul unui depozit de materiale de construcții și rezolvăm cu algoritmi probleme reale: găsirea unui produs, coada de comenzi, împachetarea în cutii, încărcarea camionului, drumul prin depozit.

📘 **Ghidul complet, cu toți pașii și exercițiile:** [`docs/GHID.md`](docs/GHID.md) (sau [`docs/GHID.pdf`](docs/GHID.pdf))

## De ce ai nevoie

- **Docker Desktop** (pe Windows, cu WSL 2), **Git** și **VS Code**.
- **Nu** trebuie să instalezi PHP, MySQL sau Composer: rulează toate în containere Docker.

## Pornire (prima dată)

```bash
git clone https://github.com/emilmolner/mini-wms.git
cd mini-wms

# 1. pornește TOT proiectul (ambele containere) cu o singură comandă
docker compose up -d --build

# 2. bibliotecile PHP, tabelele și datele de test
docker exec wms-php composer install
docker exec wms-php php bin/console doctrine:migrations:migrate -n
docker exec wms-php php bin/console app:date:genereaza
```

Ce face `docker compose up -d --build`:

- citește `compose.yaml` și pornește **ambele** containere, `wms-mysql` și `wms-php`. Nu trebuie pornite separat;
- `--build` construiește imaginea PHP din `docker/php/Dockerfile` (PHP 8.5, extensiile, Composer). E nevoie de el **prima dată** și când se schimbă ceva în folderul `docker/`;
- pornește întâi MySQL și așteaptă să fie gata (*healthcheck*), apoi aplicația PHP;
- `-d` le lasă să ruleze în fundal, iar terminalul rămâne liber.

Verifică cu `docker compose ps`: ambele containere trebuie să fie *running*, iar MySQL *healthy*.

## Adresele

| Adresă | Ce găsești |
| --- | --- |
| <http://localhost:8000/api/doc> | **Swagger**: toate rutele API-ului, grupate pe exerciții, pe care le încerci din pagină |
| <http://localhost:8000/app.html> | interfața web, cu câte un tab pentru fiecare exercițiu |
| <http://localhost:8000/_profiler> | **Symfony Profiler**: fiecare cerere, cu query-urile SQL și timpii |
| `localhost:3307` | MySQL, pentru DBeaver / MySQL Workbench: baza `wms`, utilizator `wms`, parolă `wms` (doar local) |

## Zi de zi

```bash
docker compose up -d        # pornire (fara --build, imaginea exista deja)
docker compose stop         # oprire; datele raman

docker compose ps           # ce containere ruleaza
docker compose logs -f wms-php   # ce se intampla in aplicatie (iesi cu Ctrl+C)
docker compose restart wms-php   # reporneste doar aplicatia

docker compose down         # sterge containerele; datele din MySQL raman
docker compose down -v      # sterge si datele; apoi reiei pasii de la "Pornire"
```

Testele și comenzile proiectului:

```bash
docker exec wms-php php bin/phpunit --testdox                 # toate testele
docker exec wms-php php bin/phpunit --testdox --filter Ex5    # doar un exercitiu
docker exec wms-php php bin/console app:date:genereaza        # datele de la zero
docker exec -it wms-mysql mysql -uwms -pwms wms               # consola MySQL
```

Fișierul [`api.http`](api.http) are cereri gata făcute pentru fiecare exercițiu (extensia *REST Client* din VS Code).

## Ramurile

| Ramura | Ce conține |
| --- | --- |
| `main` | proiectul de pornire: aplicația merge, dar algoritmii au `TODO`, iar testele lor sunt roșii |
| `solutii` | toate exercițiile rezolvate; `git diff main solutii -- app/src` arată doar soluțiile |

Soluțiile includ și indexul de la Exercițiul 4a. Dacă treci pe `solutii`, rulează migrările ca să-l creezi:

```bash
git checkout solutii
docker exec wms-php php bin/console doctrine:migrations:migrate -n
```

## Ce găsești în proiect

| Folder / fișier | Rol |
| --- | --- |
| `compose.yaml` | cele 2 containere: `wms-php` (aplicația) și `wms-mysql` (baza de date) |
| `docker/php/` | rețeta imaginii PHP (`Dockerfile`, `php.ini`) |
| `app/src/Algorithm/` | **algoritmii: aici lucrăm** (caută `TODO EXERCITIUL`) |
| `app/tests/Algorithm/` | testele care verifică exercițiile (`Ex1...` – `Ex8...`) |
| `app/src/Controller/` | rutele API (gata făcute) |
| `app/src/Entity/`, `app/migrations/` | tabelele bazei de date (Doctrine) |
| `app/src/Command/` | comenzile `app:...` (generarea datelor, măsurători) |
| `app/src/Warehouse/WarehouseMap.php` | harta depozitului |
| `app/public/app.html` | interfața web: o singură pagină, fără biblioteci |
| `api.http` | cereri gata făcute |
| `phpstan.dist.neon` | verificarea codului cu PHPStan (o folosesc și editoarele) |
| `docs/` | ghidul cursului (Markdown și PDF) |

## Bine de știut

- **Ce salvezi în editor se vede imediat** în aplicație: folderul `app/` e același și pe calculatorul tău, și în container. Nu trebuie repornit nimic.
- PHP ține minte codul compilat **2 secunde**. Dacă imediat după ce salvezi un fișier vezi încă rezultatul vechi, mai trimite o dată cererea.
- Datele MySQL stau într-un volum Docker (`wms_mysql_data`). Rămân după `stop` și `down` și dispar doar cu `down -v`.
- Porturile ascultă doar pe `127.0.0.1`, deci aplicația nu se vede din rețeaua Wi-Fi.
- Datele generate sunt **mereu aceleași** (aceleași produse, comenzi și clienți), ca exemplele din ghid să se potrivească la toată lumea.

## Dacă ceva nu merge

Cele mai dese probleme și rezolvările lor sunt în ghid, la [Anexa A. Probleme frecvente](docs/GHID.md#a-probleme-frecvente). Primele lucruri de încercat:

1. Docker Desktop e pornit? (*Engine running*)
2. `docker compose ps`: rulează ambele containere?
3. `docker compose logs wms-php`: ce eroare apare?
