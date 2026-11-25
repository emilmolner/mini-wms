<?php

namespace App\Algorithm\Packing;

/**
 * EXERCITIUL 5: in cate cutii impachetam produsele unei comenzi?
 *
 * In engleza: Bin Packing Problem. Metoda: Greedy Algorithm.
 *   "cutia curenta"              = Next Fit
 *   "prima cutie in care incape" = First Fit
 *   "intai cele grele"           = First Fit Decreasing (FFD)
 *
 * O cutie duce cel mult $capacity kg. Vrem cat mai PUTINE cutii.
 * Sa gasim mereu cea mai buna impachetare e foarte greu, asa ca folosim reguli simple,
 * numite "metode lacome": la fiecare pas facem alegerea care pare cea mai buna ACUM
 * si nu ne mai intoarcem asupra ei. (In carti, metoda lacoma apare cu numele englezesc "greedy".)
 *
 * Rezultatul este lista cutiilor; fiecare cutie este lista greutatilor puse in ea:
 *   [[25, 5], [14]]  =>  doua cutii: prima cu 25 kg + 5 kg, a doua cu 14 kg
 */
final class BoxPacker
{
    /**
     * EXEMPLU GATA FACUT - regula "cutia curenta":
     * avem o singura cutie deschisa; daca bucata nu mai incape, o inchidem si deschidem alta.
     *
     * @param list<int> $weights
     *
     * @return list<list<int>>
     */
    public function nextFit(array $weights, int $capacity): array
    {
        $this->checkWeights($weights, $capacity);
        $boxes = [];
        $current = [];

        foreach ($weights as $weight) {
            if (array_sum($current) + $weight > $capacity) {
                $boxes[] = $current;
                $current = [];
            }
            $current[] = $weight;
        }
        if ([] !== $current) {
            $boxes[] = $current;
        }

        return $boxes;
    }

    /**
     * EXERCITIUL 5a - regula "prima cutie in care incape":
     * toate cutiile raman deschise; fiecare bucata merge in PRIMA cutie in care mai are loc.
     * Daca nu incape in niciuna, deschidem o cutie noua.
     *
     * @param list<int> $weights
     *
     * @return list<list<int>>
     */
    public function firstFit(array $weights, int $capacity): array
    {
        $this->checkWeights($weights, $capacity);

        // TODO EXERCITIUL 5a
        //  $boxes = [];
        //  pentru fiecare $weight din $weights:
        //      $placed = false;
        //      pentru fiecare cutie ($k => $box) din $boxes:
        //          daca array_sum($box) + $weight <= $capacity:
        //              $boxes[$k][] = $weight;  $placed = true;  break;
        //      daca !$placed  =>  $boxes[] = [$weight];      // cutie noua
        //  return $boxes;
        return [];
    }

    /**
     * EXERCITIUL 5b - regula "intai cele grele":
     * ca la facut bagajul: intai lucrurile mari, apoi strecori lucrurile mici in golurile ramase.
     * Sortam bucatile de la cea mai grea la cea mai usoara si apoi aplicam "prima cutie in care incape".
     *
     * @param list<int> $weights
     *
     * @return list<list<int>>
     */
    public function firstFitDecreasing(array $weights, int $capacity): array
    {
        // TODO EXERCITIUL 5b: sorteaza $weights descrescator (functia rsort), apoi return $this->firstFit(...)
        return $this->firstFit($weights, $capacity);
    }

    /** @param list<int> $weights */
    private function checkWeights(array $weights, int $capacity): void
    {
        foreach ($weights as $weight) {
            if ($weight > $capacity) {
                throw new \InvalidArgumentException("O bucata de $weight kg nu incape intr-o cutie de $capacity kg.");
            }
        }
    }
}
