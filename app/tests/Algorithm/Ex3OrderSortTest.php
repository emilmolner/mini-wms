<?php

namespace App\Tests\Algorithm;

use App\Algorithm\Sort\OrderComparator;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/** Exercitiul 3: rulati cu  php vendor/bin/phpunit --testdox --filter Ex3 */
final class Ex3OrderSortTest extends TestCase
{
    private static function order(string $number, int $priority, string $createdAt): array
    {
        return ['numar' => $number, 'client' => 'Bricolaj Bacau SRL', 'prioritate' => $priority, 'dataCreare' => $createdAt];
    }

    #[TestDox('prioritatea 1 vine inaintea prioritatii 2')]
    public function testPriorityFirst(): void
    {
        $c = new OrderComparator();
        $urgent = self::order('CMD-00009', 1, '2026-11-26 10:00:00');
        $normal = self::order('CMD-00001', 2, '2026-11-20 10:00:00');

        self::assertLessThan(0, $c->compare($urgent, $normal));
        self::assertGreaterThan(0, $c->compare($normal, $urgent));
    }

    #[TestDox('la aceeasi prioritate, comanda mai veche vine prima')]
    public function testOlderFirst(): void
    {
        $c = new OrderComparator();
        $old = self::order('CMD-00009', 2, '2026-11-20 08:00:00');
        $new = self::order('CMD-00001', 2, '2026-11-25 08:00:00');

        self::assertLessThan(0, $c->compare($old, $new));
    }

    #[TestDox('la egalitate decide numarul comenzii; o comanda e "egala" cu ea insasi')]
    public function testNumberBreaksTies(): void
    {
        $c = new OrderComparator();
        $a = self::order('CMD-00001', 3, '2026-11-20 08:00:00');
        $b = self::order('CMD-00002', 3, '2026-11-20 08:00:00');

        self::assertLessThan(0, $c->compare($a, $b));
        self::assertSame(0, $c->compare($a, $a));
    }

    #[TestDox('usort cu comparatorul produce coada corecta')]
    public function testFullQueue(): void
    {
        $orders = [
            self::order('CMD-00005', 3, '2026-11-18 09:00:00'),
            self::order('CMD-00004', 1, '2026-11-25 09:00:00'),
            self::order('CMD-00003', 2, '2026-11-19 09:00:00'),
            self::order('CMD-00002', 1, '2026-11-21 09:00:00'),
            self::order('CMD-00001', 2, '2026-11-19 09:00:00'),
        ];
        usort($orders, (new OrderComparator())->compare(...));

        self::assertSame(['CMD-00002', 'CMD-00004', 'CMD-00001', 'CMD-00003', 'CMD-00005'], array_column($orders, 'numar'));
    }
}
