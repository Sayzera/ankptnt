<?php

namespace App\Repository;

use App\Entity\ObservationCache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @extends ServiceEntityRepository<ObservationCache>
 *
 * @method ObservationCache|null find($id, $lockMode = null, $lockVersion = null)
 * @method ObservationCache|null findOneBy(array $criteria, array $orderBy = null)
 * @method ObservationCache[]    findAll()
 * @method ObservationCache[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ObservationCacheRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private Security $security
    ) {
        parent::__construct($registry, ObservationCache::class);
    }

    public function insertObservation($data, $observationCacheMainList)
    {

        $user = $this->security->getUser() ?? 0;
        $observation = new ObservationCache();
        $observation->setDataSource($data['dataSource']);
        $observation->setSearchedWord($data['searchedWord']);
        $observation->setSearchedWordHtml($data['searchedWordHtml']);
        $observation->setTrademarkName(str_replace("'", "", $data['trademarkName']));
        $observation->setTrademarkNameHtml($data['trademarkNameHtml']);
        $observation->setNiceClasses($data['niceClasses']);
        $observation->setApplicationNo($data['applicationNo']);
        $observation->setApplicationDate($data['applicationDate']);
        $observation->setRegisterDate($data['registerDate']);
        $observation->setProtectionDate($data['protectionDate']);
        $observation->setHolderName($data['holderName']);
        $observation->setBulletinNo($data['bulletinNo']);
        $observation->setBulletinPage($data['bulletinPage']);
        $observation->setFileStatus($data['fileStatus']);
        $observation->setShapeSimilarity($data['shapeSimilarity']);
        $observation->setPhoneticSimilarity($data['phoneticSimilarity']);
        $observation->setIsPriority($data['isPriority']);
        if ($user !== 0) {
            $observation->setUser($user);
        }
        $observation->setObservationCacheMainList($observationCacheMainList);


        // observationCacheMainList id 




        $this->getEntityManager()->persist($observation);
        $this->getEntityManager()->flush();
    }

    // Daha önce kayıt olmuş mu kontrol et
    public function existsObservation($searchedWord, $niceClasses, $bulletinNo,$applicationNo)
    {
        $check = $this->findOneBy(
            [
                'searchedWord' => $searchedWord,
                'niceClasses' => $niceClasses,
                'bulletinNo' => $bulletinNo,
                'applicationNo' => $applicationNo,
            ]
        );

        if ($check) {
            return true;
        }

        return false;
    }

    //    /**
    //     * @return ObservationCache[] Returns an array of ObservationCache objects
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

    //    public function findOneBySomeField($value): ?ObservationCache
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
