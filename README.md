# Mini-WMS: algoritmi pe un depozit adevărat

Mini-curs practic de 14 ore: construim API-ul unui depozit de materiale de construcții și rezolvăm cu algoritmi probleme reale: găsirea unui produs, coada de comenzi, împachetarea în cutii, încărcarea camionului, drumul prin depozit.

📘 **Ghidul complet, cu toți pașii și exercițiile:** [`docs/GHID.md`](docs/GHID.md) (sau [`docs/GHID.pdf`](docs/GHID.pdf))

## Pornire rapidă

Ai nevoie de **Docker Desktop**, **Git** și **VS Code**.

```bash
git clone https://github.com/NUME-TUTORE/mini-wms.git
cd mini-wms
docker compose up -d --build
docker exec wms-php composer install
docker exec wms-php php bin/console doctrine:migrations:migrate -n
docker exec wms-php php bin/console app:date:genereaza
```

Apoi deschide:

- **http://localhost:8000/api/doc**: toate rutele API-ului, pe care le poți încerca direct din pagină;
- **http://localhost:8000/app.html**: interfața web (opțională).

Testele exercițiilor:

```bash
docker exec wms-php php bin/phpunit --testdox
```

## Ramurile

| Ramura | Ce conține |
|---|---|
| `main` | proiectul de pornire: aplicația merge, dar algoritmii au `TODO`, iar testele lor sunt roșii |
| `solutii` | toate exercițiile rezolvate (`git diff main solutii` arată doar soluțiile) |

## Ce găsești în proiect

| Folder / fișier | Rol |
|---|---|
| `compose.yaml` | cele 2 containere: `wms-php` (http://localhost:8000) și `wms-mysql` (portul 3307) |
| `app/src/Algorithm/` | **algoritmii: aici lucrăm** |
| `app/tests/Algorithm/` | testele care verifică exercițiile |
| `app/src/Controller/` | rutele API (gata făcute) |
| `app/src/Warehouse/WarehouseMap.php` | harta depozitului |
| `api.http` | cereri gata făcute (extensia REST Client din VS Code) |
| `docs/` | ghidul cursului și fișa tutorelui |
