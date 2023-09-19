<?php

namespace App\Service;


use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

class  UserService {


    public function __construct(private ManagerRegistry $registry, private Security $security)
    {
        $this->db = $this->registry->getConnection();
    }



    public function getTblUserAccount() {

        $userId = $this->security->getUser()->getColId();
        $query = 'select * from tbl_user_account  where ref_user = ' . $userId;

        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();

        $tblUserAccount = $stmt->fetchAllAssociative();

        return $tblUserAccount[0] ?? [];

    }
}