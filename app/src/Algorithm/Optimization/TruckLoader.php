<?php

namespace App\Algorithm\Optimization;

/**
 * EXERCITIUL 6: ce comenzi incarcam azi in camion?
 *
 * Camionul duce cel mult $capacity kg. Fiecare comanda are o greutate (kg) si o valoare (lei).
 * O comanda se ia intreaga sau deloc. Vrem sa plece camionul cu marfa de valoare cat mai MARE.
 *
 * Doua metode:
 *   - metoda lacoma: luam intai comenzile care aduc cei mai multi lei pe kilogram. Rapida, dar
 *     uneori lasa camionul pe jumatate gol si pierde varianta cea mai buna;
 *   - toate combinatiile: pentru fiecare comanda incercam "o luam" si "nu o luam" si pastram cea
 *     mai buna varianta. Gaseste mereu cea mai buna incarcare, dar pentru n comenzi sunt 2^n variante:
 *     10 comenzi = 1.024 variante, 20 = 1.048.576, 40 = peste o mie de miliarde.
 *
 * Fiecare comanda arata asa: ['nume' => 'CMD-00017', 'greutate' => 120, 'valoare' => 3400]
 * Rezultatul:                ['valoare' => ..., 'greutate' => ..., 'alese' => ['CMD-00017', ...]]
 */
final class TruckLoader
{
    /** cate variante complete a verificat ultima apelare a lui toateCombinatiile() */
    public int $combinationsChecked = 0;

    /**
     * EXERCITIUL 6 - metoda lacoma.
     *
     * @param list<array{nume: string, greutate: int, valoare: int}> $items
     *
     * @return array{valoare: int, greutate: int, alese: list<string>}
     */
    public function lacom(array $items, int $capacity): array
    {
        // TODO EXERCITIUL 6
        //  1. sorteaza $items dupa "lei pe kilogram", de la cel mai mare la cel mai mic:
        //       usort($items, fn ($a, $b) => $b['valoare'] / $b['greutate'] <=> $a['valoare'] / $a['greutate']);
        //  2. $chosen = []; $weight = 0; $value = 0;
        //     pentru fiecare $item:
        //         daca $weight + $item['greutate'] <= $capacity:
        //             il adaugi: $chosen[] = $item['nume']; $weight += ...; $value += ...;
        //  3. return ['valoare' => $value, 'greutate' => $weight, 'alese' => $chosen];
        return ['valoare' => 0, 'greutate' => 0, 'alese' => []];
    }

    /**
     * EXEMPLU GATA FACUT - toate combinatiile.
     *
     * @param list<array{nume: string, greutate: int, valoare: int}> $items
     *
     * @return array{valoare: int, greutate: int, alese: list<string>}
     */
    public function toateCombinatiile(array $items, int $capacity): array
    {
        if (\count($items) > 20) {
            throw new \InvalidArgumentException('Prea multe comenzi pentru "toate combinatiile" (maximum 20).');
        }

        $this->combinationsChecked = 0;
        $best = ['valoare' => 0, 'greutate' => 0, 'alese' => []];
        $this->tryFrom($items, 0, [], 0, 0, $capacity, $best);

        return $best;
    }

    /**
     * Ne uitam la comanda cu numarul $i si incercam ambele variante: o luam / nu o luam.
     * Functia se apeleaza pe ea insasi pentru comanda urmatoare (recursivitate).
     *
     * @param list<array{nume: string, greutate: int, valoare: int}> $items
     * @param list<string>                                         $chosen ce am ales pana acum
     * @param array{valoare: int, greutate: int, alese: list<string>} $best  cea mai buna varianta gasita
     */
    private function tryFrom(array $items, int $i, array $chosen, int $weight, int $value, int $capacity, array &$best): void
    {
        if ($weight > $capacity) {
            return; // camionul e prea incarcat: varianta asta nu e buna
        }

        if ($i === \count($items)) {
            // am hotarat pentru toate comenzile: avem o varianta completa
            ++$this->combinationsChecked;
            if ($value > $best['valoare']) {
                $best = ['valoare' => $value, 'greutate' => $weight, 'alese' => $chosen];
            }

            return;
        }

        $item = $items[$i];

        // varianta 1: LUAM comanda $i
        $this->tryFrom($items, $i + 1, [...$chosen, $item['nume']], $weight + $item['greutate'], $value + $item['valoare'], $capacity, $best);

        // varianta 2: NU o luam
        $this->tryFrom($items, $i + 1, $chosen, $weight, $value, $capacity, $best);
    }
}
