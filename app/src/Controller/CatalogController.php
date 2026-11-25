<?php

namespace App\Controller;

use App\Algorithm\Hash\MissingCodeFinder;
use App\Algorithm\Search\BinarySearch;
use App\Algorithm\Search\LinearSearch;
use App\Repository\ProductRepository;
use Doctrine\DBAL\Connection;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/catalog')]
#[OA\Tag(name: 'Catalog: cautare (Ex. 1), tabela de dispersie (Ex. 2), index (Ex. 4a)')]
final class CatalogController extends AbstractController
{
    public function __construct(private readonly ProductRepository $products)
    {
    }

    /** EXERCITIUL 1: cauta un cod in toate codurile din catalog (sortate), secvential sau binar. */
    #[Route('/cautare', methods: ['GET'])]
    #[OA\Parameter(name: 'cod', in: 'query', example: 'PROD-0791900')]
    #[OA\Parameter(name: 'metoda', in: 'query', schema: new OA\Schema(type: 'string', enum: ['secventiala', 'binara'], default: 'binara'))]
    public function search(
        #[MapQueryParameter(name: 'cod')] string $code,
        #[MapQueryParameter(name: 'metoda')] string $method = 'binara',
    ): JsonResponse {
        $codes = $this->products->allCodesSorted();
        $algorithm = match ($method) {
            'secventiala' => new LinearSearch(),
            'binara' => new BinarySearch(),
            default => throw new \InvalidArgumentException('metoda trebuie sa fie "secventiala" sau "binara".'),
        };

        $start = hrtime(true);
        $position = $algorithm->find($codes, strtoupper(trim($code)));
        $ms = (hrtime(true) - $start) / 1e6;

        return $this->json([
            'cod' => $code,
            'metoda' => $method,
            'gasit' => $position >= 0,
            'pozitie' => $position,
            'produseInCatalog' => \count($codes),
            'comparatii' => $algorithm->comparisons,
            'timpMs' => round($ms, 3),
        ]);
    }

    /** EXERCITIUL 2: care dintre codurile trimise nu exista in catalog? Varianta naiva vs. tabela de dispersie. */
    #[Route('/verificare', methods: ['POST'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(example: ['coduri' => ['PROD-0791900', 'PROD-1583800', 'PROD-0000001', 'PROD-INEXISTENT']]))]
    public function check(Request $request, MissingCodeFinder $finder): JsonResponse
    {
        $codes = json_decode($request->getContent(), true)['coduri'] ?? null;
        if (!\is_array($codes) || [] === $codes || \count($codes) > 500 || array_filter($codes, fn ($c) => !\is_string($c))) {
            throw new \InvalidArgumentException('Trimite {"coduri": [...]} cu intre 1 si 500 de coduri (texte).');
        }

        return $this->json($this->compareFinders($finder, $codes, true));
    }

    /**
     * EXERCITIUL 2 (varianta mare): alege la intamplare $count coduri, jumatate existente, jumatate inventate,
     * si le verifica cu ambele metode. Aici se vede diferenta dintre O(m * n) si O(n + m).
     */
    #[Route('/verificare-aleatoare', methods: ['GET'])]
    #[OA\Parameter(name: 'numar', in: 'query', description: 'cate coduri verificam', schema: new OA\Schema(type: 'integer', default: 200))]
    public function checkRandom(MissingCodeFinder $finder, #[MapQueryParameter(name: 'numar')] int $count = 200): JsonResponse
    {
        $count = min(max(1, $count), 1000);
        $catalog = $this->products->allCodesSorted();

        mt_srand(42); // aceleasi coduri la fiecare rulare
        $codes = [];
        for ($i = 0; $i < $count; ++$i) {
            $codes[] = 0 === $i % 2 ? $catalog[mt_rand(0, \count($catalog) - 1)] : \sprintf('PROD-X%06d', mt_rand(0, 999999));
        }

        return $this->json($this->compareFinders($finder, $codes, false, $catalog));
    }

    /**
     * EXERCITIUL 4a: cauta un produs direct in MySQL si arata cum l-a cautat MySQL (EXPLAIN ANALYZE).
     * Fara index pe coloana code: "Table scan" (citeste tot tabelul). Cu index: "Index lookup".
     */
    #[Route('/produs', methods: ['GET'])]
    #[OA\Parameter(name: 'cod', in: 'query', example: 'PROD-0791900')]
    public function product(#[MapQueryParameter(name: 'cod')] string $code, Connection $db): JsonResponse
    {
        $sql = 'SELECT p.id, p.code AS cod, p.name AS denumire, p.category AS categorie,
                       p.weight_kg AS greutate_kg, p.price AS pret_lei, l.code AS locatie
                FROM product p JOIN location l ON l.id = p.location_id
                WHERE p.code = :code';
        $params = ['code' => strtoupper(trim($code))];
        $db->executeQuery('SELECT 1'); // deschidem conexiunea inainte, ca sa masuram doar cautarea

        $start = hrtime(true);
        $product = $db->fetchAssociative($sql, $params);
        $ms = (hrtime(true) - $start) / 1e6;

        $plan = array_values(array_filter(explode("\n", (string) $db->fetchOne('EXPLAIN ANALYZE '.$sql, $params))));

        return $this->json([
            'produs' => $product ?: null,
            'timpMs' => round($ms, 3),
            'folosesteIndex' => !preg_grep('/Table scan on p/', $plan),
            'cumACautatMySQL' => $plan,
        ]);
    }

    /**
     * @param list<string>      $codes
     * @param list<string>|null $catalog
     */
    private function compareFinders(MissingCodeFinder $finder, array $codes, bool $listMissing, ?array $catalog = null): array
    {
        $catalog ??= $this->products->allCodesSorted();
        $result = ['coduriVerificate' => \count($codes), 'produseInCatalog' => \count($catalog)];

        foreach (['naiva' => 'findMissingNaive', 'cuDispersie' => 'findMissingFast'] as $name => $method) {
            $start = hrtime(true);
            $missing = $finder->$method($codes, $catalog);
            $result[$name] = [
                ($listMissing ? 'lipsa' : 'numarLipsa') => $listMissing ? $missing : \count($missing),
                'operatii' => $finder->operations,
                'timpMs' => round((hrtime(true) - $start) / 1e6, 3),
            ];
        }

        return $result;
    }
}
