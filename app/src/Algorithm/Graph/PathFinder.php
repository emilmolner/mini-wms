<?php

namespace App\Algorithm\Graph;

use App\Warehouse\WarehouseMap;

/**
 * EXERCITIUL 7: drumul cel mai scurt prin depozit.
 *
 * In engleza: Shortest Path in a Grid / Graph, cu Breadth-First Search (BFS). Structura: Queue (FIFO).
 * Complexitate: O(V + E) - V = noduri (celule), E = muchii (legaturi intre celule vecine).
 *
 * Arunci o piatra in apa: valurile pornesc din acel punct si se departeaza in cercuri.
 * Primul val atinge locurile aflate la 1 pas, al doilea val pe cele aflate la 2 pasi, si tot asa.
 * Primul val care atinge destinatia ne spune drumul cel mai scurt.
 *
 * Asa lucreaza "parcurgerea in latime" (in engleza: Breadth-First Search, prescurtat BFS):
 *   - tinem o COADA de celule de vizitat (ca la magazin: cine a venit primul, iese primul);
 *   - scoatem o celula din coada si punem in coada vecinii ei nevizitati inca;
 *   - pentru fiecare celula noua tinem minte "de unde am venit", ca la final sa refacem drumul.
 */
final class PathFinder
{
    /** cate celule am scos din coada la ultima cautare */
    public int $visited = 0;

    /**
     * @param array{0: int, 1: int} $from [rand, coloana]
     * @param array{0: int, 1: int} $to   [rand, coloana]
     *
     * @return list<array{0: int, 1: int}>|null drumul (inclusiv $from si $to) sau null daca nu exista
     */
    public function shortestPath(WarehouseMap $map, array $from, array $to): ?array
    {
        $this->visited = 0;

        // TODO EXERCITIUL 7
        //  $queue = new \SplQueue();                   // coada: enqueue() pune la final, dequeue() scoate primul
        //  $queue->enqueue($from);
        //  $cameFrom = [self::key($from) => null];      // celulele descoperite si de unde am ajuns in ele
        //
        //  cat timp coada nu e goala (!$queue->isEmpty()):
        //      $cell = $queue->dequeue();
        //      ++$this->visited;
        //      daca $cell === $to  =>  return $this->buildPath($cameFrom, $to);    // am ajuns!
        //
        //      pentru fiecare $next din $map->neighbors($cell[0], $cell[1]):
        //          daca NU array_key_exists(self::key($next), $cameFrom):           // celula noua
        //              $cameFrom[self::key($next)] = $cell;
        //              $queue->enqueue($next);
        //
        //  return null;    // coada s-a golit si nu am ajuns: nu exista drum
        return null;
    }

    /**
     * GATA FACUT: refacem drumul de la destinatie inapoi spre start, din "de unde am venit" in "de unde am venit".
     *
     * @param array<string, array{0: int, 1: int}|null> $cameFrom
     * @param array{0: int, 1: int}                     $to
     *
     * @return list<array{0: int, 1: int}>
     */
    private function buildPath(array $cameFrom, array $to): array
    {
        $path = [];
        for ($cell = $to; null !== $cell; $cell = $cameFrom[self::key($cell)]) {
            $path[] = $cell;
        }

        return array_reverse($path);
    }

    /** [3, 4] => "3,4" (folosit ca cheie in array) */
    private static function key(array $cell): string
    {
        return $cell[0].','.$cell[1];
    }
}
