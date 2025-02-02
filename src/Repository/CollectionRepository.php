<?php

namespace App\Repository;

use App\Entity\Collection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Collection|null find($id, $lockMode = null, $lockVersion = null)
 * @method Collection|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collection[]    findAll()
 * @method Collection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Collection::class);
    }

    /**
     * Get a array with all collections
     * 
     * @return array
     */
    public function findAllCollections()
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Return collections array.
     * 
     * @param int $id
     * 
     * @return array<Collection>
     */
    public function findCollectionById($id)
    {
        return $this->createQueryBuilder('c')
            ->select('c, p, u')
            ->leftJoin('c.photos', 'p')
            ->leftJoin('p.user', 'u')
            ->where('c.id = :collection_id')
            ->setParameter('collection_id', $id)
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Search collection by arg
     * 
     * @param string $args
     * 
     * @return array<Collection>
     */
    public function searchCollections($args)
    {
        return $this->createQueryBuilder('c')
            ->where('c.title LIKE :searchTitle')
            ->orWhere('c.description LIKE :searchDescription')
            ->setParameter('searchTitle', '%' . $args . '%')
            ->setParameter('searchDescription', '%' . $args . '%')
            ->getQuery()
            ->getArrayResult();
    }

    // /**
    //  * @return Collection[] Returns an array of Collection objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Collection
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
