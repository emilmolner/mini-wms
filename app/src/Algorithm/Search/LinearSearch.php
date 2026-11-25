<?php

namespace App\Algorithm\Search;

/**
 * EXEMPLU GATA FACUT: cautarea "pe rand" (secventiala).
 *
 * In engleza: Linear Search / Sequential Search. Complexitate: O(n).
 *
 * Ca atunci cand cauti o carte pe un raft: te uiti la prima, apoi la a doua, apoi la a treia...
 * pana o gasesti. Daca raftul are 1.000.000 de carti, poti ajunge sa te uiti la toate.
 */
final class LinearSearch
{
    /** de cate ori am comparat un cod din lista cu codul cautat */
    public int $comparisons = 0;

    /**
     * @param list<string> $codes lista de coduri (nu conteaza daca e sortata)
     *
     * @return int pozitia codului cautat sau -1 daca nu exista
     */
    public function find(array $codes, string $target): int
    {
        $this->comparisons = 0;

        for ($i = 0; $i < \count($codes); ++$i) {
            ++$this->comparisons;
            if ($codes[$i] === $target) {
                return $i;
            }
        }

        return -1;
    }
}
