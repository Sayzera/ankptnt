<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

class DesignService
{
    private $account_ref = null;

    public function __construct($registry, $request)
    {
        $this->db = $registry->getConnection();
        $this->account_ref = $request->getSession()->get('ref_account');
    }

    public function getDesingList($request)
    {

        $where = " AND 1=1 ";

        $type = $request->query->get('type');

        if($type != null) {
            $where .= " AND son_durum_detay = '$type' ";
        }   

        $sql = "
        select * from v_design_file 
        where firma_id = " . $this->account_ref . "
        AND silinmis = false $where
        ";

        $stmt   =  $this->db->prepare($sql);
        $stmt   = $stmt->execute();
        $result = $stmt->fetchAllAssociative();


        return $result;
    }
    /**
     * Tasarım Bilgisi modal
     */
    public function  findByDesignInfo($id)
    {
        $sql = 'select 
         t1.*,
         t2.ref_responsible,
         CONCAT(t3.col_name,\' \',t3.col_surname) as sorumlu,
         t2.col_extra_design_number,
         t2.col_publication_deferment_number as yayin_erteleme
         from v_design_file t1 
        left join tbl_design_file t2 on t2.col_id = t1.id
        left join tbl_employee t3 on t3.col_id = t2.ref_responsible


         WHERE t1.id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt =  $stmt->execute(['id' => $id]);
        $result = $stmt->fetchAllAssociative();




        if (count($result) > 0) {
            return new JsonResponse([
                'status' => true,
                'message' => 'Design data başarıyla getirildi',
                'data' => $result[0],
            ]);
        } else {
            return new JsonResponse([
                'status' => false,
                'message' => 'Design data getirilirken bir hata oluştu',
                'data' => null
            ]);
        }
    }

    /**
     * Tasarım Dosyalar
     */
    public function findByDesignFiles($id)
    {
        $sql = '
        select * from tbl_design_file_process_document t1 
        left join tbl_design_file_process t2 on t2.design = t1.ref_process_id
        where t2.ref_design_id = :id
        ';
        $stmt = $this->db->prepare($sql);
        $stmt =  $stmt->execute(['id' => $id]);
        $result = $stmt->fetchAllAssociative();

        if (count($result) > 0) {
            return new JsonResponse([
                'status' => true,
                'message' => 'Design data başarıyla getirildi',
                'data' => $result
            ]);
        } else {
            return new JsonResponse([
                'status' => false,
                'message' => 'Design data getirilirken bir hata oluştu',
                'data' => null
            ]);
        }
    }

    /**
     * Tasarım Rüçhanlar
     */
    public function findByDesignPriority($id)
    {
        $sql = '
        select t1.*, t2.col_name_en from tbl_design_file_priority t1
        left join tbl_country t2 on t2.col_id  = t1.country_col_id 
        where t1.ref_design_id  = :id
        ';
        $stmt = $this->db->prepare($sql);
        $stmt =  $stmt->execute(['id' => $id]);
        $result = $stmt->fetchAllAssociative();

        if (count($result) > 0) {
            return new JsonResponse([
                'status' => true,
                'message' => 'Design data başarıyla getirildi',
                'data' => $result
            ]);
        } else {
            return new JsonResponse([
                'status' => false,
                'message' => 'Design data getirilirken bir hata oluştu',
                'data' => null
            ]);
        }
    }

    /**
     * Tasarımlar
     */
    public function findByDesignDesigns($id)
    {
        $siniflarQuery = 'select * from tbl_design_file_classes where ref_design_file_id = :id';
        $stmt = $this->db->prepare($siniflarQuery)->execute(['id' => $id])->fetchAllAssociative();
        $siniflar = count($stmt) > 0 ? $stmt[0] : [];

        $tasarimcilarQuery = "select * from tbl_design_file_designer where ref_design_id = :id";
        $stmt = $this->db->prepare($tasarimcilarQuery)->execute(['id' => $id])->fetchAllAssociative();
        $tasarimcilar = $stmt;


        return new JsonResponse([
            'status' => true,
            'message' => 'Design data başarıyla getirildi',
            'data' => [
                'siniflar' => $siniflar,
                'tasarimcilar' => $tasarimcilar
            ]
        ]);
    }

    /**
     * Tasarım Faturalar
     */
    public function findByDesignInvoices($id)
    {
        $sql = " select *
        FROM v_transaction_for_apiz_file where col_file_id = :id 
        ";
        $stmt = $this->db->prepare($sql);
        $stmt =  $stmt->execute(['id' => $id]);
        $result = $stmt->fetchAllAssociative();

        if (count($result) > 0) {
            return new JsonResponse([
                'status' => true,
                'message' => 'Design data başarıyla getirildi',
                'data' => $result
            ]);
        } else {
            return new JsonResponse([
                'status' => false,
                'message' => 'Design data getirilirken bir hata oluştu',
                'data' => null
            ]);
        }
    }

    /**
     * Tasarım İşlemleri
     */
    public function getDesignDetailActions($id)
    {
        $col_id = $id;
        $query = '
        select t1.*, t2.col_description  as islem,  
        concat(t3.col_name,\' \', t3.col_surname) as kullanici_adi,
        t4.col_detail as detay
        from v_design_file_process t1 
        left join tbl_f_feelist t2 on  t2.col_id = t1.ref_feelist 
        left join tbl_employee t3 on t3.col_id = t1.employee_col_id 
        left join tbl_design_file_last_status t4 on t4.col_id = t1.ref_design_process_last_status_id 
        where t1.ref_design_id = ' . $col_id . ' ';

        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();

        $result = $stmt->fetchAllAssociative();

        if (count($result) > 0) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Tasarım  detay işlemleri',
                'data' => $result
            ]);
        } else {
            return new JsonResponse([
                'success' => false,
                'message' => 'Tasarım işlemleri bulunamadı',
                'data' => []
            ]);
        }
    }
}


// tbl_design_file_designer 	

// lef join tdfd.col_tc_identity_number