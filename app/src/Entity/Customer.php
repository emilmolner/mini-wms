<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/** Un client al depozitului, de exemplu "Bricolaj Bacau SRL". Comenzile il gasesc prin customer_id. */
#[ORM\Entity]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    private string $name;

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
}
