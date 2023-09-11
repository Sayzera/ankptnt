<?php
namespace App\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;

class DomesticBrandService {
    private  $db;
    public function __construct(private ManagerRegistry $registry)
    {
        $this->db = $this->registry->getConnection('apz');
    }

    public function getDomesticBrand($id = 0)
    {
        $where = 'WHERE 1=1 AND';
        $where .= ' silinmis = false AND';

        if($id > 0){
            $where .= ' id = '.$id. ' ';
        }

        $where = rtrim($where, 'AND');




        $domesticBrand = $this->db->prepare('SELECT * FROM v_trademark_yim_file  '.$where.' LIMIT 50');
        // get all
        $stmt = $domesticBrand->execute();
        $domesticBrand = $stmt->fetchAllAssociative();

        return $domesticBrand;
    }


}