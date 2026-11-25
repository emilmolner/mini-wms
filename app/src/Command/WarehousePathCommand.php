<?php

namespace App\Command;

use App\Algorithm\Graph\PathFinder;
use App\Warehouse\WarehouseMap;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Style\SymfonyStyle;

/** Exercitiul 7: deseneaza in terminal drumul cel mai scurt pana la o locatie. */
#[AsCommand(name: 'app:depozit:drum', description: 'Afiseaza harta depozitului si drumul pana la o locatie (ex: W08)')]
final class WarehousePathCommand
{
    public function __construct(private readonly PathFinder $pathFinder)
    {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(description: 'Locatia unde vrem sa ajungem, de exemplu W08', name: 'spre')] string $to,
        #[Argument(description: 'Locatia de plecare', name: 'de_la')] string $from = 'START',
    ): int {
        $map = new WarehouseMap();
        $toPosition = $map->positionOf($to);
        $path = $this->pathFinder->shortestPath($map, $map->positionOf($from), $toPosition);

        if (null === $path) {
            $io->error('Nu am gasit niciun drum. Ai rezolvat Exercitiul 7 (PathFinder.php)?');
            $io->writeln($map->render([], [$toPosition]));

            return 1;
        }

        $io->writeln($map->render($path, [$toPosition]));
        $io->newLine();
        $io->writeln(\sprintf('Pasi: <info>%d</info>   Celule verificate: <info>%d</info>', \count($path) - 1, $this->pathFinder->visited));

        return 0;
    }
}
