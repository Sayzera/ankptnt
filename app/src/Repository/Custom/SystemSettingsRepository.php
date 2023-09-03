<?php

namespace App\Repository\Custom;

use App\Entity\Custom\SystemSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SystemSettings>
 *
 * @method SystemSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method SystemSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method SystemSettings[]    findAll()
 * @method SystemSettings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SystemSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SystemSettings::class);
    }


    public function deneme() {
        echo "deneme";
        exit;
    }

//    /**
//     * @return SystemSettings[] Returns an array of SystemSettings objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SystemSettings
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
