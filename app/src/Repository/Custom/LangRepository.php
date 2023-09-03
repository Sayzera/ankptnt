<?php

namespace App\Repository\Custom;

use App\Entity\Custom\Lang;
use App\Entity\Custom\LangMessages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

/**
 * @extends ServiceEntityRepository<Lang>
 *
 * @method Lang|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lang|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lang[]    findAll()
 * @method Lang[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LangRepository extends ServiceEntityRepository
{
    private $entityManager;


    public function __construct(ManagerRegistry $registry)
    {
        $this->entityManager = $registry->getManager('custom');
        parent::__construct($registry, Lang::class);
    }


    /**
     * @param $langName
     * @description addLang methodu lang parametresine göre lang ekler örnek: tr, en, de
     * @return Lang|object
     *
     */
    public function addLang($langName = 'tr') {
        // existing lang
        $lang = $this->entityManager->getRepository(Lang::class)->findOneBy(['name' => $langName]);

        if ($lang) {
            return $lang;
        }

        $lang = new Lang();
        $lang->setName($langName);
        $lang->setStatus(true);
        $dateTimeImmutable = new \DateTimeImmutable();
        $lang->setCreatedAt($dateTimeImmutable);
        $lang->setUpdatedAt($dateTimeImmutable);

        $this->entityManager->persist($lang);
        $this->entityManager->flush();

        return $lang;
    }

    public function getActiveLang() {
    // get all
     return $this->entityManager->getRepository(Lang::class)
         ->findBy(['status' => true]);
    }

    public function getLang($langName = 'tr') {
        $lang = $this->entityManager->getRepository(Lang::class)->findOneBy(['name' => $langName]);

        if ($lang) {
            return $lang;
        }

        return null;
    }

    public function deleteAllLangs() {
        $deleteQuery = $this->entityManager->createQuery('DELETE FROM App\Entity\Custom\Lang')->execute();
    }

    /**
     * @param $par
     * @description addLangMessage methodu lang parametresine göre lang mesajı ekler
     * @return array|object|null
     * @throws \Exception
     */
    public function addLangMessage($par) {
        $lang = $this->entityManager->getRepository(Lang::class)->findOneBy(['name' => $par['lang']]);

        if (!$lang) {
            throw new \Exception('Dil bulunamadı');
        }

        $langMessage = $this->entityManager->getRepository(LangMessages::class)->findOneBy(['name' => $par['key'], 'lang' => $lang]);


        if($langMessage) {
            throw new \Exception('Bu dil mesajı zaten var');
        }

        $langMessage = new LangMessages();
        $langMessage->setLang($lang); // ilişkili tabloya ekleme
        $langMessage->setName($par['key']);
        $langMessage->setMessage($par['value']);
        $langMessage->setStatus(true);
        $dateTimeImmutable = new \DateTimeImmutable();
        $langMessage->setCreatedAt($dateTimeImmutable);
        $langMessage->setUpdatedAt($dateTimeImmutable);

        $this->entityManager->persist($langMessage);
        $this->entityManager->flush();

        return $this->getAllLangMessage($par);
    }

    /**
     * @param $par
     * @description getAllLangMessage methodu lang parametresine göre lang mesajlarını getirir
     * @return array
     * @throws \Exception
     */
    public function  getAllLangMessage($par) {
        $lang = $this->entityManager->getRepository(Lang::class)->findOneBy(['name' => $par['lang']]);

        if (!$lang) {
            throw new \Exception('Dil bulunamadı');
        }

        $data = [];

        $langMessages = $this->entityManager->getRepository(LangMessages::class)->findBy(['lang' => $lang]);

        foreach ($langMessages as $langMessage) {
            $data[] = [
                'key' => $langMessage->getName(),
                'value' => $langMessage->getMessage(),
                'lang' => $langMessage->getLang()->getName(),
                'id' => $langMessage->getId()
            ];
        }

        return $data;

    }


    /**
     * @param $par
     * @description getLangMessage methodu name ve lang parametrelerine göre lang mesajını getirir
     * @return object|null
     *
     */
    public function getLangMessage($par) {
        $lang = $this->entityManager->getRepository(Lang::class)->findOneBy(['name' => $par['lang']]);

        if (!$lang) {
           throw new \Exception('Dil bulunamadı');
        }

        $langMessage = $this->entityManager->getRepository(LangMessages::class)->findOneBy(['name' => $par['key'], 'lang' => $lang]);

        if($langMessage) {
            throw new \Exception('Bu dil mesajı zaten var');
        }

        return null;
    }

    /**
     * @description  Eklenen lang mesajını siler
     * @param $par
     */
    public function  deleteLangMessage($par) : array {
         $id = $par['id'];
         // veriyi bul
         $langMessage = $this->entityManager->getRepository(LangMessages::class)->find($id);
         if(!$langMessage) {
             return throw new \Exception('Dil mesajı bulunamadı');
         }
        // veriyi sil
        $this->entityManager->remove($langMessage);
        $this->entityManager->flush();

          return [
                'key' => $langMessage->getName(),
                'value' => $langMessage->getMessage(),
                'lang' => $langMessage->getLang()->getName(),
                'id' => $langMessage->getId()
          ];

    }


    public function updateLangMessage($par) {
        $lang = $this->entityManager->getRepository(Lang::class)->findOneBy(['name' => $par['lang']]);

        if (!$lang) {
            throw new \Exception('Dil bulunamadı');
        }

        $langMessage = $this->entityManager->getRepository(LangMessages::class)->find($par['id']);

        if(!$langMessage) {
            throw new \Exception('Bu dil mesajı bulunamadı');
        }

        // update
        $langMessage->setName($par['key']);
        $langMessage->setMessage($par['value']);
        $langMessage->setStatus(true);
        $dateTimeImmutable = new \DateTimeImmutable();
        $langMessage->setCreatedAt($dateTimeImmutable);
        $langMessage->setUpdatedAt($dateTimeImmutable);

        $this->entityManager->persist($langMessage);
        $this->entityManager->flush();

        return [
            'key' => $langMessage->getName(),
            'value' => $langMessage->getMessage(),
            'lang' => $langMessage->getLang()->getName(),
            'id' => $langMessage->getId()
        ];
    }




//    /**
//     * @return Lang[] Returns an array of Lang objects
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

//    public function findOneBySomeField($value): ?Lang
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
