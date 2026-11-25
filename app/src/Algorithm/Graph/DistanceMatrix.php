<?php

namespace App\Algorithm\Graph;

use App\Warehouse\WarehouseMap;

/**
 * GATA FACUT: calculeaza distantele (in pasi) intre oricare doua locatii, folosind PathFinder (BFS).
 * In engleza: Distance Matrix (All-Pairs Shortest Paths).
 */
final class DistanceMatrix
{
    public function __construct(private readonly PathFinder $pathFinder)
    {
    }

    /**
     * @param list<array{0: int, 1: int}> $positions
     *
     * @return list<list<int>>
     */
    public function build(WarehouseMap $map, array $positions): array
    {
        $n = \count($positions);
        $dist = array_fill(0, $n, array_fill(0, $n, 0));

        for ($i = 0; $i < $n; ++$i) {
            for ($j = $i + 1; $j < $n; ++$j) {
                $path = $this->pathFinder->shortestPath($map, $positions[$i], $positions[$j]);
                if (null === $path) {
                    throw new \RuntimeException('Nu exista drum intre doua locatii (PathFinder este implementat?).');
                }
                $dist[$i][$j] = $dist[$j][$i] = \count($path) - 1;
            }
        }

        return $dist;
    }
}
