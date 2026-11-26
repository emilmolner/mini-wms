<?php

namespace App\Command;

use App\Warehouse\WarehouseMap;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Umple baza de date cu date de test: locatiile din harta, 200.000 de produse, 8 clienti, 3.000 de comenzi.
 *
 * Valorile "la intamplare" sunt calculate cu CRC32 (o functie care transforma un text intr-un numar),
 * deci sunt ACELEASI la toata lumea: comanda CMD-00017 arata la fel pe toate calculatoarele.
 */
#[AsCommand(name: 'app:date:genereaza', description: 'Sterge si genereaza din nou datele de test')]
final class SeedCommand
{
    private const PRODUCTS = 200000;
    private const ORDERS = 3000;

    public function __construct(private readonly Connection $db)
    {
    }

    public function __invoke(SymfonyStyle $io): int
    {
        $start = microtime(true);

        $this->db->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
        foreach (['order_line', 'orders', 'customer', 'product', 'location'] as $table) {
            $this->db->executeStatement("TRUNCATE TABLE $table");
        }
        $this->db->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
        $this->db->executeStatement('SET SESSION cte_max_recursion_depth = 1000000');

        // 1. locatiile: celulele de culoar de langa rafturi
        $map = new WarehouseMap();
        foreach ($map->pickLocations() as $location) {
            $this->db->insert('location', ['code' => $location['code'], 'map_row' => $location['row'], 'map_col' => $location['col']]);
        }
        $locations = \count($map->pickLocations());
        $io->writeln("Locatii: $locations");

        // 2. produsele; codul = i * 7919 modulo 10.000.000 => coduri unice, dar amestecate
        $this->db->executeStatement(<<<'SQL'
            INSERT INTO product (code, name, category, weight_kg, price, location_id)
            WITH RECURSIVE seq (i) AS (SELECT 1 UNION ALL SELECT i + 1 FROM seq WHERE i < :products)
            SELECT
                CONCAT('PROD-', LPAD((i * 7919) % 10000000, 7, '0')),
                CONCAT(ELT(1 + CRC32(CONCAT('nume', i)) % 14, 'Bormasina', 'Surubelnita', 'Vopsea lavabila', 'Gresie',
                           'Faianta', 'Cablu electric', 'Bec LED', 'Robinet', 'Parchet', 'Adeziv', 'Ciment', 'Lampa',
                           'Ghiveci', 'Furtun'), ' model ', i),
                ELT(1 + CRC32(CONCAT('categorie', i)) % 6, 'Scule', 'Constructii', 'Electrice', 'Sanitare', 'Gradina', 'Decoratiuni'),
                1 + CRC32(CONCAT('greutate', i)) % 25,
                10 + CRC32(CONCAT('pret', i)) % 2000,
                1 + CRC32(CONCAT('locatie', i)) % :locations
            FROM seq
            SQL, ['products' => self::PRODUCTS, 'locations' => $locations]);
        $io->writeln('Produse: '.self::PRODUCTS);

        // 3. clientii (id-urile 1..8, in ordinea de mai jos)
        $customers = ['Bricolaj Bacau SRL', 'Constructii Moldova SA', 'Ionescu Ana', 'Instal Siret SRL',
            'Casa Verde Onesti SRL', 'Renovari Rapide SRL', 'Electro Trotus SRL', 'Tamplaria Bistrita SRL'];
        foreach ($customers as $name) {
            $this->db->insert('customer', ['name' => $name]);
        }
        $io->writeln('Clienti: '.\count($customers));

        // 4. comenzile, din ultimele 10 zile
        $this->db->executeStatement(<<<'SQL'
            INSERT INTO orders (number, customer_id, priority, created_at)
            WITH RECURSIVE seq (i) AS (SELECT 1 UNION ALL SELECT i + 1 FROM seq WHERE i < :orders)
            SELECT
                CONCAT('CMD-', LPAD(i, 5, '0')),
                1 + CRC32(CONCAT('client', i)) % 8,
                1 + CRC32(CONCAT('prioritate', i)) % 3,
                TIMESTAMP('2026-11-27 08:00:00') - INTERVAL (CRC32(CONCAT('data', i)) % 14400) MINUTE
            FROM seq
            SQL, ['orders' => self::ORDERS]);
        $io->writeln('Comenzi: '.self::ORDERS);

        // 5. liniile: comanda cu id-ul i are 1 + (i % 6) produse
        $this->db->executeStatement(<<<'SQL'
            INSERT INTO order_line (order_id, product_id, quantity)
            WITH RECURSIVE g (k) AS (SELECT 1 UNION ALL SELECT k + 1 FROM g WHERE k < 6)
            SELECT o.id, 1 + CRC32(CONCAT('produs', o.id, '-', g.k)) % :products, 1 + CRC32(CONCAT('cantitate', o.id, '-', g.k)) % 3
            FROM orders o JOIN g ON g.k <= 1 + o.id % 6
            ORDER BY o.id, g.k
            SQL, ['products' => self::PRODUCTS]);

        $io->success(\sprintf('Gata in %.1f secunde.', microtime(true) - $start));

        return 0;
    }
}
