<?php

namespace App\Repository;

use App\Entity\ObservationCacheMainList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ObservationCacheMainList>
 *
 * @method ObservationCacheMainList|null find($id, $lockMode = null, $lockVersion = null)
 * @method ObservationCacheMainList|null findOneBy(array $criteria, array $orderBy = null)
 * @method ObservationCacheMainList[]    findAll()
 * @method ObservationCacheMainList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ObservationCacheMainListRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private  ObservationCacheRepository $observationCacheRepository)
    {
        parent::__construct($registry, ObservationCacheMainList::class);
    }

    public function insertObservatinCacheMain($data, $observationList)
    {

        $_niceClasses = [];
        foreach ($data['NiceClasses'] as $niceClass) {
            $_niceClasses[] = $niceClass['No'];
        }

        $niceClasses = join(',',$_niceClasses);

        $observationCacheMainList = new ObservationCacheMainList();
        $observationCacheMainList->setSearchedWord($data['TrademarkName']);
        $observationCacheMainList->setNiceClasses($niceClasses);
        $observationCacheMainList->setBulletinNo($data['BulletinNo']);
        $this->getEntityManager()->persist($observationCacheMainList);
        $this->getEntityManager()->flush();

        if(!isset($observationList['trademarkSearchList'])) { return; }

        foreach ($observationList['trademarkSearchList'] as $observation) {
             /**
              * gözlem sonuçları daha önceden eklenmiş mi
              */
             $exists = $this->observationCacheRepository->existsObservation(
                $observation['searchedWord'],
                 $observation['niceClasses'],
                 $observation['bulletinNo']
             );

                 if($exists) { continue; }

                $this->observationCacheRepository->insertObservation($observation,$observationCacheMainList);




        }




    }

    public function existsObservationCacheMain($searchedWord, $niceClasses, $bulletinNo)
    {
        $_niceClasses = [];
        foreach ($niceClasses as $niceClass) {
            $_niceClasses[] = $niceClass['No'];
        }

        $niceClasses = join(',',$_niceClasses);

        $result = $this->findOneBy([
            'searchedWord' => $searchedWord,
            'niceClasses' => $niceClasses,
            'bulletinNo' => $bulletinNo
        ]);

        if ($result) {
            return $result;
        }

        return false;
    }

//    /**
//     * @return ObservationCacheMainList[] Returns an array of ObservationCacheMainList objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ObservationCacheMainList
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
