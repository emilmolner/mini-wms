# Mini-WMS: algoritmi pe un depozit adevărat

**Ghidul cursului** · 14 ore, în 3 întâlniri (6 h + 6 h + 2 h) · Tutore: **Emil Molner**

---

## Cuprins

- [Ce facem la acest curs](#ce-facem-la-acest-curs)
- [Partea 0: Pregătirea](#partea-0-pregătirea)
  - [0.1 Programele necesare](#01-programele-necesare)
  - [0.2 Pornirea proiectului](#02-pornirea-proiectului)
  - [0.3 Turul proiectului](#03-turul-proiectului)
  - [0.4 PHP în 15 minute (pentru cine știe C)](#04-php-în-15-minute-pentru-cine-știe-c)
  - [0.5 Cum lucrăm la un exercițiu](#05-cum-lucrăm-la-un-exercițiu)
- [Întâlnirea 1: Căutare, dicționare, sortare, baza de date](#întâlnirea-1)
  - [Exercițiul 1: Căutarea binară](#exercițiul-1-căutarea-binară)
  - [Exercițiul 2: Dulapul cu sertare (tabela de dispersie)](#exercițiul-2-dulapul-cu-sertare)
  - [Exercițiul 3: Coada de comenzi (sortare)](#exercițiul-3-coada-de-comenzi)
  - [Exercițiul 4: Baza de date (index și problema N+1)](#exercițiul-4-baza-de-date)
- [Întâlnirea 2: Metoda lacomă și drumuri prin depozit](#întâlnirea-2)
  - [Exercițiul 5: Împachetarea în cutii](#exercițiul-5-împachetarea-în-cutii)
  - [Exercițiul 6: Încărcarea camionului](#exercițiul-6-încărcarea-camionului)
  - [Exercițiul 7: Drumul cel mai scurt](#exercițiul-7-drumul-cel-mai-scurt)
  - [Exercițiul 8: Ruta de colectare](#exercițiul-8-ruta-de-colectare)
- [Întâlnirea 3: Interfața web și prezentarea](#întâlnirea-3)
- [Anexe: probleme frecvente, comenzi, cuvinte noi, dicționar român–englez](#anexe)

---

## Ce facem la acest curs

Construim API-ul unui **depozit de materiale de construcții**. Un astfel de program se numește **WMS** (*Warehouse Management System*). Depozitul are:

- **200.000 de produse**, fiecare cu un cod (`PROD-0791900`), o greutate, un preț și un loc pe raft;
- **3.000 de comenzi** de la clienți (`Bricolaj Bacău SRL`, `Instal Siret SRL`, …);
- o **hartă** cu rafturi și culoare, pe care se plimbă omul care strânge produsele.

La fiecare exercițiu pornim de la o **problemă reală** din depozit („care comandă se pregătește prima?”, „în câte cutii încape comanda?”) și o rezolvăm cu un algoritm. Apoi comparăm o variantă **simplă** cu una **deșteaptă** și vedem diferența în cifre.

### Cele 8 exerciții

| # | Problema din depozit | Ideea | Întâlnirea |
|---|---|---|---|
| 1 | Găsește un produs după cod | Căutarea binară („ghicește numărul”) | 1 |
| 2 | Ce coduri trimise de client nu există? | Dulapul cu sertare (tabela de dispersie) | 1 |
| 3 | În ce ordine pregătim comenzile? | Sortare cu reguli proprii | 1 |
| 4 | De ce e lentă baza de date? | Index și problema N+1 | 1 |
| 5 | În câte cutii împachetăm comanda? | Metoda lacomă | 2 |
| 6 | Ce comenzi încărcăm în camion? | Metoda lacomă vs. toate combinațiile | 2 |
| 7 | Care e drumul cel mai scurt până la raft? | Valuri în apă (parcurgerea în lățime) | 2 |
| 8 | În ce ordine luăm produsele de pe rafturi? | Cel mai apropiat vecin vs. toate variantele | 2 |

### Programul

| Întâlnirea 1 (6 h) | | Întâlnirea 2 (6 h) | | Întâlnirea 3 (2 h) | |
|---|---|---|---|---|---|
| 0:00 | Prezentare, ce e un WMS | 0:00 | Recapitulare | 0:00 | Interfața web (opțional) |
| 0:30 | Pregătirea (partea 0) | 0:20 | Exercițiul 5 | 0:50 | Prezentări scurte |
| 1:30 | *pauză* | 1:15 | Exercițiul 6 | 1:40 | Întrebări despre job |
| 1:45 | PHP în 15 minute | 2:15 | *pauză* | | |
| 2:15 | Exercițiul 1 | 2:30 | Exercițiul 7 | | |
| 3:00 | Exercițiul 2 | 3:45 | *pauză de masă* | | |
| 3:30 | *pauză de masă* | 4:30 | Exercițiul 8 | | |
| 4:15 | Exercițiul 3 | 5:30 | Recapitulare | | |
| 5:00 | Exercițiul 4 | | | | |
| 5:45 | Recapitulare | | | | |

Programul e orientativ. Dacă un exercițiu durează mai mult, nu e nicio problemă.

---

## Partea 0: Pregătirea

### 0.1 Programele necesare

| Program | La ce folosește | De unde |
|---|---|---|
| **Docker Desktop** | rulează aplicația și baza de date, la fel pe orice calculator | docker.com |
| **Git** | descarcă proiectul și salvează modificările | git-scm.com |
| **VS Code** | editorul în care scriem cod | code.visualstudio.com |

**Pe Windows**, Docker Desktop are nevoie de virtualizare. Verifici în *Task Manager → Performance → CPU → Virtualization: Enabled*. Dacă apare *Disabled*, se activează din BIOS. La instalare, lasă bifată opțiunea **WSL 2**.

Verifică în terminal că totul e instalat:

```bash
docker --version
docker compose version
git --version
```

> **Nu trebuie să instalezi PHP, MySQL sau Composer.** Ele rulează în containerele Docker.

### 0.2 Pornirea proiectului

**Pasul 1. Descarcă proiectul** într-un folder fără spații și fără diacritice în cale (de exemplu `C:\proiecte` sau `~/proiecte`):

```bash
git clone https://github.com/emilmolner/mini-wms.git
cd mini-wms
```

Deschide folderul în VS Code (*File → Open Folder*). VS Code îți propune câteva extensii recomandate: instalează-le. Terminalul se deschide din *Terminal → New Terminal*. Toate comenzile de mai jos se scriu acolo.

**Pasul 2. Pornește Docker Desktop**, apoi containerele:

```bash
docker compose up -d --build
```

Prima dată durează câteva minute. Se pornesc două „calculatoare mici” (containere):

| Container | Ce face | Adresa |
|---|---|---|
| `wms-php` | aplicația noastră (PHP + Symfony) | http://localhost:8000 |
| `wms-mysql` | baza de date MySQL | `localhost:3307` (utilizator `wms`, parolă `wms`) |

**Pasul 3. Instalează bibliotecile PHP, creează tabelele și generează datele:**

```bash
docker exec wms-php composer install
docker exec wms-php php bin/console doctrine:migrations:migrate -n
docker exec wms-php php bin/console app:date:genereaza
```

Ultima comandă trebuie să afișeze:

```text
Locatii: 48
Produse: 200000
Comenzi: 3000

 [OK] Gata in 1.2 secunde.
```

**Pasul 4. Verifică.** Deschide în browser **http://localhost:8000/api/doc**. Vezi pagina cu toate rutele API-ului, grupate pe exerciții. Deschide `GET /api/depozit/harta`, apasă **Try it out**, apoi **Execute**. Trebuie să vezi harta depozitului.

**La începutul fiecărei întâlniri** e suficient să pornești Docker Desktop și să rulezi `docker compose up -d`. **La sfârșit:** `docker compose stop`.

### 0.3 Turul proiectului

```text
mini-wms/
├── compose.yaml                  cele două containere
├── api.http                      cereri gata făcute (extensia REST Client)
├── docs/GHID.md                  acest ghid
└── app/                          aplicația Symfony
    ├── src/
    │   ├── Algorithm/            ★ AICI LUCRĂM: algoritmii
    │   ├── Controller/           rutele API (gata făcute)
    │   ├── Entity/               tabelele: Product, Customer, Order, OrderLine, Location
    │   ├── Repository/           interogările spre baza de date
    │   ├── Command/              comenzi de terminal
    │   └── Warehouse/            harta depozitului
    ├── tests/Algorithm/          testele care verifică exercițiile
    └── public/app.html           interfața web (opțională)
```

**Ce se întâmplă când apelezi API-ul.** Să zicem că ceri `GET /api/catalog/cautare?cod=PROD-0791900`:

1. Symfony găsește **controllerul** care răspunde la adresa `/api/catalog/cautare` (`CatalogController`);
2. controllerul ia din baza de date lista de coduri;
3. controllerul cheamă **algoritmul** nostru (`BinarySearch`);
4. rezultatul se trimite înapoi ca **JSON**:

```json
{ "cod": "PROD-0791900", "gasit": true, "pozitie": 15899, "comparatii": 18, "timpMs": 0.017 }
```

Tu scrii doar pasul 3. Restul e gata făcut.

**Harta depozitului.** Fiecare caracter e o celulă:

```text
    ABCDEFGHIJKLMNOPQRSTUVWXY
00  #########################      #  perete
01  #S......................#      =  raft
02  #.==.==.==.==.==.==.==..#      .  culoar
03  #.==.==.==.==.==.==.==..#      S  start (rampa de încărcare)
04  #.==.==.==.==.==.==.==..#      P  un palet uitat pe culoar
05  #.......P...............#
06  #.==.==.==.==.==.==.==..#
07  #.==.==.==.==.==.==.==..#
08  #.==.==.==.==.==.==.==..#
09  #.......................#
10  #########################
```

O locație are un cod format din **litera coloanei + numărul rândului**. De exemplu, `W08` înseamnă coloana W, rândul 08. Locațiile de pe care se iau produse sunt celulele de culoar aflate lângă un raft.

### 0.4 PHP în 15 minute (pentru cine știe C)

| În C | În PHP | Observații |
|---|---|---|
| `int x = 5;` | `$x = 5;` | Variabilele încep cu `$`, iar tipul nu se scrie. |
| `int v[3] = {1, 2, 3};` | `$v = [1, 2, 3];` | Un „array” poate crește oricât. |
| `v[0]` | `$v[0]` | |
| *(nu există)* | `$p = ['nume' => 'Ciment', 'kg' => 20];` | Un array poate avea chei text (ca un dicționar). |
| *(nu există)* | `$p['nume']` | ce valoare are cheia `'nume'` |
| `n` (ținut separat) | `count($v)` | câte elemente are array-ul |
| `for (int i = 0; i < n; i++)` | `for ($i = 0; $i < count($v); ++$i)` | |
| *(nu există)* | `foreach ($v as $element) { ... }` | parcurge pe rând fiecare element |
| *(nu există)* | `foreach ($p as $cheie => $valoare) { ... }` | parcurge și cheile |
| `strcmp(a, b) == 0` | `$a === $b` | `===` compară și valoarea, și tipul |
| `a / b` (împărțire întreagă) | `intdiv($a, $b)` | `$a / $b` dă rezultat cu virgulă |
| `printf("%d", x);` | `echo $x;` | |
| funcție | `function suma(int $a, int $b): int { return $a + $b; }` | |
| *(nu există)* | `$v[] = 7;` | adaugă 7 la sfârșitul array-ului |
| *(nu există)* | `isset($p['kg'])` | există cheia `'kg'`? |
| *(nu există)* | `unset($p['kg'])` | șterge cheia `'kg'` |

**Clase, pe scurt.** O clasă e un „tip” care grupează date și funcții (metode). În proiect, aproape fiecare algoritm e o clasă cu o singură metodă importantă:

```php
<?php
final class BinarySearch
{
    public int $comparisons = 0;            // o variabilă a obiectului

    public function find(array $codes, string $target): int
    {
        $this->comparisons = 0;             // $this = „obiectul curent”
        // ...
        return -1;
    }
}

$search = new BinarySearch();               // creăm un obiect
$pos = $search->find($lista, 'PROD-0000005');   // apelăm metoda
echo $search->comparisons;                  // citim variabila
```

### 0.5 Cum lucrăm la un exercițiu

Fiecare exercițiu se face la fel:

1. **Citești problema** în acest ghid.
2. **Deschizi fișierul** indicat. Cauți `TODO EXERCITIUL N`. Deasupra e explicat, pas cu pas, ce ai de scris.
3. **Ștergi linia provizorie** de sub comentariu (de exemplu `return -1;`) și **scrii codul**.
4. **Rulezi testele** exercițiului:

   ```bash
   docker exec wms-php php bin/phpunit --testdox --filter Ex1
   ```

   La început majoritatea testelor sunt roșii (✘). Câteva pot fi verzi din start, pentru că verifică un caz simplu (de exemplu „cod inexistent → -1”). Exercițiul e gata când **toate** sunt verzi (✔).
5. **Încerci în browser**, în http://localhost:8000/api/doc, ruta exercițiului.
6. **Salvezi în Git:**

   ```bash
   git add .
   git commit -m "Exercitiul 1: cautarea binara"
   ```

> **Testele sunt prietenii tăi.** Un test roșu îți spune exact ce nu merge: ce rezultat aștepta și ce a primit. Citește mesajul înainte să ceri ajutor.

Toate testele deodată: `docker exec wms-php php bin/phpunit --testdox`.

---

<a id="întâlnirea-1"></a>
# Întâlnirea 1: Căutare, dicționare, sortare, baza de date

## Cât de repede crește munca? (5 minute de teorie)

Când comparăm doi algoritmi, nu ne interesează doar dacă sunt corecți. Ne interesează **ce se întâmplă când datele cresc**: de 10 ori mai multe produse înseamnă de 10 ori mai multă muncă? Mai puțin? Mult mai mult?

| Cum crește munca | Se scrie | Exemplu din depozit | 1.000 de elemente | 1.000.000 de elemente |
|---|---|---|---|---|
| deloc | O(1) | deschizi sertarul cu eticheta potrivită | 1 pas | 1 pas |
| foarte încet | O(log n) | căutarea binară | ~10 pași | ~20 de pași |
| la fel ca datele | O(n) | cauți pe rând | 1.000 de pași | 1.000.000 de pași |
| mult mai repede | O(n²) | două bucle, una în alta | 1.000.000 de pași | 1.000.000.000.000 de pași |
| exploziv | O(2ⁿ), O(n!) | toate combinațiile | imposibil | imposibil |

Citim „O(n)” ca „de ordinul lui n”. Nu e nevoie de mai mult de atât pentru acest curs.

---

## Exercițiul 1: Căutarea binară

> 🇬🇧 **În engleză:** Binary Search · familia *Searching* · O(log n)

**Problema.** Un client sună: „Aveți produsul `PROD-0791900`?”. Avem 200.000 de coduri. Cum îl găsim repede?

**Varianta simplă (gata făcută).** Ne uităm la coduri **pe rând**, de la primul până îl găsim. Ca atunci când cauți o carte pe un raft uitându-te la fiecare. Fișierul: `app/src/Algorithm/Search/LinearSearch.php`.

**Ideea deșteaptă: jocul „ghicește numărul”.** Mă gândesc la un număr între 1 și 100. Tu spui un număr, iar eu îți răspund doar „mai mare” sau „mai mic”. Cel mai bine începi cu **50**: oricare ar fi răspunsul, ai scăpat de jumătate din numere. Apoi 25 sau 75, și tot așa. În cel mult **7 încercări** ai ghicit.

Asta funcționează **doar dacă lista e sortată**, la fel cum jocul merge doar pentru că numerele sunt în ordine.

```text
Căutăm PROD-0000500 în: [0100, 0200, 0300, 0400, 0500, 0600, 0700]
                          ^ stânga          ^ mijloc         ^ dreapta
0400 < 0500  → căutăm doar în dreapta: [0500, 0600, 0700]
                                          ^ stânga ^ mijloc ^ dreapta
0600 > 0500  → căutăm doar în stânga:  [0500]
                                          ^ mijloc
0500 = 0500  → GĂSIT, în 3 pași
```

**Ce ai de făcut.** Deschide `app/src/Algorithm/Search/BinarySearch.php` și completează metoda `find()` după pașii din comentariu.

**Cum verifici:**

```bash
docker exec wms-php php bin/phpunit --testdox --filter Ex1
docker exec wms-php php bin/console app:masurare:cautare
```

A doua comandă caută un cod care nu există în liste tot mai mari și afișează un tabel. Uită-te la coloanele *comparații secvențial* și *comparații binar*:

| n | comparații pe rând | comparații binar |
|---|---|---|
| 1.000 | 1.000 | 10 |
| 1.000.000 | 1.000.000 | 20 |

Încearcă și în Swagger `GET /api/catalog/cautare`, cu `metoda=secventiala`, apoi cu `metoda=binara`.

**Întrebări:**
1. De câte ori crește numărul de comparații când lista crește de 1.000 de ori?
2. De ce nu putem folosi căutarea binară pe o listă nesortată?

---

## Exercițiul 2: Dulapul cu sertare

> 🇬🇧 **În engleză:** Hash Table / Hash Map · *lookup* în O(1)

**Problema.** Un client trimite o listă cu 200 de coduri. Vrem să-i spunem care dintre ele **nu există** în catalog.

**Ce e „catalogul”.** Tabela `product` din MySQL, coloana `code`: 200.000 de coduri. Controllerul le aduce pe toate într-un array PHP (`SELECT code FROM product`, în `ProductRepository::allCodesSorted()`), iar comparația o facem noi, în PHP. Așa scriem și înțelegem algoritmul; în producție am lăsa MySQL să facă asta (vezi întrebarea 3).

**Varianta simplă (gata făcută).** Pentru fiecare cod cerut, căutăm pe rând în tot catalogul: 200 × 200.000 = până la **40 de milioane** de comparații.

**Ideea deșteaptă: un dulap cu sertare etichetate.** Pregătim întâi un dulap în care fiecare produs are sertarul lui, cu codul scris pe etichetă. După aceea, ca să afli dacă un cod există, **deschizi direct sertarul** cu eticheta lui. Nu te mai uiți prin celelalte.

În PHP, „dulapul” e un array cu chei:

```php
<?php
$index = [];
$index['PROD-0000001'] = true;     // punem eticheta pe sertar
$index['PROD-0000002'] = true;

isset($index['PROD-0000001']);     // true: sertarul există
isset($index['PROD-9999999']);     // false: nu există
```

`isset` e **instantaneu**, oricât de mare ar fi dulapul. În cărți, structura asta se numește **tabelă de dispersie** (*hash table*).

**Ce ai de făcut.** Deschide `app/src/Algorithm/Hash/MissingCodeFinder.php` și completează `findMissingFast()`.

**Cum verifici:**

```bash
docker exec wms-php php bin/phpunit --testdox --filter Ex2
```

Apoi `GET /api/catalog/verificare-aleatoare?numar=200`:

```json
"naiva":       { "numarLipsa": 100, "operatii": 30291568, "timpMs": 300 },
"cuDispersie": { "numarLipsa": 100, "operatii": 200200,   "timpMs": 8 }
```

**Întrebări:**
1. Încearcă `POST /api/catalog/verificare` cu doar 2 coduri. Care variantă e mai rapidă acum? De ce?
   (Indiciu: cât durează să etichetezi 200.000 de sertare?)
2. Unde ai mai văzut „dicționare” în viața de zi cu zi?
3. La serviciu, aceeași verificare s-ar face direct în baza de date:

   ```sql
   SELECT code FROM product WHERE code IN ('PROD-0791900', 'PROD-INEXISTENT');
   ```

   Codurile trimise care nu apar în rezultat sunt cele lipsă. De ce e mai bine așa decât să aduci 200.000 de coduri în PHP? Ce o face rapidă? (Indiciu: Exercițiul 4a.)

---

## Exercițiul 3: Coada de comenzi

> 🇬🇧 **În engleză:** Sorting cu un Comparator · *custom sort* / *multi-key sort*

**Problema.** Avem 3.000 de comenzi. În ce ordine le pregătim?

**Ideea: ca la camera de gardă.** Intră întâi urgențele. Între doi pacienți la fel de urgenți, intră cel venit primul. Regula din depozit:

1. prioritatea mai mică e mai importantă: **1** (urgent), apoi **2** (normal), apoi **3** (poate aștepta);
2. la aceeași prioritate, comanda **mai veche** are întâietate;
3. dacă și data e aceeași, decide **numărul comenzii** (`CMD-00001` înaintea lui `CMD-00002`).

**Nu scriem noi algoritmul de sortare.** PHP are deja `usort()`, care sortează orice. Trebuie doar să-i spunem **care din două comenzi vine prima**. Funcția care face asta se numește **comparator** și întoarce:

| Rezultat | Înseamnă |
|---|---|
| un număr negativ | `$a` vine înaintea lui `$b` |
| `0` | sunt la egalitate |
| un număr pozitiv | `$a` vine după `$b` |

Operatorul `<=>` („nava spațială”) face exact asta: `1 <=> 2` dă `-1`, `2 <=> 2` dă `0`, `3 <=> 2` dă `1`.

**Ce ai de făcut.** Deschide `app/src/Algorithm/Sort/OrderComparator.php` și completează `compare()`.

**Cum verifici:**

```bash
docker exec wms-php php bin/phpunit --testdox --filter Ex3
```

Apoi `GET /api/comenzi/coada`: primele comenzi trebuie să aibă `"prioritate": 1` și datele cele mai vechi.

În răspuns, `pePrioritati` arată câte comenzi are fiecare prioritate și pe ce locuri din coadă au ajuns. Dacă sortarea e bună, fiecare prioritate stă pe locuri una după alta (`"unaDupaAlta": true`). Ca să vezi granița dintre prioritatea 1 și 2, cere o bucată din mijlocul cozii: `GET /api/comenzi/coada?dela=983&limita=8`.

**Întrebări:**
1. Cum ai schimba regula ca firma `Bricolaj Bacău SRL` (client important) să fie mereu prima?
2. `usort` sortează 3.000 de comenzi în câteva milisecunde. Câte comparații face, aproximativ? (Indiciu: sortările bune fac în jur de n × log₂ n comparații.)

---

## Exercițiul 4: Baza de date

> 🇬🇧 **În engleză:** Database Index (B-Tree) și N+1 Query Problem

Algoritmii nu trăiesc doar în codul PHP. Și baza de date folosește aceleași idei.

### 4a. Indexul: cuprinsul cărții

**Problema.** `GET /api/catalog/produs?cod=PROD-0791900` caută produsul direct în MySQL. Încearcă-l și uită-te la `cumACautatMySQL`:

```text
-> Table scan on p  (... rows=198978)
```

**Table scan** înseamnă că MySQL a citit **tot tabelul**, rând cu rând, adică exact căutarea pe rând din Exercițiul 1.

**Ideea: cuprinsul unei cărți.** Ca să găsești capitolul „Ciment”, nu citești toată cartea: te uiți în cuprins, unde capitolele sunt în ordine, și sari direct la pagină. Un **index** este cuprinsul unei coloane: o listă **sortată** a valorilor, cu „pagina” (rândul) fiecăreia. MySQL caută în el ca în căutarea binară.

**Ce ai de făcut.**

1. Deschide `app/src/Entity/Product.php`. Deasupra liniei `class Product` adaugă:

   ```php
   <?php
   #[ORM\Index(name: 'idx_product_code', columns: ['code'])]
   ```

2. Cere-i lui Doctrine să genereze modificarea pentru baza de date, apoi aplic-o:

   ```bash
   docker exec wms-php php bin/console make:migration
   docker exec wms-php php bin/console doctrine:migrations:migrate -n
   ```

   Comanda `make:migration` creează un fișier nou în `app/migrations/`. Deschide-l: vei vedea `CREATE INDEX idx_product_code ON product (code)`.

3. Încearcă din nou `GET /api/catalog/produs?cod=PROD-0791900`. Acum trebuie să vezi `"folosesteIndex": true` și `Index lookup on p using idx_product_code`. Compară și `timpMs` cu cel de dinainte.

**Întrebări:**
1. Dacă indexul e atât de bun, de ce nu punem index pe toate coloanele? (Indiciu: ce trebuie să facă MySQL cu fiecare index când adăugăm un produs nou?)

### 4b. Problema N+1: drumul la magazin

**Problema.** Vrem raportul cu **toate comenzile** clientului `Bricolaj Bacău SRL` și produsele din ele. Clientul are id-ul **1** (lista clienților, cu id-urile lor: `GET /api/clienti`). Încearcă `GET /api/comenzi/raport?client=1&metoda=naiva`:

```json
{ "comenzi": 373, "queryuriSql": 1671, "timpMs": 228 }
```

**1.671 de interogări** pentru un singur raport! De unde vin?

- 1 interogare pentru lista de comenzi;
- **pentru fiecare** din cele 373 de comenzi, încă una pentru liniile ei;
- **pentru fiecare** produs, încă una pentru detaliile lui.

**Ideea: drumul la magazin.** Ai de cumpărat 50 de lucruri. Varianta N+1: mergi la magazin, cumperi un lucru, te întorci acasă, apoi pleci după următorul… de 50 de ori. Varianta bună: faci **o listă** și mergi **o singură dată**.

În SQL, „lista” se numește **JOIN**: aducem comenzile, liniile și produsele dintr-o singură interogare.

**Ce ai de făcut.** Deschide `app/src/Repository/OrderRepository.php`, metoda `findByCustomerWithJoin()`, și adaugă cele două JOIN-uri descrise în comentariu.

**Cum verifici:** `GET /api/comenzi/raport?client=1&metoda=join` trebuie să arate `"queryuriSql": 1` și un timp mult mai mic.

**Întrebări:**
1. De ce se numește „N+1”?
2. Problema asta apare foarte des în aplicațiile reale. De ce credeți că nu se observă când ai doar 5 comenzi de test?

### Debug ca la serviciu: Symfony Profiler

Symfony ține minte, în modul de dezvoltare, tot ce s-a întâmplat la fiecare cerere: timpul, memoria, erorile și **fiecare query SQL**. Unealta se numește **Symfony Profiler** (*Web Profiler*) și o folosim zilnic la serviciu ca să găsim probleme ca N+1.

1. Fă o cerere, de exemplu `GET /api/comenzi/raport?client=1&metoda=naiva` din Swagger, sau apasă „Compară” în tabul „4. Baza de date” din pagina web.
2. Deschide **http://localhost:8000/_profiler**. Vezi ultimele cereri; alege-o pe cea cu `/api/comenzi/raport`. Din pagina web ajungi direct acolo, cu linkul 🔎 de lângă rezultat.
3. În stânga, alege **Doctrine**. Vezi câte query-uri s-au trimis și cât au durat.
4. Tabul **Grouped Statements** (deschis implicit) adună cele 1.671 de query-uri în 3 rânduri, cu coloana **Count** (de câte ori s-a trimis fiecare), la fel ca în pagina noastră. Cu **Show all queries** le vezi pe toate, unul câte unul.
5. Lângă un query, apasă **Explain query**: vezi același tabel `EXPLAIN` ca la Exercițiul 4a, cu coloana `key`.

Fă același lucru pentru `metoda=join` și compară. Același panou te ajută și la 4a: caută produsul, apoi apasă „Explain query” înainte și după ce adaugi indexul.

---

<a id="întâlnirea-2"></a>
# Întâlnirea 2: Metoda lacomă și drumuri prin depozit

## Rapid sau perfect? (5 minute de teorie)

Unele probleme sunt **foarte greu** de rezolvat perfect: singura metodă sigură e să încerci toate variantele, iar variantele „explodează”:

| Câte elemente | Câte variante (toate ordinile: n!) |
|---|---|
| 5 | 120 |
| 10 | 3.628.800 |
| 20 | 2.432.902.008.176.640.000 |

În astfel de cazuri, în practică folosim **metoda lacomă**: la fiecare pas facem alegerea care pare cea mai bună **acum** și nu ne mai întoarcem. E rapidă și de obicei dă un rezultat bun, dar **nu garantează** că e cel mai bun. (În cărți și la curs, metoda lacomă apare cu numele englezesc **greedy**.)

Azi comparăm de trei ori o metodă lacomă cu una care încearcă tot.

---

## Exercițiul 5: Împachetarea în cutii

> 🇬🇧 **În engleză:** Bin Packing Problem · Greedy: Next Fit, First Fit, First Fit Decreasing

**Problema.** Comanda `CMD-00587` are 11 bucăți: `[12, 12, 18, 18, 4, 13, 23, 23, 17, 17, 17]` kg. O cutie duce cel mult **30 kg**. Vrem cât mai **puține** cutii.

**Trei reguli simple (toate „lacome”):**

| Regula | Cum funcționează | Cutii pentru CMD-00587 |
|---|---|---|
| **Cutia curentă** (gata făcută) | O singură cutie deschisă. Dacă bucata nu mai încape, închizi cutia și deschizi alta. | 9 |
| **Prima cutie în care încape** (5a) | Toate cutiile rămân deschise. Pui bucata în **prima** cutie în care mai are loc. | 8 |
| **Întâi cele grele** (5b) | Ca la făcut bagajul: sortezi bucățile de la cea mai grea la cea mai ușoară, apoi aplici regula de mai sus. | 7 |

**Ce ai de făcut.** Deschide `app/src/Algorithm/Packing/BoxPacker.php` și completează `firstFit()` (5a) și `firstFitDecreasing()` (5b). Pentru 5b ajung 2 rânduri.

**Cum verifici:**

```bash
docker exec wms-php php bin/phpunit --testdox --filter Ex5
```

Apoi `GET /api/comenzi/CMD-00587/impachetare`. Încearcă și cu `capacitate=40`.

**Întrebări:**
1. Desenează pe hârtie cutiile pentru regula „întâi cele grele”. Poți face cu 6 cutii? (Totalul e 174 kg.)
2. De ce ajută să pui întâi bucățile mari?

---

## Exercițiul 6: Încărcarea camionului

> 🇬🇧 **În engleză:** 0/1 Knapsack Problem · Greedy vs. Brute Force (Exhaustive Search)

**Problema.** Camionul duce **50 kg**. Avem trei comenzi:

| Comanda | Greutate | Valoare | Lei pe kg |
|---|---|---|---|
| Paleți gresie | 10 kg | 60 lei | **6** |
| Saci ciment | 20 kg | 100 lei | **5** |
| Cutii faianță | 30 kg | 120 lei | **4** |

O comandă se ia **întreagă sau deloc**. Ce încărcăm ca să plece marfă de valoare cât mai **mare**?

**Metoda lacomă (o scrii tu).** Iei întâi ce aduce **cei mai mulți lei pe kilogram**: gresie (10 kg), apoi ciment (30 kg în total). Faianța nu mai încape. **Total: 160 lei**, iar camionul pleacă cu 20 kg loc liber.

**Toate combinațiile (gata făcută).** Pentru fiecare comandă încerci ambele variante: „**o iau**” și „**nu o iau**”. Cu 3 comenzi sunt 2 × 2 × 2 = **8 variante**. Cea mai bună: ciment + faianță = **220 lei**.

```text
                         gresie?
                   da /          \ nu
               ciment?            ciment?
             da /   \ nu        da /   \ nu
         faianță?  faianță?   faianță?  faianță?
          ...        ...        ...        ...
```

Pentru 20 de comenzi sunt 2²⁰ = **1.048.576** de variante, iar pentru 40 de comenzi peste **o mie de miliarde**.

**Ce ai de făcut.** Deschide `app/src/Algorithm/Optimization/TruckLoader.php` și completează `lacom()`. Citește și `toateCombinatiile()`: e scrisă cu o funcție care se apelează pe ea însăși (recursivitate).

**Cum verifici:**

```bash
docker exec wms-php php bin/phpunit --testdox --filter Ex6
```

Apoi:
- `POST /api/camion/incarcare` cu exemplul de mai sus (e deja completat în Swagger): 160 față de 220;
- `GET /api/camion/plan?capacitate=800&comenzi=15`: primele 15 comenzi din coada de azi. Metoda lacomă pierde aproximativ 3.000 de lei. Crește `comenzi` la 20 și uită-te la `timpMs` și `varianteVerificate`.

**Întrebări:**
1. Găsește un exemplu cu 3 comenzi în care metoda lacomă dă rezultatul cel mai bun.
2. În depozit avem 200 de comenzi pe zi. Ce metodă folosim? De ce?

---

## Exercițiul 7: Drumul cel mai scurt

> 🇬🇧 **În engleză:** Shortest Path · Breadth-First Search (BFS) · Queue

**Problema.** Omul care strânge produsele pleacă de la rampă (`S`) și trebuie să ajungă la raftul `W08`. Pe unde merge ca să facă **cei mai puțini pași**? Atenție la paletul `P` de pe culoar.

**Ideea: valurile în apă.** Arunci o piatră în apă. Valurile pornesc din acel punct și se depărtează în cercuri. Primul val atinge celulele aflate la 1 pas, al doilea val pe cele aflate la 2 pași, și tot așa. **Primul val care atinge destinația** ne dă drumul cel mai scurt.

```text
   pornim din S          după 2 valuri          după 4 valuri
   #########             #########              #########
   #S.......             #S12.....              #S1234...
   #.==.==..             #1==.==..              #1==4==..
   #.==.==..             #2==.==..              #2==.==..
                                                #3==.==..
                                                #4.......
```

Ca să facem asta, folosim o **coadă**, ca la casa de marcat: cine intră primul, iese primul.

1. Punem startul în coadă.
2. Scoatem prima celulă din coadă. Vecinii ei (sus, jos, stânga, dreapta) **pe care nu i-am văzut încă** intră la coadă. Pentru fiecare vecin ținem minte **de unde am venit**.
3. Repetăm până scoatem din coadă destinația.
4. Refacem drumul de la destinație înapoi, din „de unde am venit” în „de unde am venit”. Partea asta e gata făcută.

În cărți, metoda se numește **parcurgerea în lățime** (*Breadth-First Search*, **BFS**).

**Ce ai de făcut.** Deschide `app/src/Algorithm/Graph/PathFinder.php` și completează `shortestPath()`.

**Cum verifici:**

```bash
docker exec wms-php php bin/phpunit --testdox --filter Ex7
docker exec wms-php php bin/console app:depozit:drum W08
```

A doua comandă desenează drumul în terminal, cu `*`:

```text
01  #S*********.............#
02  #.==.==.==*==.==.==.==..#
03  #.==.==.==*==.==.==.==..#
04  #.==.==.==*==.==.==.==..#
05  #.......P.*************.#
06  #.==.==.==.==.==.==.==*.#
07  #.==.==.==.==.==.==.==*.#
08  #.==.==.==.==.==.==.==o.#

Pasi: 28   Celule verificate: 118
```

**Întrebări:**
1. Mută paletul `P` în `app/src/Warehouse/WarehouseMap.php` (de exemplu pe rândul 01) și rulează din nou. Ce se schimbă?
2. Pune un perete care închide complet raftul `W08`. Ce răspunde programul?

---

## Exercițiul 8: Ruta de colectare

> 🇬🇧 **În engleză:** Travelling Salesman Problem (TSP) · Nearest Neighbor Heuristic vs. Brute Force

**Problema.** Comanda `CMD-00017` are produse pe 6 rafturi: `B02`, `H03`, `B07`, `T04`, `W08`, `T07`. Omul pleacă de la rampă, trece pe la toate și se întoarce. În ce **ordine** merge ca să facă cei mai puțini pași?

Distanțele dintre rafturi le calculăm cu algoritmul din Exercițiul 7. Partea asta e gata făcută.

**Cel mai apropiat vecin (o scrii tu, metodă lacomă).** Din locul în care ești, mergi **mereu la cel mai apropiat raft** la care n-ai fost. La final te întorci la rampă.

**Toate variantele (gata făcută).** Încerci toate ordinile posibile: pentru 6 rafturi sunt 6 × 5 × 4 × 3 × 2 × 1 = **720** de rute.

| Metoda | Ruta pentru CMD-00017 | Pași |
|---|---|---|
| Cel mai apropiat vecin | START → B02 → B07 → H03 → T04 → T07 → W08 → START | 70 |
| Toate variantele | START → B02 → B07 → W08 → T07 → T04 → H03 → START | **64** |

Metoda lacomă a „uitat” că la final trebuie să se întoarcă de la W08, cel mai îndepărtat raft.

**Ce ai de făcut.** Deschide `app/src/Algorithm/Graph/PickingRoute.php` și completează `nearestNeighbor()`.

**Cum verifici:**

```bash
docker exec wms-php php bin/phpunit --testdox --filter Ex8
```

Apoi `GET /api/comenzi/CMD-00017/ruta`, unde `harta` are drumul desenat. Încearcă și alte comenzi: `CMD-00023`, `CMD-00041`, `CMD-00083`.

**Întrebări:**
1. Pentru 6 rafturi, „toate variantele” verifică 720 de rute. Câte ar verifica pentru 12 rafturi?
2. În depozitele mari, un om ia 30–40 de produse pe tură. Ce metodă se folosește?

Toate testele trebuie să fie acum verzi:

```bash
docker exec wms-php php bin/phpunit --testdox
```

---

<a id="întâlnirea-3"></a>
# Întâlnirea 3: Interfața web și prezentarea

## Interfața web (opțional)

Până acum am folosit API-ul din Swagger. Un API e făcut ca **alte programe** să-l folosească: o aplicație de mobil, un cititor de coduri de bare, o pagină web.

Deschide **http://localhost:8000/app.html**. E o singură pagină HTML (`app/public/app.html`), fără biblioteci și fără instalări, care apelează API-ul nostru cu `fetch()` și desenează rezultatele. Are câte un tab pentru fiecare exercițiu, de la 1 la 8: cutiile se văd ca dreptunghiuri, iar drumul și ruta se desenează animat pe hartă.

Un tab arată rezultatul corect doar după ce ai rezolvat exercițiul lui. De exemplu, la „7. Drum în depozit”, drumul galben apare abia după Exercițiul 7.

**Ce poți încerca** (alege una):

1. **Ușor:** schimbă culorile hărții (variabilele din `:root`, la începutul fișierului).
2. **Mediu:** la tabul „6. Camion”, desenează două bare orizontale care arată cât s-a umplut camionul cu fiecare metodă (de exemplu 600 din 800 kg = bară plină 75%). Te poți inspira din modul în care sunt desenate cutiile, în funcția `cutii()`.
3. **Mai greu:** la tabul „3. Coada de comenzi”, adaugă un câmp „Client” care afișează doar comenzile acelui client (filtrare în JavaScript, cu `filter()`).

## Prezentarea (5 minute de fiecare)

Alege **un exercițiu** și explică-l colegilor:
1. care era problema din depozit;
2. ideea algoritmului, cu o comparație din viața de zi cu zi;
3. codul tău (2–3 rânduri importante);
4. o cifră care arată diferența (comparații, query-uri, pași, lei).

La serviciu facem la fel când prezentăm o modificare colegilor: problema, soluția, dovada că merge.

## Soluțiile

Soluțiile tuturor exercițiilor sunt pe ramura `solutii`. După ce tutorele le publică:

```bash
git fetch origin
git diff main origin/solutii -- app/src
```

Ultima comandă îți arată, fișier cu fișier, diferența dintre ce aveai de completat și soluție.

---

<a id="anexe"></a>
# Anexe

## A. Probleme frecvente

| Problema | Soluția |
|---|---|
| `Cannot connect to the Docker daemon` | Docker Desktop nu e pornit. Pornește-l și așteaptă să scrie *Engine running*. |
| `port is already allocated` (8000 sau 3307) | Alt program folosește portul. În `compose.yaml`, schimbă partea din stânga: `"8001:8000"`, apoi folosește http://localhost:8001. |
| `No such container: wms-php` | Containerele nu sunt pornite: `docker compose up -d`. |
| Pagina dă eroare 500 imediat după pornire | Nu ai rulat `composer install`, sau MySQL încă pornește. Mai așteaptă 30 de secunde. |
| `Table 'wms.product' doesn't exist` | Nu ai rulat migrările (`doctrine:migrations:migrate -n`). |
| Testele merg, dar Swagger arată date vechi | Reîncarcă pagina cu Ctrl+F5. |
| Am stricat ceva într-un fișier | `git checkout -- app/src/Algorithm/Search/BinarySearch.php` îl aduce înapoi la ultima versiune salvată. |
| Vreau să o iau de la capăt cu datele | `docker exec wms-php php bin/console app:date:genereaza` |
| Vreau să șterg tot (containere și date) | `docker compose down -v`, apoi pașii din 0.2 |
| **Linux:** VS Code spune *Permission denied* când salvez un fișier creat de o comandă (de exemplu migrarea) | Fișierele create în container aparțin utilizatorului `root`. Rulează o dată `sudo chown -R $USER app`. |
| **Windows:** totul merge foarte încet | Docker Desktop e mult mai rapid dacă proiectul stă în WSL. Deschide terminalul *Ubuntu (WSL)*, clonează proiectul acolo (de exemplu în `~/proiecte`) și deschide-l din VS Code cu extensia *WSL*. |

## B. Comenzi utile

```bash
# pornire / oprire
docker compose up -d
docker compose stop

# testele (toate, sau doar ale unui exercițiu)
docker exec wms-php php bin/phpunit --testdox
docker exec wms-php php bin/phpunit --testdox --filter Ex5

# comenzile proiectului
docker exec wms-php php bin/console app:date:genereaza
docker exec wms-php php bin/console app:masurare:cautare
docker exec wms-php php bin/console app:depozit:drum W08

# baza de date, direct din terminal
docker exec -it wms-mysql mysql -uwms -pwms wms
```

În MySQL poți încerca, de exemplu:

```sql
SELECT c.name, COUNT(*) FROM orders o JOIN customer c ON c.id = o.customer_id GROUP BY c.name;
EXPLAIN ANALYZE SELECT * FROM product WHERE code = 'PROD-0791900';
```

(ieși cu `exit`)

## C. Cuvinte noi

| Cuvânt | Pe scurt |
|---|---|
| **Algoritm** | o rețetă: pași clari care rezolvă o problemă |
| **API** | o „ușă” prin care alte programe vorbesc cu aplicația noastră |
| **JSON** | formatul în care răspunde API-ul: `{"cheie": "valoare"}` |
| **Endpoint / rută** | o adresă a API-ului, de exemplu `GET /api/depozit/harta` |
| **Container (Docker)** | un „calculator mic” izolat, în care rulează un program |
| **Complexitate, O(n)** | cât de repede crește munca atunci când cresc datele |
| **Căutare binară** | cauți într-o listă sortată, înjumătățind-o la fiecare pas |
| **Tabelă de dispersie** | un dulap cu sertare etichetate: găsești instant după etichetă |
| **Comparator** | funcția care spune care din două elemente vine primul |
| **Index (bază de date)** | cuprinsul unei coloane, ținut sortat |
| **Problema N+1** | o interogare pentru listă, plus câte una pentru fiecare element |
| **Symfony Profiler** | pagina `/_profiler`: tot ce s-a întâmplat la o cerere (query-uri, timp, erori) |
| **Metoda lacomă (greedy)** | la fiecare pas alegi ce pare mai bun acum, fără să revii |
| **Parcurgerea în lățime (BFS)** | explorezi „în valuri”, de la cel mai apropiat la cel mai depărtat |
| **Recursivitate** | o funcție care se apelează pe ea însăși, pe o problemă mai mică |
| **Test automat** | cod care verifică alt cod: verde, merge; roșu, ceva e greșit |
| **WMS** | *Warehouse Management System*, programul care administrează un depozit |

## D. Dicționar român – englez

La curs, în cărți, pe Stack Overflow sau când întrebi un asistent AI, algoritmii apar aproape mereu cu numele în engleză. Tabelul te ajută să faci legătura.

### Familii de algoritmi și metode

| În ghid | În engleză | Ce înseamnă pe scurt | Unde apare |
|---|---|---|---|
| Căutare | **Searching** | găsești un element într-o colecție | Ex. 1, 2 |
| Sortare | **Sorting** | pui elementele într-o ordine | Ex. 3 |
| Filtrare | **Filtering** | păstrezi doar elementele care respectă o condiție (`array_filter`, `WHERE` în SQL) | Ex. 2 (codurile lipsă) |
| Împarte și stăpânește | **Divide and Conquer** | împarți problema în bucăți mai mici | Ex. 1 |
| Metoda lacomă | **Greedy Algorithm** | alegi la fiecare pas ce pare mai bun acum | Ex. 5, 6, 8 |
| Toate variantele / combinațiile | **Brute Force**, **Exhaustive Search** | încerci tot și păstrezi ce e mai bun | Ex. 6, 8 |
| Revenire (încearcă, apoi anulează) | **Backtracking** | construiești soluția pas cu pas, cu „undo” | Ex. 6, 8 |
| Recursivitate | **Recursion** | o funcție care se apelează pe ea însăși | Ex. 6, 8 |
| Euristică | **Heuristic** | regulă rapidă, bună în practică, fără garanția celui mai bun rezultat | Ex. 8 |
| Programare dinamică | **Dynamic Programming (DP)** | reții rezultatele subproblemelor ca să nu le calculezi de două ori | la curs |
| Optimizare combinatorie | **Combinatorial Optimization** | cauți cea mai bună combinație dintre foarte multe | Ex. 5, 6, 8 |

### Algoritmi și probleme

| În ghid | În engleză | Complexitate |
|---|---|---|
| Căutare pe rând (secvențială) | **Linear Search**, Sequential Search | O(n) |
| Căutare binară | **Binary Search** | O(log n) |
| Dulapul cu sertare (tabelă de dispersie) | **Hash Table**, Hash Map, Dictionary; în PHP *associative array* | O(1) în medie |
| Două bucle una în alta | **Nested Loops** | O(n · m) |
| Comparator | **Comparator**, Comparison Function | |
| Sortarea din PHP (`usort`) | **Hybrid Sort** (Quick Sort + Insertion Sort) | O(n log n) |
| Sortare prin interclasare (la curs) | **Merge Sort** | O(n log n) |
| Sortare rapidă (la curs) | **Quick Sort** | O(n log n) în medie |
| Index în baza de date | **Database Index**, B-Tree | O(log n) |
| Citirea întregului tabel | **Full Table Scan** | O(n) |
| Problema N+1 | **N+1 Query Problem**; soluția: **Eager Loading**, JOIN | |
| Împachetarea în cutii | **Bin Packing Problem** | |
| „Cutia curentă” | **Next Fit** | |
| „Prima cutie în care încape” | **First Fit** | |
| „Întâi cele grele” | **First Fit Decreasing (FFD)** | |
| Încărcarea camionului (problema rucsacului) | **0/1 Knapsack Problem** | |
| Drumul cel mai scurt | **Shortest Path** | |
| Parcurgerea în lățime („valuri în apă”) | **Breadth-First Search (BFS)** | O(V + E) |
| Parcurgerea în adâncime | **Depth-First Search (DFS)** | O(V + E) |
| Drumul cel mai ieftin (la curs) | **Dijkstra's Algorithm** | |
| Ruta de colectare (problema comis-voiajorului) | **Travelling Salesman Problem (TSP)** | |
| Cel mai apropiat vecin | **Nearest Neighbor Heuristic** | O(n²) |
| Toate ordinile posibile | **Permutations** | O(n!) |

### Structuri de date și alți termeni

| În ghid | În engleză |
|---|---|
| Listă / vector | **Array**, List |
| Coadă (primul venit, primul servit) | **Queue**, FIFO (*First In, First Out*) |
| Stivă (ultimul venit, primul servit) | **Stack**, LIFO (*Last In, First Out*) |
| Coadă cu priorități | **Priority Queue**, Heap |
| Graf, nod, muchie | **Graph**, Node / Vertex, Edge |
| Hartă ca o matrice de celule | **Grid** |
| Matricea distanțelor | **Distance Matrix** |
| Complexitate, „de ordinul lui n” | **Time Complexity**, **Big O Notation** |
| Cazul cel mai nefavorabil | **Worst Case** |
| Cazul mediu | **Average Case** |
| Soluția cea mai bună | **Optimal Solution** |
| Test automat | **Unit Test** |
| Interogare SQL | **Query** |

