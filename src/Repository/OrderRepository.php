<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findStripeSessionId()
    {
        $query = $this
            ->createQueryBuilder('o')
            ->select('o.stripeSessionId');

        return $query->getQuery()->getResult();
    }

    /**
     * Affichage des commandes dans l'espace membre de l'utilisateur
     */
    public function findSuccessOrders($user)
    {
        return $this
            ->createQueryBuilder('o')
            // Uniquement les commandes payées
            ->andWhere('o.state > 0')
            // Uniquement à l'utilisateur associé
            ->andWhere('o.user = :user')
            ->setParameter('user', $user)
            ->orderBy('o.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
