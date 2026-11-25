<?php

namespace App\Warehouse;

/**
 * Harta depozitului, ca o matrice de caractere (un "grid").
 *
 *   #  perete      =  raft (nu se poate trece prin el)
 *   .  culoar      S  punctul de start (rampa de incarcare)
 *   P  un palet uitat pe culoar (nu se poate trece)
 *
 * O celula are coordonatele [rand, coloana], numarate de la 0 din coltul stanga-sus.
 * Codul unei celule este litera coloanei + randul pe doua cifre: [3, 4] => "E03".
 * Locatiile de picking sunt celulele de culoar aflate langa un raft.
 */
final class WarehouseMap
{
    public const LAYOUT = [
        '#########################',
        '#S......................#',
        '#.==.==.==.==.==.==.==..#',
        '#.==.==.==.==.==.==.==..#',
        '#.==.==.==.==.==.==.==..#',
        '#.......P...............#',
        '#.==.==.==.==.==.==.==..#',
        '#.==.==.==.==.==.==.==..#',
        '#.==.==.==.==.==.==.==..#',
        '#.......................#',
        '#########################',
    ];

    /** @var list<string> */
    private array $grid;

    /** @param list<string>|null $layout o alta harta (folosita in teste) */
    public function __construct(?array $layout = null)
    {
        $this->grid = $layout ?? self::LAYOUT;
    }

    public function rows(): int
    {
        return \count($this->grid);
    }

    public function cols(): int
    {
        return \strlen($this->grid[0]);
    }

    public function cell(int $row, int $col): string
    {
        return $this->grid[$row][$col] ?? '#';
    }

    public function isWalkable(int $row, int $col): bool
    {
        return \in_array($this->cell($row, $col), ['.', 'S'], true);
    }

    /** @return array{0: int, 1: int} pozitia lui S */
    public function start(): array
    {
        foreach ($this->grid as $row => $line) {
            $col = strpos($line, 'S');
            if (false !== $col) {
                return [$row, $col];
            }
        }

        throw new \LogicException('Harta nu are punct de start S.');
    }

    /**
     * Vecinii in care se poate merge: sus, jos, stanga, dreapta (nu pe diagonala).
     *
     * @return list<array{0: int, 1: int}>
     */
    public function neighbors(int $row, int $col): array
    {
        $result = [];
        foreach ([[-1, 0], [1, 0], [0, -1], [0, 1]] as [$dr, $dc]) {
            if ($this->isWalkable($row + $dr, $col + $dc)) {
                $result[] = [$row + $dr, $col + $dc];
            }
        }

        return $result;
    }

    public static function code(int $row, int $col): string
    {
        return \chr(\ord('A') + $col).\sprintf('%02d', $row);
    }

    /**
     * "E03" => [3, 4]; "START" sau "S" => pozitia de start.
     *
     * @return array{0: int, 1: int}
     */
    public function positionOf(string $code): array
    {
        $code = strtoupper(trim($code));
        if ('START' === $code || 'S' === $code) {
            return $this->start();
        }
        if (!preg_match('/^([A-Z])(\d{1,2})$/', $code, $m)) {
            throw new \InvalidArgumentException("Cod de locatie invalid: \"$code\" (exemplu corect: E03).");
        }
        $position = [(int) $m[2], \ord($m[1]) - \ord('A')];
        if (!$this->isWalkable(...$position)) {
            throw new \InvalidArgumentException("Locatia $code nu este pe un culoar.");
        }

        return $position;
    }

    /**
     * Toate locatiile de picking: celule de culoar care au un raft in stanga sau in dreapta.
     *
     * @return list<array{code: string, row: int, col: int}>
     */
    public function pickLocations(): array
    {
        $locations = [];
        for ($row = 0; $row < $this->rows(); ++$row) {
            for ($col = 0; $col < $this->cols(); ++$col) {
                $nextToShelf = '=' === $this->cell($row, $col - 1) || '=' === $this->cell($row, $col + 1);
                if ('.' === $this->cell($row, $col) && $nextToShelf) {
                    $locations[] = ['code' => self::code($row, $col), 'row' => $row, 'col' => $col];
                }
            }
        }

        return $locations;
    }

    /**
     * Harta ca text, cu drumul desenat cu "*" si tintele marcate cu "o".
     *
     * @param list<array{0: int, 1: int}> $path
     * @param list<array{0: int, 1: int}> $targets
     *
     * @return list<string>
     */
    public function render(array $path = [], array $targets = []): array
    {
        $grid = $this->grid;
        foreach ($path as [$row, $col]) {
            if ('S' !== $grid[$row][$col]) {
                $grid[$row][$col] = '*';
            }
        }
        foreach ($targets as [$row, $col]) {
            $grid[$row][$col] = 'o';
        }

        // antetul cu literele coloanelor, ca sa se poata citi codurile locatiilor
        $header = '    '.implode('', array_map(fn (int $c) => \chr(\ord('A') + $c), range(0, $this->cols() - 1)));
        $lines = [$header];
        foreach ($grid as $row => $line) {
            $lines[] = \sprintf('%02d  %s', $row, $line);
        }

        return $lines;
    }
}
