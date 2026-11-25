<?php

namespace App\Algorithm\Graph;

/**
 * EXERCITIUL 8: in ce ordine luam produsele unei comenzi de pe rafturi?
 *
 * Plecam de la rampa (START), trecem pe la toate locatiile comenzii si ne intoarcem la rampa.
 * Primim tabelul distantelor: $dist[$i][$j] = cati pasi sunt de la locatia $i la locatia $j.
 * Locatia 0 este START. O ruta arata asa: [0, 2, 1, 3, 0].
 *
 * Doua metode:
 *   - "cel mai apropiat vecin" (o metoda lacoma): din locul in care esti, mergi mereu la cel mai
 *     apropiat raft la care nu ai fost. Rapida si de obicei buna, dar nu mereu cea mai buna;
 *   - "toate variantele": incercam toate ordinile posibile. Gaseste mereu ruta cea mai scurta,
 *     dar pentru 6 rafturi sunt 720 de ordini, iar pentru 15 rafturi peste un milion de milioane.
 */
final class PickingRoute
{
    /** cate rute complete a verificat ultima apelare a lui allRoutes() */
    public int $routesChecked = 0;

    /**
     * EXERCITIUL 8 - cel mai apropiat vecin.
     *
     * @param list<list<int>> $dist
     *
     * @return list<int>
     */
    public function nearestNeighbor(array $dist): array
    {
        $n = \count($dist);

        // TODO EXERCITIUL 8
        //  $route = [0];        // pornim de la START
        //  $current = 0;        // unde suntem acum
        //  $notVisited = [];    // locatiile 1, 2, ..., n-1:  for ($i = 1; $i < $n; ++$i) { $notVisited[$i] = true; }
        //
        //  cat timp mai sunt locatii nevizitate ($notVisited nu e gol):
        //      $next = null;
        //      pentru fiecare $candidate din array_keys($notVisited):
        //          daca $next === null SAU $dist[$current][$candidate] < $dist[$current][$next]:
        //              $next = $candidate;
        //      $route[] = $next;   unset($notVisited[$next]);   $current = $next;
        //
        //  $route[] = 0;        // ne intoarcem la START
        //  return $route;
        return array_merge([0], range(1, $n - 1), [0]);
    }

    /**
     * EXEMPLU GATA FACUT - toate variantele.
     * Construim ruta pas cu pas: incercam fiecare locatie nevizitata, mergem mai departe,
     * apoi o scoatem si incercam urmatoarea (recursivitate).
     *
     * @param list<list<int>> $dist
     *
     * @return list<int>
     */
    public function allRoutes(array $dist): array
    {
        if (\count($dist) > 10) {
            throw new \InvalidArgumentException('Prea multe locatii pentru "toate variantele" (maximum 9 + START).');
        }

        $this->routesChecked = 0;
        $notVisited = range(1, \count($dist) - 1);
        $best = ['length' => PHP_INT_MAX, 'route' => []];
        $this->tryRoutes($dist, [0], $notVisited, $best);

        return $best['route'];
    }

    /**
     * @param list<list<int>>                      $dist
     * @param list<int>                            $route      ruta construita pana acum
     * @param list<int>                            $notVisited locatiile prin care nu am trecut inca
     * @param array{length: int, route: list<int>} $best       cea mai buna ruta gasita
     */
    private function tryRoutes(array $dist, array $route, array $notVisited, array &$best): void
    {
        if ([] === $notVisited) {
            $complete = [...$route, 0];
            ++$this->routesChecked;
            if (self::length($dist, $complete) < $best['length']) {
                $best = ['length' => self::length($dist, $complete), 'route' => $complete];
            }

            return;
        }

        foreach ($notVisited as $k => $next) {
            $rest = $notVisited;
            unset($rest[$k]);
            $this->tryRoutes($dist, [...$route, $next], array_values($rest), $best);
        }
    }

    /**
     * Lungimea unei rute = suma distantelor dintre locatiile consecutive.
     *
     * @param list<list<int>> $dist
     * @param list<int>       $route
     */
    public static function length(array $dist, array $route): int
    {
        $total = 0;
        for ($i = 1; $i < \count($route); ++$i) {
            $total += $dist[$route[$i - 1]][$route[$i]];
        }

        return $total;
    }
}
