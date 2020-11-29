<?php

namespace App\Repository;

use App\Controller\Classe\Search;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Requête pour récupérer les produits en fonction de la recherche des utilisateurs
     * @return Product[]
     */
    public function findWithSearch(Search $search)
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('c', 'p')
            ->join('p.category', 'c');

        if (!empty($search->categories)) {
            $query = $query
                ->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $search->categories);
        }

        if (!empty($search->string)) {
            $query = $query
                ->andWhere('p.name LIKE :string')
                // "%{}%" => permet d'effectuer une recherche partielle
                ->setParameter('string', "%{$search->string}%");
        }

        /**
         * getQuery() : exécution et création de la requête
         * getResult() : retourne le résultat de la requête
         */
        return $query->getQuery()->getResult();

    }

}
