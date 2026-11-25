<?php

namespace App\Tests\Algorithm;

use App\Algorithm\Packing\BoxPacker;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/** Exercitiul 5: rulati cu  php vendor/bin/phpunit --testdox --filter Ex5 */
final class Ex5BoxPackerTest extends TestCase
{
    /** bucatile comenzii CMD-00587 */
    private const WEIGHTS = [12, 12, 18, 18, 4, 13, 23, 23, 17, 17, 17];

    #[TestDox('5a: "prima cutie in care incape" pentru comanda CMD-00587 foloseste 8 cutii')]
    public function testFirstFit(): void
    {
        self::assertSame(
            [[12, 12, 4], [18], [18], [13, 17], [23], [23], [17], [17]],
            (new BoxPacker())->firstFit(self::WEIGHTS, 30),
        );
    }

    #[TestDox('5b: "intai cele grele" pentru comanda CMD-00587 foloseste doar 7 cutii')]
    public function testFirstFitDecreasing(): void
    {
        self::assertSame(
            [[23, 4], [23], [18, 12], [18, 12], [17, 13], [17], [17]],
            (new BoxPacker())->firstFitDecreasing(self::WEIGHTS, 30),
        );
    }

    #[TestDox('5a: nicio cutie nu depaseste capacitatea si nu se pierde nicio bucata')]
    public function testNoBoxIsOverloaded(): void
    {
        mt_srand(3);
        $weights = array_map(fn () => mt_rand(1, 20), range(1, 200));

        foreach (['firstFit', 'firstFitDecreasing'] as $method) {
            $boxes = (new BoxPacker())->$method($weights, 20);
            self::assertNotEmpty($boxes, "$method nu intoarce nicio cutie");
            foreach ($boxes as $box) {
                self::assertLessThanOrEqual(20, array_sum($box), "$method: cutie prea grea");
            }
            self::assertSame(array_sum($weights), array_sum(array_map('array_sum', $boxes)), "$method: s-au pierdut bucati");
        }
    }

    #[TestDox('5a: fara bucati nu e nevoie de nicio cutie')]
    public function testEmptyOrder(): void
    {
        self::assertSame([], (new BoxPacker())->firstFit([], 30));
    }
}
