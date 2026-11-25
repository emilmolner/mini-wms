<?php

namespace App\Algorithm\Sort;

/**
 * EXERCITIUL 3: in ce ordine pregatim comenzile?
 *
 * Ca la camera de garda: intai urgentele, iar intre doi pacienti la fel de urgenti,
 * intra cel care a venit primul. Regula din depozit:
 *   1. prioritatea mai mica e mai importanta: 1 (urgent), apoi 2 (normal), apoi 3 (poate astepta);
 *   2. la aceeasi prioritate, comanda mai VECHE are intaietate;
 *   3. daca si data e aceeasi, decide numarul comenzii (CMD-00001 inaintea lui CMD-00002).
 *
 * Nu scriem noi algoritmul de sortare: PHP are deja usort(). Noi ii spunem doar
 * CARE din doua comenzi trebuie sa fie prima. Functia asta se numeste "comparator" si intoarce:
 *   un numar negativ  => $a vine inaintea lui $b
 *   0                 => sunt la egalitate
 *   un numar pozitiv  => $a vine dupa $b
 *
 * Operatorul <=> ("nava spatiala") face exact asta:  1 <=> 2 este -1,  2 <=> 2 este 0,  3 <=> 2 este 1.
 */
final class OrderComparator
{
    /**
     * Fiecare comanda arata asa:
     *   ['numar' => 'CMD-00017', 'client' => 'Bricolaj Bacau SRL', 'prioritate' => 1, 'dataCreare' => '2026-11-20 09:15:00']
     *
     * @param array{numar: string, prioritate: int, dataCreare: string} $a
     * @param array{numar: string, prioritate: int, dataCreare: string} $b
     */
    public function compare(array $a, array $b): int
    {
        // TODO EXERCITIUL 3
        //  daca $a['prioritate'] !== $b['prioritate']  =>  return $a['prioritate'] <=> $b['prioritate'];
        //  daca $a['dataCreare'] !== $b['dataCreare']  =>  return $a['dataCreare'] <=> $b['dataCreare'];
        //  return $a['numar'] <=> $b['numar'];
        //
        //  (datele sunt texte de forma "2026-11-20 09:15:00", asa ca se pot compara direct)
        return 0;
    }
}
