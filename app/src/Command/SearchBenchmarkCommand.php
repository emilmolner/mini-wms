<?php

namespace App\Command;

use App\Algorithm\Search\BinarySearch;
use App\Algorithm\Search\LinearSearch;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * EXERCITIUL 1 - verificarea experimentala a complexitatii:
 * cautam un cod care NU exista (cazul cel mai nefavorabil) in liste din ce in ce mai mari.
 */
#[AsCommand(name: 'app:masurare:cautare', description: 'Compara cautarea secventiala cu cea binara pe liste tot mai mari')]
final class SearchBenchmarkCommand
{
    public function __invoke(SymfonyStyle $io): int
    {
        $linear = new LinearSearch();
        $binary = new BinarySearch();
        $rows = [];

        foreach ([1_000, 10_000, 100_000, 1_000_000] as $n) {
            // lista sortata: PROD-0000000, PROD-0000002, PROD-0000004, ... (doar numere pare)
            $codes = [];
            for ($i = 0; $i < $n; ++$i) {
                $codes[] = \sprintf('PROD-%07d', 2 * $i);
            }
            $missing = 'PROD-9999999'; // impar => nu exista in lista

            $t = hrtime(true);
            $linear->find($codes, $missing);
            $linearMs = (hrtime(true) - $t) / 1e6;

            $t = hrtime(true);
            $binary->find($codes, $missing);
            $binaryMs = (hrtime(true) - $t) / 1e6;

            $rows[] = [
                number_format($n, 0, ',', '.'),
                number_format($linear->comparisons, 0, ',', '.'),
                \sprintf('%.3f ms', $linearMs),
                $binary->comparisons,
                \sprintf('%.3f ms', $binaryMs),
                \sprintf('%.1f', log($n, 2)),
            ];
        }

        $io->title('Cautarea unui cod inexistent (cazul cel mai nefavorabil)');
        $io->table(['n', 'comparatii secvential', 'timp secvential', 'comparatii binar', 'timp binar', 'log2(n)'], $rows);

        if (0 === $binary->comparisons) {
            $io->warning('Cautarea binara face 0 comparatii: Exercitiul 1 (BinarySearch.php) nu este inca rezolvat.');
        }

        return 0;
    }
}
