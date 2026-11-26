<?php

namespace App\Controller;

use App\Algorithm\Graph\DistanceMatrix;
use App\Algorithm\Graph\PathFinder;
use App\Algorithm\Graph\PickingRoute;
use App\Algorithm\Packing\BoxPacker;
use App\Algorithm\Sort\OrderComparator;
use Symfony\Bridge\Doctrine\Middleware\Debug\DebugDataHolder;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use App\Repository\OrderRepository;
use App\Warehouse\WarehouseMap;
use Doctrine\DBAL\Connection;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/comenzi')]
#[OA\Tag(name: 'Comenzi: coada (Ex. 3), raport N+1 (Ex. 4b), cutii (Ex. 5), ruta (Ex. 8)')]
final class OrderController extends AbstractController
{
    public function __construct(private readonly Connection $db)
    {
    }

    /** EXERCITIUL 3: coada de pregatire a comenzilor, sortata dupa regulile din OrderComparator. */
    #[Route('/coada', methods: ['GET'])]
    #[OA\Parameter(name: 'limita', in: 'query', description: 'cate comenzi afisam', schema: new OA\Schema(type: 'integer', default: 15))]
    public function queue(OrderComparator $comparator, #[MapQueryParameter(name: 'limita')] int $limit = 15): JsonResponse
    {
        // comenzile vin din baza de date in ordinea in care au fost introduse; sortarea o facem NOI, in PHP
        $orders = $this->db->fetchAllAssociative(
            "SELECT number AS numar, customer AS client, priority AS prioritate,
                    DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS dataCreare
             FROM orders ORDER BY id",
        );
        foreach ($orders as &$order) {
            $order['prioritate'] = (int) $order['prioritate'];
        }
        unset($order);

        $start = hrtime(true);
        usort($orders, $comparator->compare(...));
        $ms = (hrtime(true) - $start) / 1e6;

        return $this->json([
            'totalComenzi' => \count($orders),
            'timpMs' => round($ms, 3),
            'coada' => \array_slice($orders, 0, max(1, $limit)),
        ]);
    }

    /** EXERCITIUL 4b: toate comenzile unui client, cu produsele lor. Cate query-uri SQL se trimit la MySQL? */
    #[Route('/raport', methods: ['GET'])]
    #[OA\Parameter(name: 'client', in: 'query', schema: new OA\Schema(type: 'string', default: 'Bricolaj Bacau SRL'))]
    #[OA\Parameter(name: 'metoda', in: 'query', schema: new OA\Schema(type: 'string', enum: ['naiva', 'join'], default: 'naiva'))]
    public function report(
        OrderRepository $repository,
        #[Autowire(service: 'doctrine.debug_data_holder')] DebugDataHolder $sqlLog,
        #[MapQueryParameter] string $client = 'Bricolaj Bacau SRL',
        #[MapQueryParameter(name: 'metoda')] string $method = 'naiva',
    ): JsonResponse {
        $sqlLog->reset(); // Symfony tine minte (in modul dev) fiecare query trimis; incepem numaratoarea de la 0
        $start = hrtime(true);

        $orders = 'join' === $method ? $repository->findByCustomerWithJoin($client) : $repository->findByCustomerNaive($client);

        $report = [];
        foreach ($orders as $order) {
            $products = [];
            foreach ($order->getLines() as $line) {             // aici Doctrine poate trimite un query...
                $products[] = [
                    'cod' => $line->getProduct()->getCode(),     // ...si aici inca unul, pentru fiecare produs
                    'denumire' => $line->getProduct()->getName(),
                    'cantitate' => $line->getQuantity(),
                ];
            }
            $report[] = ['numar' => $order->getNumber(), 'produse' => $products];
        }

        // query-urile trimise, grupate: acelasi SQL (cu alt parametru) se numara o singura data, cu "deCateOri"
        $queries = array_column($sqlLog->getData()['default'] ?? [], 'sql');
        $groups = [];
        foreach (array_count_values($queries) as $sql => $times) {
            $groups[] = ['deCateOri' => $times, 'sql' => $sql];
        }

        return $this->json([
            'client' => $client,
            'metoda' => $method,
            'comenzi' => \count($report),
            'queryuriSql' => \count($queries),
            'tipuriDeQuery' => $groups,
            'timpMs' => round((hrtime(true) - $start) / 1e6, 3),
            'primeleDoua' => \array_slice($report, 0, 2),
        ]);
    }

    /** EXERCITIUL 5: in cate cutii incap produsele comenzii? (exemplu bun: CMD-00587) */
    #[Route('/{numar}/impachetare', methods: ['GET'])]
    #[OA\Parameter(name: 'numar', in: 'path', example: 'CMD-00587')]
    #[OA\Parameter(name: 'capacitate', in: 'query', description: 'cate kg suporta o cutie', schema: new OA\Schema(type: 'integer', default: 30))]
    public function packing(string $numar, BoxPacker $packer, #[MapQueryParameter(name: 'capacitate')] int $capacity = 30): JsonResponse
    {
        $lines = $this->orderLines($numar);

        // fiecare bucata separat: 2 bucati x 7 kg => [7, 7]
        $weights = [];
        foreach ($lines as $line) {
            if ($line['weight_kg'] > $capacity) {
                throw new \InvalidArgumentException(\sprintf(
                    'Produsul %s (%s) are %d kg pe bucata si nu incape intr-o cutie de %d kg. Mareste capacitatea cutiei.',
                    $line['code'], $line['name'], $line['weight_kg'], $capacity,
                ));
            }
            for ($k = 0; $k < $line['quantity']; ++$k) {
                $weights[] = (int) $line['weight_kg'];
            }
        }

        $result = [
            'comanda' => $numar,
            'detaliiComanda' => $this->orderDetails($numar, $lines),
            'capacitateCutie' => $capacity,
            'bucati' => $weights,
            'totalKg' => array_sum($weights),
        ];
        $methods = [
            'cutiaCurenta' => 'nextFit',
            'primaCutieInCareIncape' => 'firstFit',
            'intaiCeleGrele' => 'firstFitDecreasing',
        ];
        foreach ($methods as $label => $method) {
            $boxes = $packer->$method($weights, $capacity);
            $result[$label] = ['numarCutii' => \count($boxes), 'cutii' => $boxes];
        }

        return $this->json($result);
    }

    /** EXERCITIUL 8: ruta de colectare a produselor unei comenzi: cel mai apropiat vecin vs. toate variantele. */
    #[Route('/{numar}/ruta', methods: ['GET'])]
    #[OA\Parameter(name: 'numar', in: 'path', example: 'CMD-00017')]
    public function route(string $numar, PickingRoute $picking, DistanceMatrix $matrix, PathFinder $pathFinder): JsonResponse
    {
        $map = new WarehouseMap();
        $codes = ['START'];
        $positions = [$map->start()];
        $lines = $this->orderLines($numar);
        foreach ($lines as $line) {
            if (!\in_array($line['location'], $codes, true)) {
                $codes[] = $line['location'];
                $positions[] = [(int) $line['map_row'], (int) $line['map_col']];
            }
        }

        $dist = $matrix->build($map, $positions);

        $start = hrtime(true);
        $nn = $picking->nearestNeighbor($dist);
        $nnMs = (hrtime(true) - $start) / 1e6;

        $start = hrtime(true);
        $best = $picking->allRoutes($dist);
        $bestMs = (hrtime(true) - $start) / 1e6;

        // drumul complet al unei rute, celula cu celula (pentru desenul din pagina web)
        $fullPath = function (array $route) use ($pathFinder, $map, $positions): array {
            $cells = [];
            for ($i = 1; $i < \count($route); ++$i) {
                $cells = [...$cells, ...($pathFinder->shortestPath($map, $positions[$route[$i - 1]], $positions[$route[$i]]) ?? [])];
            }

            return array_map(fn (array $cell) => WarehouseMap::code(...$cell), $cells);
        };

        $toCodes = fn (array $route) => array_map(fn (int $i) => $codes[$i], $route);

        return $this->json([
            'comanda' => $numar,
            'detaliiComanda' => $this->orderDetails($numar, $lines),
            'locatii' => $codes,
            'distante' => $dist,
            'celMaiApropiatVecin' => [
                'ruta' => $toCodes($nn),
                'pasi' => PickingRoute::length($dist, $nn),
                'timpMs' => round($nnMs, 3),
                'drumComplet' => $fullPath($nn),
            ],
            'toateVariantele' => [
                'ruta' => $toCodes($best),
                'pasi' => PickingRoute::length($dist, $best),
                'ruteVerificate' => $picking->routesChecked,
                'timpMs' => round($bestMs, 3),
                'drumComplet' => $fullPath($best),
            ],
            'harta' => $map->render(),
        ]);
    }

    /** @return list<array{code: string, name: string, weight_kg: int, quantity: int, location: string, map_row: int, map_col: int}> */
    private function orderLines(string $number): array
    {
        $lines = $this->db->fetchAllAssociative(
            'SELECT p.code, p.name, p.weight_kg, ol.quantity, l.code AS location, l.map_row, l.map_col
             FROM orders o
             JOIN order_line ol ON ol.order_id = o.id
             JOIN product p ON p.id = ol.product_id
             JOIN location l ON l.id = p.location_id
             WHERE o.number = :number
             ORDER BY ol.id',
            ['number' => strtoupper($number)],
        );
        if ([] === $lines) {
            throw new NotFoundHttpException("Comanda $number nu exista (exemplu corect: CMD-00017).");
        }

        return $lines;
    }

    /**
     * Datele comenzii pentru afisare: clientul, prioritatea, data si produsele (cu cantitate si greutate).
     *
     * @param list<array{code: string, name: string, weight_kg: int, quantity: int, location: string}> $lines
     */
    private function orderDetails(string $number, array $lines): array
    {
        $order = $this->db->fetchAssociative(
            "SELECT customer AS client, priority AS prioritate, DATE_FORMAT(created_at, '%Y-%m-%d %H:%i') AS dataCreare
             FROM orders WHERE number = :number",
            ['number' => strtoupper($number)],
        );

        return [
            'numar' => strtoupper($number),
            'client' => $order['client'],
            'prioritate' => (int) $order['prioritate'],
            'dataCreare' => $order['dataCreare'],
            'produse' => array_map(fn (array $line) => [
                'cod' => $line['code'],
                'denumire' => $line['name'],
                'cantitate' => (int) $line['quantity'],
                'kgPeBucata' => (int) $line['weight_kg'],
                'totalKg' => (int) $line['quantity'] * (int) $line['weight_kg'],
                'locatie' => $line['location'],
            ], $lines),
        ];
    }
}
