<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Un produs din catalog.
 * Atentie: coloana "code" NU are index la inceput. Il adaugam in Exercitiul 4a.
 * In engleza: Database Index (in MySQL, un B-Tree); fara index, MySQL face "Full Table Scan".
 */
#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Index(name: 'idx_product_code', columns: ['code'])]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function __construct(
        /** codul produsului, de exemplu PROD-0791900 */
        #[ORM\Column(length: 20)]
        private string $code,
        #[ORM\Column(length: 120)]
        private string $name,
        #[ORM\Column(length: 40)]
        private string $category,
        /** greutatea unei bucati, in kilograme (numar intreg, ca sa fie simplu) */
        #[ORM\Column]
        private int $weightKg,
        /** pretul unei bucati, in lei */
        #[ORM\Column]
        private int $price,
        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        private Location $location,
    ) {
    }

    public function getId(): ?int { return $this->id; }
    public function getCode(): string { return $this->code; }
    public function getName(): string { return $this->name; }
    public function getCategory(): string { return $this->category; }
    public function getWeightKg(): int { return $this->weightKg; }
    public function getPrice(): int { return $this->price; }
    public function getLocation(): Location { return $this->location; }
}
