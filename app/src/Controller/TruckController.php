<?php

namespace App\Controller;

use App\Algorithm\Optimization\TruckLoader;
use Doctrine\DBAL\Connection;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/camion')]
#[OA\Tag(name: 'Camion: metoda lacoma vs. toate combinatiile (Ex. 6)')]
final class TruckController extends AbstractController
{
    public function __construct(private readonly TruckLoader $loader)
    {
    }

    /** EXERCITIUL 6: comenzi scrise de noi. In exemplu, metoda lacoma obtine 160 de lei, iar cea mai buna incarcare 220. */
    #[Route('/incarcare', methods: ['POST'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(example: [
        'capacitate' => 50,
        'comenzi' => [
            ['nume' => 'Paleti gresie', 'greutate' => 10, 'valoare' => 60],
            ['nume' => 'Saci ciment', 'greutate' => 20, 'valoare' => 100],
            ['nume' => 'Cutii faianta', 'greutate' => 30, 'valoare' => 120],
        ],
    ]))]
    public function load(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $capacity = $data['capacitate'] ?? null;
        $items = $data['comenzi'] ?? null;

        if (!\is_int($capacity) || $capacity < 1 || $capacity > 100000 || !\is_array($items) || \count($items) > 20) {
            throw new \InvalidArgumentException('Trimite {"capacitate": 1..100000, "comenzi": [...]} cu cel mult 20 de comenzi.');
        }
        foreach ($items as $item) {
            if (!\is_string($item['nume'] ?? null) || !\is_int($item['greutate'] ?? null) || $item['greutate'] < 1 || !\is_int($item['valoare'] ?? null)) {
                throw new \InvalidArgumentException('Fiecare comanda are forma {"nume": "Saci ciment", "greutate": 20, "valoare": 100}.');
            }
        }

        return $this->json(['capacitate' => $capacity] + $this->compare($items, $capacity));
    }

    /** EXERCITIUL 6: ce comenzi din coada de azi incarcam in camion? */
    #[Route('/plan', methods: ['GET'])]
    #[OA\Parameter(name: 'capacitate', in: 'query', description: 'cate kg duce camionul', schema: new OA\Schema(type: 'integer', default: 800))]
    #[OA\Parameter(name: 'comenzi', in: 'query', description: 'cate comenzi din coada luam in calcul (maximum 20)', schema: new OA\Schema(type: 'integer', default: 15))]
    public function plan(
        Connection $db,
        #[MapQueryParameter(name: 'capacitate')] int $capacity = 800,
        #[MapQueryParameter(name: 'comenzi')] int $orders = 15,
    ): JsonResponse {
        $items = $db->fetchAllAssociative(
            'SELECT o.number AS nume, CAST(SUM(ol.quantity * p.weight_kg) AS SIGNED) AS greutate, CAST(SUM(ol.quantity * p.price) AS SIGNED) AS valoare
             FROM orders o
             JOIN order_line ol ON ol.order_id = o.id
             JOIN product p ON p.id = ol.product_id
             GROUP BY o.id
             ORDER BY o.priority, o.created_at, o.number
             LIMIT :n',
            ['n' => min(max(1, $orders), 20)],
            ['n' => \Doctrine\DBAL\ParameterType::INTEGER],
        );
        $items = array_map(fn (array $i) => ['nume' => $i['nume'], 'greutate' => (int) $i['greutate'], 'valoare' => (int) $i['valoare']], $items);

        return $this->json(['capacitate' => $capacity, 'comenziDinCoada' => $items] + $this->compare($items, $capacity));
    }

    /** @param list<array{nume: string, greutate: int, valoare: int}> $items */
    private function compare(array $items, int $capacity): array
    {
        $result = [];
        foreach (['metodaLacoma' => 'lacom', 'toateCombinatiile' => 'toateCombinatiile'] as $label => $method) {
            $start = hrtime(true);
            $result[$label] = $this->loader->$method($items, $capacity) + ['timpMs' => round((hrtime(true) - $start) / 1e6, 3)];
        }
        $result['toateCombinatiile']['varianteVerificate'] = $this->loader->combinationsChecked;

        return $result;
    }
}
