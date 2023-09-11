<?php

namespace App\Service;


use Doctrine\Persistence\ManagerRegistry;

class ApizUserService
{
    private  $db;
    public function __construct(private ManagerRegistry $registry)
    {
        $this->db = $this->registry->getConnection('apz');
    }

    public function getPatiTblEmployee() : array
    {
        // get tbl_employee select
       $patiTblEmployeeUsers = $this->db->prepare('SELECT * FROM tbl_employee')->execute();
       $patiUsers =  $patiTblEmployeeUsers->fetchAllAssociative();
       return  $patiUsers;
    }


}