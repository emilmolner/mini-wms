<?php

namespace App\Algorithm\Search;

/**
 * EXERCITIUL 1: cautarea binara (cautarea "prin injumatatire").
 *
 * Jocul "ghiceste numarul": ma gandesc la un numar intre 1 si 100, iar tu primesti doar raspunsul
 * "mai mare" sau "mai mic". Cel mai bine incepi cu 50: oricare ar fi raspunsul, scapi de jumatate
 * din numere. Apoi 25 sau 75, si tot asa. In cel mult 7 incercari ai ghicit.
 *
 * Aici facem la fel, dar cu o lista de coduri SORTATA:
 *   - ne uitam la codul din mijloc;
 *   - daca e cel cautat, gata;
 *   - daca e mai mic decat cel cautat, cautam mai departe doar in jumatatea din dreapta;
 *   - daca e mai mare, cautam doar in jumatatea din stanga.
 *
 * 1.000.000 de coduri => cel mult 20 de pasi (fata de 1.000.000 la cautarea pe rand).
 */
final class BinarySearch
{
    /** de cate ori am comparat un cod din lista cu codul cautat */
    public int $comparisons = 0;

    /**
     * @param list<string> $sortedCodes lista de coduri, sortata crescator
     *
     * @return int pozitia codului cautat sau -1 daca nu exista
     */
    public function find(array $sortedCodes, string $target): int
    {
        $this->comparisons = 0;

        // TODO EXERCITIUL 1
        //  $left  = 0;                          // inceputul zonei in care mai cautam
        //  $right = count($sortedCodes) - 1;    // sfarsitul zonei
        //
        //  cat timp ($left <= $right):
        //      $middle = intdiv($left + $right, 2);     // pozitia din mijloc
        //      ++$this->comparisons;
        //      daca $sortedCodes[$middle] === $target   => return $middle;
        //      daca $sortedCodes[$middle] < $target     => $left = $middle + 1;   (mergem la dreapta)
        //      altfel                                   => $right = $middle - 1;  (mergem la stanga)
        //
        //  return -1;   // zona a ramas goala: codul nu exista
        return -1;
    }
}
