<?php

namespace App\Tests\Algorithm;

use App\Algorithm\Search\BinarySearch;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/** Exercitiul 1: rulati cu  php vendor/bin/phpunit --testdox --filter Ex1 */
final class Ex1BinarySearchTest extends TestCase
{
    private const CODES = ['PROD-0000100', 'PROD-0000200', 'PROD-0000300', 'PROD-0000400', 'PROD-0000500'];

    #[TestDox('gaseste codul din mijloc, primul si ultimul')]
    public function testFindsExistingCodes(): void
    {
        $search = new BinarySearch();
        self::assertSame(2, $search->find(self::CODES, 'PROD-0000300'));
        self::assertSame(0, $search->find(self::CODES, 'PROD-0000100'));
        self::assertSame(4, $search->find(self::CODES, 'PROD-0000500'));
    }

    #[TestDox('intoarce -1 pentru un cod care nu exista')]
    public function testReturnsMinusOneWhenMissing(): void
    {
        $search = new BinarySearch();
        self::assertSame(-1, $search->find(self::CODES, 'PROD-0000001'), 'mai mic decat toate');
        self::assertSame(-1, $search->find(self::CODES, 'PROD-0000250'), 'intre doua coduri');
        self::assertSame(-1, $search->find(self::CODES, 'PROD-9999999'), 'mai mare decat toate');
        self::assertSame(-1, $search->find([], 'PROD-0000100'), 'lista goala');
    }

    #[TestDox('face cel mult 20 de comparatii intr-o lista de 1.000.000 de coduri')]
    public function testUsesLogarithmicNumberOfComparisons(): void
    {
        $codes = [];
        for ($i = 0; $i < 1_000_000; ++$i) {
            $codes[] = \sprintf('PROD-%07d', $i);
        }

        $search = new BinarySearch();
        self::assertSame(765432, $search->find($codes, 'PROD-0765432'));
        self::assertGreaterThan(0, $search->comparisons, 'nu uita sa cresti $this->comparisons');
        self::assertLessThanOrEqual(20, $search->comparisons);

        self::assertSame(-1, $search->find($codes, 'PROD-X'));
        self::assertLessThanOrEqual(21, $search->comparisons);
    }
}
