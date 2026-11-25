<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * O locatie din depozit (o celula din harta), de exemplu "D03".
 * row / col sunt coordonatele in harta din App\Warehouse\WarehouseMap.
 */
#[ORM\Entity]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function __construct(
        #[ORM\Column(length: 10, unique: true)]
        private string $code,
        #[ORM\Column(name: 'map_row')]
        private int $row,
        #[ORM\Column(name: 'map_col')]
        private int $col,
    ) {
    }

    public function getId(): ?int { return $this->id; }
    public function getCode(): string { return $this->code; }
    public function getRow(): int { return $this->row; }
    public function getCol(): int { return $this->col; }
}
