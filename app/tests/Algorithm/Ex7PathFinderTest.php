<?php

namespace App\Tests\Algorithm;

use App\Algorithm\Graph\PathFinder;
use App\Warehouse\WarehouseMap;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/** Exercitiul 7: rulati cu  php vendor/bin/phpunit --testdox --filter Ex7 */
final class Ex7PathFinderTest extends TestCase
{
    #[TestDox('drumul de la START la W08 are 28 de pasi')]
    public function testShortestPathInWarehouse(): void
    {
        $map = new WarehouseMap();
        $path = (new PathFinder())->shortestPath($map, $map->start(), $map->positionOf('W08'));

        self::assertNotNull($path, 'shortestPath a intors null');
        self::assertCount(29, $path, '28 de pasi = 29 de celule (cu tot cu start)');
        self::assertSame($map->start(), $path[0]);
        self::assertSame($map->positionOf('W08'), $path[28]);
    }

    #[TestDox('drumul merge doar pe culoare, cate o celula vecina pe rand')]
    public function testPathIsValid(): void
    {
        $map = new WarehouseMap();
        $path = (new PathFinder())->shortestPath($map, $map->positionOf('B08'), $map->positionOf('T02'));
        self::assertNotNull($path);

        for ($i = 0; $i < \count($path); ++$i) {
            self::assertTrue($map->isWalkable(...$path[$i]), 'drumul trece printr-un raft sau perete');
            if ($i > 0) {
                $step = abs($path[$i][0] - $path[$i - 1][0]) + abs($path[$i][1] - $path[$i - 1][1]);
                self::assertSame(1, $step, 'doua celule consecutive din drum trebuie sa fie vecine');
            }
        }
    }

    #[TestDox('ocoleste paletul P (drumul cel mai scurt trece pe langa el)')]
    public function testGoesAroundObstacle(): void
    {
        $map = new WarehouseMap([
            '#######',
            '#S.P..#',
            '#.###.#',
            '#.....#',
            '#######',
        ]);
        $path = (new PathFinder())->shortestPath($map, [1, 1], [1, 4]);

        self::assertNotNull($path);
        self::assertCount(10, $path);
    }

    #[TestDox('intoarce null daca destinatia nu se poate atinge')]
    public function testUnreachable(): void
    {
        $map = new WarehouseMap([
            '#######',
            '#S.#..#',
            '#######',
        ]);
        self::assertNull((new PathFinder())->shortestPath($map, [1, 1], [1, 5]));
    }

    #[TestDox('daca pornim chiar din destinatie, drumul are o singura celula')]
    public function testSameCell(): void
    {
        $map = new WarehouseMap();
        self::assertSame([$map->start()], (new PathFinder())->shortestPath($map, $map->start(), $map->start()));
    }
}
