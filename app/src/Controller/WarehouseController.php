<?php

namespace App\Controller;

use App\Algorithm\Graph\PathFinder;
use App\Warehouse\WarehouseMap;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/depozit')]
#[OA\Tag(name: 'Depozit: harta si drumul cel mai scurt (Ex. 7)')]
final class WarehouseController extends AbstractController
{
    /** Harta depozitului si lista locatiilor de pe care se iau produse. */
    #[Route('/harta', methods: ['GET'])]
    public function map(): JsonResponse
    {
        $map = new WarehouseMap();

        return $this->json([
            'legenda' => '# perete, = raft, . culoar, S start (rampa), P palet pe culoar',
            'harta' => $map->render(),
            'locatii' => array_column($map->pickLocations(), 'code'),
        ]);
    }

    /** EXERCITIUL 7: drumul cel mai scurt de la o locatie la alta. */
    #[Route('/drum', methods: ['GET'])]
    #[OA\Parameter(name: 'spre', in: 'query', example: 'W08')]
    #[OA\Parameter(name: 'de_la', in: 'query', schema: new OA\Schema(type: 'string', default: 'START'))]
    public function path(
        PathFinder $pathFinder,
        #[MapQueryParameter(name: 'spre')] string $to,
        #[MapQueryParameter(name: 'de_la')] string $from = 'START',
    ): JsonResponse {
        $map = new WarehouseMap();
        $toPosition = $map->positionOf($to);
        $path = $pathFinder->shortestPath($map, $map->positionOf($from), $toPosition);

        return $this->json([
            'de_la' => $from,
            'spre' => $to,
            'gasit' => null !== $path,
            'pasi' => null !== $path ? \count($path) - 1 : null,
            'celuleVerificate' => $pathFinder->visited,
            'drum' => array_map(fn (array $cell) => WarehouseMap::code(...$cell), $path ?? []),
            'harta' => $map->render($path ?? [], [$toPosition]),
        ]);
    }
}
