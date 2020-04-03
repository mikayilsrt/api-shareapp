<?php

namespace App\Repository;

use App\Entity\Photo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Photo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Photo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Photo[]    findAll()
 * @method Photo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Photo::class);
    }

    /**
     * Get all photos posted.
     * 
     * @return Array
     */
    public function findAllPhotos()
    {
        return $this->createQueryBuilder('p')
            ->select('p, u, c')
            ->leftJoin('p.collection', 'c')
            ->leftJoin('p.user', 'u')
            ->getQuery()
            ->getArrayResult();
    }

    public function findById($id)
    {
        return $this->createQueryBuilder('p')
            ->select('p, u, c')
            ->leftJoin('p.user', 'u')
            ->leftJoin('p.collection', 'c')
            ->where('p.id = :photo_id')
            ->setParameter('photo_id', $id)
            ->getQuery()
            ->getArrayResult();
    }

    // /**
    //  * @return Photo[] Returns an array of Photo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Photo
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
