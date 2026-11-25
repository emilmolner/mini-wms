<?php

namespace App\Algorithm\Hash;

/**
 * EXERCITIUL 2: un client trimite o lista de coduri. Care dintre ele NU exista in catalog?
 *
 * In engleza: Hash Table / Hash Map (in PHP: associative array), "lookup" in O(1).
 * Varianta naiva: Nested Loops, O(n * m). Varianta rapida: O(n + m).
 *
 * Varianta "naiva": pentru fiecare cod cerut, ne uitam prin TOT catalogul. Ca si cum ai cauta
 * cheia potrivita incercand-o pe fiecare din legatura de chei. 200 de coduri x 200.000 de produse
 * inseamna zeci de milioane de comparatii.
 *
 * Varianta rapida: punem intai catalogul intr-un "dulap cu sertare etichetate" (un array cu chei).
 * Dupa aceea, ca sa vedem daca un cod exista, deschidem direct sertarul cu eticheta lui:
 *
 *   $index = ['PROD-0000001' => true, 'PROD-0000002' => true];
 *   isset($index['PROD-0000001']);   // true  - instant, fara sa ne uitam prin tot dulapul
 *   isset($index['PROD-9999999']);   // false
 *
 * In PHP, orice array cu chei functioneaza asa. In carti se numeste "tabela de dispersie" (hash table).
 */
final class MissingCodeFinder
{
    /** cati pasi a facut ultima metoda apelata */
    public int $operations = 0;

    /**
     * EXEMPLU GATA FACUT: doua bucle, una in alta.
     *
     * @param list<string> $requested codurile cerute de client
     * @param list<string> $catalog   toate codurile din catalog
     *
     * @return list<string> codurile cerute care nu exista, in ordinea in care au fost cerute
     */
    public function findMissingNaive(array $requested, array $catalog): array
    {
        $this->operations = 0;
        $missing = [];

        foreach ($requested as $code) {
            $found = false;
            foreach ($catalog as $existing) {
                ++$this->operations;
                if ($existing === $code) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $missing[] = $code;
            }
        }

        return $missing;
    }

    /**
     * @param list<string> $requested
     * @param list<string> $catalog
     *
     * @return list<string>
     */
    public function findMissingFast(array $requested, array $catalog): array
    {
        $this->operations = 0;

        // TODO EXERCITIUL 2
        //  Pasul 1 - "etichetam sertarele":
        //      $index = [];
        //      pentru fiecare $code din $catalog:  $index[$code] = true;  ++$this->operations;
        //
        //  Pasul 2 - verificam codurile cerute:
        //      $missing = [];
        //      pentru fiecare $code din $requested:
        //          ++$this->operations;
        //          daca NU isset($index[$code])  =>  $missing[] = $code;
        //
        //  return $missing;
        return [];
    }
}
