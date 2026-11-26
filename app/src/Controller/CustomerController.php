<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/clienti')]
#[OA\Tag(name: 'Clienti')]
final class CustomerController extends AbstractController
{
    /** Lista clientilor, cu id-ul folosit la raport (Exercitiul 4b) si numarul de comenzi. */
    #[Route('', methods: ['GET'])]
    public function list(Connection $db): JsonResponse
    {
        $customers = $db->fetchAllAssociative(
            'SELECT c.id, c.name AS nume, COUNT(o.id) AS comenzi
             FROM customer c LEFT JOIN orders o ON o.customer_id = c.id
             GROUP BY c.id, c.name
             ORDER BY c.id',
        );

        return $this->json(array_map(fn (array $c) => ['id' => (int) $c['id'], 'nume' => $c['nume'], 'comenzi' => (int) $c['comenzi']], $customers));
    }
}
