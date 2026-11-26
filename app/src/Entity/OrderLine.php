<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/** O linie de comanda: produsul si cantitatea. */
#[ORM\Entity]
class OrderLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'lines')]
        #[ORM\JoinColumn(nullable: false)]
        private Order $order,
        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        private Product $product,
        #[ORM\Column]
        private int $quantity,
    ) {
    }

    public function getId(): ?int { return $this->id; }
    public function getOrder(): Order { return $this->order; }
    public function getProduct(): Product { return $this->product; }
    public function getQuantity(): int { return $this->quantity; }
}
