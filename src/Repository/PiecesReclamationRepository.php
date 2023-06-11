<?php

namespace App\Repository;

use App\Entity\PiecesReclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PiecesReclamation>
 *
 * @method PiecesReclamation|null find($id, $lockMode = null, $lockVersion = null)
 * @method PiecesReclamation|null findOneBy(array $criteria, array $orderBy = null)
 * @method PiecesReclamation[]    findAll()
 * @method PiecesReclamation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PiecesReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PiecesReclamation::class);
    }

    public function save(PiecesReclamation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PiecesReclamation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PiecesReclamation[] Returns an array of PiecesReclamation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PiecesReclamation
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
