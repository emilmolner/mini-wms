<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * O comanda de client. Tabela se numeste "orders" pentru ca ORDER este cuvant rezervat in SQL.
 * priority: 1 = urgenta, 2 = normala, 3 = poate astepta.
 */
#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'orders')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20, unique: true)]
    private string $number;

    #[ORM\Column(length: 80)]
    private string $customer;

    #[ORM\Column]
    private int $priority;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    /** @var Collection<int, OrderLine> */
    #[ORM\OneToMany(targetEntity: OrderLine::class, mappedBy: 'order')]
    private Collection $lines;

    public function __construct()
    {
        $this->lines = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getNumber(): string { return $this->number; }
    public function getCustomer(): string { return $this->customer; }
    public function getPriority(): int { return $this->priority; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    /** @return Collection<int, OrderLine> */
    public function getLines(): Collection { return $this->lines; }
}
