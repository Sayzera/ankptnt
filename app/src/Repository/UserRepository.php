<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry, private UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct($registry, User::class);
    }

    public function registerUser($tblEmployee)
    {
        foreach ($tblEmployee as $employee) {
            // Eğer kullanıcı daha önceden kayıt olmuşsa kayıt etme
            if (!$this->existsUser($employee['col_email'])) {
                $user = new User();
                $hashedPassword = $this->passwordHasher->hashPassword(
                    $user,
                    $employee['col_password']
                );
                $user->setEmail($employee['col_email']);
                $user->setPassword($hashedPassword);
                $user->setRoles(['ROLE_USER']);
                $user->setColExpiryDate($employee['col_expiry_date']);
                $user->setColIsCustomerRepresentative($employee['col_is_customer_representative']);
                $user->setColIsDeleted($employee['col_is_deleted']);
                $user->setColLastLogin($employee['col_last_login']);
                $user->setColLevel($employee['col_level']);
                $user->setColName($employee['col_name']);
                $user->setColStartingofEmployment($employee['col_startingof_employment']);
                $user->setColSurname($employee['col_surname']);
                $user->setColWorkgroupId($employee['col_workgroup_id']);
                $user->setColDepartmentId($employee['col_department_id']);
                $user->setColUsername($employee['col_username']);
                $user->setColUnixUsername($employee['col_unix_username']);
                $user->setColFirstPage($employee['col_first_page']);
                $user->setColRegistrationNumber($employee['col_registration_number']);
                $user->setColWatchAuth($employee['col_watch_auth']);
                $user->setColIsWorkingOn($employee['col_is_working_on']);
                $user->setColExpiryDate($employee['col_expiry_date']);
                $user->setColId($employee['col_id']);
                $this->getEntityManager()->persist($user);
                $this->getEntityManager()->flush();
            }
        }
    }


    public function  existsUser($email)
    {
        $userExist = $this->findOneBy(['email' => $email]);

        if ($userExist) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
