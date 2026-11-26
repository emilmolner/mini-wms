<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Order> */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * GATA FACUT - varianta naiva: aduce doar comenzile clientului (dupa customer_id).
     * Liniile si produsele lor se incarca abia cand le folosim, cate un query pentru fiecare.
     *
     * @return list<Order>
     */
    public function findByCustomerNaive(int $customerId): array
    {
        return $this->findBy(['customer' => $customerId], ['id' => 'ASC']);
    }

    /**
     * In engleza: "N+1 Query Problem", rezolvata cu Eager Loading (JOIN + addSelect / "fetch join").
     *
     * EXERCITIUL 4b: aceleasi comenzi, dar cu liniile si produsele aduse din ACELASI query (JOIN).
     *
     * @return list<Order>
     */
    public function findByCustomerWithJoin(int $customerId): array
    {
        // TODO EXERCITIUL 4b: adauga cele doua JOIN-uri (cu addSelect, ca Doctrine sa si incarce datele):
        //     ->leftJoin('o.lines', 'l')->addSelect('l')
        //     ->leftJoin('l.product', 'p')->addSelect('p')
        return $this->createQueryBuilder('o')
            ->leftJoin('o.lines', 'l')->addSelect('l')
            ->leftJoin('l.product', 'p')->addSelect('p')
            ->where('o.customer = :customer')
            ->setParameter('customer', $customerId)
            ->orderBy('o.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
