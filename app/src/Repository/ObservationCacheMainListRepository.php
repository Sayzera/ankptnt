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
        private  ObservationCacheRepository $observationCacheRepository
    ) {
        parent::__construct($registry, ObservationCacheMainList::class);
    }

    public function insertObservatinCacheMain($data, $observationList)
    {


        $_niceClasses = [];
        foreach ($data['NiceClasses'] as $niceClass) {
            $_niceClasses[] = $niceClass['No'];
        }

        $niceClasses = join(',', $_niceClasses);

        $observationCacheMainList = new ObservationCacheMainList();
        $observationCacheMainList->setSearchedWord($data['TrademarkName']);
        $observationCacheMainList->setNiceClasses($niceClasses);
        $observationCacheMainList->setBulletinNo($data['BulletinNo']);
        $observationCacheMainList->setIsCompleted(false);

        /**
         * Eğer gözlem otomatik olarak yapılmış ise burda yapılan gözlem 
         * yim mi yoksa yda mı bunuda belirtiyoruz 
         */
        if (isset($data['account_id'])) {
            $observationCacheMainList->setRefAccountId($data['account_id']);
            $observationCacheMainList->setTrademarkId($data['trademark_id']);
            $observationCacheMainList->setYimMarka($data['yim_marka'] ?? false);
            $observationCacheMainList->setYdaMarka($data['yda_marka'] ?? false);
            $observationCacheMainList->setCompanyEmail($data['foreign_company_email'] ?? null);
            $observationCacheMainList->setIsForeignCompany($data['is_foreign_company'] ?? false);
        }


        $this->getEntityManager()->persist($observationCacheMainList);
        $this->getEntityManager()->flush();

        if (!isset($observationList['trademarkSearchList'])) {
            return;
        }

        foreach ($observationList['trademarkSearchList'] as $observation) {
            /**
             * gözlem sonuçları daha önceden eklenmiş mi
             */
            $exists = $this->observationCacheRepository->existsObservation(
                $observation['searchedWord'],
                $observation['niceClasses'],
                $observation['bulletinNo'],
                $observation['applicationNo']
            );

            if ($exists) {
                continue;
            }

            $this->observationCacheRepository->insertObservation($observation, $observationCacheMainList);
        }


        // update  observationCacheMainList
        $observationCacheMainList->setIsCompleted(true);
        $this->getEntityManager()->persist($observationCacheMainList);
        $this->getEntityManager()->flush();
    }

    public function existsObservationCacheMain($searchedWord, $niceClasses, $bulletinNo, $data = null)
    {
        $_niceClasses = [];
        foreach ($niceClasses as $niceClass) {
            $_niceClasses[] = $niceClass['No'];
        }

        $niceClasses = join(',', $_niceClasses);

        /**
         * Otomatik Gözlem çalışmıştır
         */
        if (isset($data['trademark_id']) &&  $data['trademark_id'] && $data['account_id']) {

            $result = $this->findOneBy([
                'searchedWord' => $searchedWord,
                'niceClasses' => $niceClasses,
                'bulletinNo' => $bulletinNo,
                'trademark_id' => $data['trademark_id'],
                'ref_account_id' => $data['account_id'],
                'is_completed' => 1
            ]);

            if ($result) {
                return $result;
            } else {
                return false;
            }
        }

        /**
         * Tekli gözlem çalışmıştır
         */
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
