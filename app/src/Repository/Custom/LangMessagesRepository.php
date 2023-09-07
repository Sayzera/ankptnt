<?php

namespace App\Repository\Custom;

use App\Entity\Custom\LangMessages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LangMessages>
 *
 * @method LangMessages|null find($id, $lockMode = null, $lockVersion = null)
 * @method LangMessages|null findOneBy(array $criteria, array $orderBy = null)
 * @method LangMessages[]    findAll()
 * @method LangMessages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LangMessagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LangMessages::class);
    }

//    /**
//     * @return LangMessages[] Returns an array of LangMessages objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LangMessages
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
