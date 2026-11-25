<?php

namespace App\Tests\Algorithm;

use App\Algorithm\Graph\PickingRoute;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/** Exercitiul 8: rulati cu  php vendor/bin/phpunit --testdox --filter Ex8 */
final class Ex8PickingRouteTest extends TestCase
{
    /** distantele pentru comanda CMD-00017; locatiile sunt, in ordine: START, B02, H03, B07, T04, W08, T07 */
    private const CMD_00017 = [
        [0, 1, 8, 6, 21, 28, 24],
        [1, 0, 9, 5, 22, 29, 25],
        [8, 9, 0, 10, 17, 22, 20],
        [6, 5, 10, 0, 25, 24, 22],
        [21, 22, 17, 25, 0, 7, 3],
        [28, 29, 22, 24, 7, 0, 6],
        [24, 25, 20, 22, 3, 6, 0],
    ];

    #[TestDox('cel mai apropiat vecin pentru CMD-00017: START, B02, B07, H03, T04, T07, W08, START (70 de pasi)')]
    public function testNearestNeighbor(): void
    {
        $route = (new PickingRoute())->nearestNeighbor(self::CMD_00017);

        self::assertSame([0, 1, 3, 2, 4, 6, 5, 0], $route);
        self::assertSame(70, PickingRoute::length(self::CMD_00017, $route));
    }

    #[TestDox('cel mai apropiat vecin nu e mereu cel mai bun: "toate variantele" gaseste 64 de pasi')]
    public function testNotAlwaysOptimal(): void
    {
        $best = (new PickingRoute())->allRoutes(self::CMD_00017);
        self::assertSame(64, PickingRoute::length(self::CMD_00017, $best));
    }

    #[TestDox('ruta pleaca din start, trece o data prin fiecare locatie si se intoarce')]
    public function testRouteIsComplete(): void
    {
        mt_srand(5);
        $n = 8;
        $dist = array_fill(0, $n, array_fill(0, $n, 0));
        for ($i = 0; $i < $n; ++$i) {
            for ($j = $i + 1; $j < $n; ++$j) {
                $dist[$i][$j] = $dist[$j][$i] = mt_rand(1, 30);
            }
        }

        $route = (new PickingRoute())->nearestNeighbor($dist);
        self::assertSame(0, $route[0]);
        self::assertSame(0, $route[\count($route) - 1]);
        $middle = \array_slice($route, 1, -1);
        sort($middle);
        self::assertSame(range(1, $n - 1), $middle);
    }
}
