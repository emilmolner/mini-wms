<?php

namespace App\Tests\Algorithm;

use App\Algorithm\Optimization\TruckLoader;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/** Exercitiul 6: rulati cu  php vendor/bin/phpunit --testdox --filter Ex6 */
final class Ex6TruckLoaderTest extends TestCase
{
    /** camion de 50 kg */
    private const EXAMPLE = [
        ['nume' => 'Paleti gresie', 'greutate' => 10, 'valoare' => 60],   // 6 lei / kg
        ['nume' => 'Saci ciment', 'greutate' => 20, 'valoare' => 100],    // 5 lei / kg
        ['nume' => 'Cutii faianta', 'greutate' => 30, 'valoare' => 120],  // 4 lei / kg
    ];

    #[TestDox('metoda lacoma ia intai ce aduce mai multi lei pe kg: gresie + ciment = 160 lei')]
    public function testLacom(): void
    {
        self::assertSame(
            ['valoare' => 160, 'greutate' => 30, 'alese' => ['Paleti gresie', 'Saci ciment']],
            (new TruckLoader())->lacom(self::EXAMPLE, 50),
        );
    }

    #[TestDox('metoda lacoma nu e mereu cea mai buna: ciment + faianta = 220 lei')]
    public function testLacomIsNotAlwaysBest(): void
    {
        $best = (new TruckLoader())->toateCombinatiile(self::EXAMPLE, 50);
        self::assertSame(220, $best['valoare']);
        self::assertSame(['Saci ciment', 'Cutii faianta'], $best['alese']);
    }

    #[TestDox('metoda lacoma nu depaseste niciodata capacitatea camionului')]
    public function testLacomRespectsCapacity(): void
    {
        mt_srand(11);
        $items = [];
        for ($i = 0; $i < 30; ++$i) {
            $items[] = ['nume' => "CMD-$i", 'greutate' => mt_rand(5, 60), 'valoare' => mt_rand(100, 5000)];
        }

        $result = (new TruckLoader())->lacom($items, 200);
        self::assertNotEmpty($result['alese'], 'metoda lacoma nu a ales nicio comanda');
        self::assertLessThanOrEqual(200, $result['greutate']);

        $chosen = array_filter($items, fn (array $item) => \in_array($item['nume'], $result['alese'], true));
        self::assertSame($result['greutate'], array_sum(array_column($chosen, 'greutate')), 'greutatea nu corespunde comenzilor alese');
        self::assertSame($result['valoare'], array_sum(array_column($chosen, 'valoare')), 'valoarea nu corespunde comenzilor alese');
    }

    #[TestDox('daca nu incape nimic, camionul pleaca gol')]
    public function testNothingFits(): void
    {
        self::assertSame(['valoare' => 0, 'greutate' => 0, 'alese' => []], (new TruckLoader())->lacom(self::EXAMPLE, 5));
    }
}
