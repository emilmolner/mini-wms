<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Product> */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Toate codurile de produs, sortate crescator (sortarea o face MySQL).
     *
     * @return list<string>
     */
    public function allCodesSorted(): array
    {
        return $this->getEntityManager()->getConnection()
            ->fetchFirstColumn('SELECT code FROM product ORDER BY code');
    }
}
