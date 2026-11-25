<?php

namespace App\Tests\Algorithm;

use App\Algorithm\Hash\MissingCodeFinder;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/** Exercitiul 2: rulati cu  php vendor/bin/phpunit --testdox --filter Ex2 */
final class Ex2MissingCodeFinderTest extends TestCase
{
    #[TestDox('gaseste codurile care lipsesc, in ordinea in care au fost cerute')]
    public function testFindsMissingCodes(): void
    {
        $catalog = ['PROD-1', 'PROD-2', 'PROD-3', 'PROD-4'];
        $requested = ['PROD-9', 'PROD-2', 'PROD-7', 'PROD-4'];

        self::assertSame(['PROD-9', 'PROD-7'], (new MissingCodeFinder())->findMissingFast($requested, $catalog));
    }

    #[TestDox('da acelasi rezultat ca varianta naiva')]
    public function testSameResultAsNaive(): void
    {
        $catalog = [];
        for ($i = 0; $i < 2000; $i += 2) {
            $catalog[] = "PROD-$i";
        }
        $requested = [];
        for ($i = 0; $i < 300; $i += 3) {
            $requested[] = "PROD-$i";
        }

        $finder = new MissingCodeFinder();
        self::assertSame($finder->findMissingNaive($requested, $catalog), $finder->findMissingFast($requested, $catalog));
    }

    #[TestDox('face cel mult n + m operatii (n = catalog, m = coduri cerute)')]
    public function testLinearNumberOfOperations(): void
    {
        $catalog = array_map(fn (int $i) => "PROD-$i", range(1, 10000));
        $requested = array_map(fn (int $i) => "PROD-X$i", range(1, 500));

        $finder = new MissingCodeFinder();
        self::assertCount(500, $finder->findMissingFast($requested, $catalog));
        self::assertGreaterThan(0, $finder->operations, 'nu uita sa cresti $this->operations');
        self::assertLessThanOrEqual(10000 + 500, $finder->operations);
    }

    #[TestDox('functioneaza si cu liste goale')]
    public function testEmptyLists(): void
    {
        $finder = new MissingCodeFinder();
        self::assertSame([], $finder->findMissingFast([], ['PROD-1']));
        self::assertSame(['PROD-1'], $finder->findMissingFast(['PROD-1'], []));
    }
}
