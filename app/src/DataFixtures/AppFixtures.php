<?php

namespace App\DataFixtures;

use App\Entity\Custom\Lang;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function __construct(ManagerRegistry $registry)
    {
        $this->CustomEntityManager = $registry->getManager();

    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $lang = new Lang();


        // existing lang
        $lang = $this->CustomEntityManager->getRepository(Lang::class)->findOneBy(['name' => 'tr']);
        if ($lang) {
            return;
        }

        $lang = new Lang();

        $lang->setName('tr');
        $lang->setStatus(true);
        $dateTimeImmutable = new \DateTimeImmutable();
        $lang->setCreatedAt($dateTimeImmutable);
        $lang->setUpdatedAt($dateTimeImmutable);

        $this->CustomEntityManager->persist($lang);
        $this->CustomEntityManager->flush();

    }
}
