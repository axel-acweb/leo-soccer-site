<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\GroupStageMatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupStageMatch>
 *
 * @method GroupStageMatch|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupStageMatch|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupStageMatch[]    findAll()
 * @method GroupStageMatch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupStageMatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupStageMatch::class);
    }

    public function save(GroupStageMatch $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GroupStageMatch $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return GroupStageMatch[] Returns an array of GroupStageMatch objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GroupStageMatch
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findByTeam(Book $team)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.team_1 = :val OR g.team_2 = :val')
            ->setParameter('val', $team)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
