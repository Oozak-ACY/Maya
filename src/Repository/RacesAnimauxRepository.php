<?php

namespace App\Repository;

use App\Entity\RacesAnimaux;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RacesAnimaux>
 *
 * @method RacesAnimaux|null find($id, $lockMode = null, $lockVersion = null)
 * @method RacesAnimaux|null findOneBy(array $criteria, array $orderBy = null)
 * @method RacesAnimaux[]    findAll()
 * @method RacesAnimaux[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RacesAnimauxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RacesAnimaux::class);
    }

    public function save(RacesAnimaux $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RacesAnimaux $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return RacesAnimaux[] Returns an array of RacesAnimaux objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RacesAnimaux
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findStatRace(){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT r.id, r.libelle, COUNT(a.id) AS nbAnimaux
            FROM App\Entity\RacesAnimaux r
            JOIN App\Entity\Animaux a
            GROUP BY r.id
            ORDER BY r.libelle ASC'
        );
        return $query->getResult();
    }
}
