<?php

namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

class SearchObservationService
{

    public function __construct(ManagerRegistry $registry)
    {
        $this->db = $registry->getConnection('arastirgozle');

        $this->patiDB = $registry->getConnection();
    }

    public function getObservations()
    {
        $observations = $this->db->prepare('SELECT * FROM tbl_tm  ')
            ->execute()
            ->fetchAllAssociative();

        return $observations;
    }

    public function findNilSendMailList($col_userId, $userEmail) {
        $sql = "SELECT 
                    t1.col_id as userId, 
                    t1.col_username as username,
                    t2.ref_account as accountId,
                    t3.mail as mail
                    from tbl_user t1
                    left join tbl_user_account t2 on t1.col_id = t2.ref_user
                    left join nil_send_mail_list t3 on t3.firma_id = t2.ref_account
                    where t1.col_id = $col_userId
        ";

        $result = $this->patiDB->prepare($sql)->execute()->fetchAllAssociative();

        $email = filter_var($userEmail, FILTER_VALIDATE_EMAIL);


        if($email) {
            return [
                'mail' => $userEmail
            ];
        } else {
            if(count($result) > 0 && filter_var($result[0]['mail'], FILTER_VALIDATE_EMAIL)) {
                return $result[0];
            } else {
                return false;
            }
        }
    
    }  

    /**
     * Eşya listesini getir
     */
    public function getItems($applicationno, $basvuru_numarasi)
    {
        // basvuru_numarasi = Marka sahibine ait başvuru numarası

        // basvuru sahibi class açıklaması
        $basvuru_sahibi_class_aciklama = '';
        $basvuru_sahibi_sql  = "SELECT * FROM tbl_tm WHERE col_application_number = :applicationno";
        $basvuru_sahibi = $this->db->prepare($basvuru_sahibi_sql)->execute(['applicationno' => $basvuru_numarasi])->fetchAssociative();


        if (!$basvuru_sahibi) {
            $basvuru_sahibi['col_nice_classes'] = '';
        }

        $basvuru_sahibi_col_nice_classes = explode('/', $basvuru_sahibi['col_nice_classes']);
        if (count($basvuru_sahibi_col_nice_classes) > 0) {
            $basvuru_sahibi_col_nice_classes =  array_map('trim', $basvuru_sahibi_col_nice_classes);
            $basvuru_sahibi_col_nice_classes =  array_map('intval', $basvuru_sahibi_col_nice_classes);
            $basvuru_sahibi_col_nice_classes = array_filter($basvuru_sahibi_col_nice_classes, function ($item) {
                if ($item > 0) return $item;
            });
        }

        if (isset($basvuru_sahibi['col_id'])) {
            // Basvuru sahibi eşya listesi
            $basvuru_sahibi_esya_sql = "select * from tbl_tm_goods where ref_trademark = :ref_trademark";
            $basvuru_sahibi_esya = $this->db->prepare($basvuru_sahibi_esya_sql)->execute(['ref_trademark' => $basvuru_sahibi['col_id']])->fetchAllAssociative();

            foreach ($basvuru_sahibi_esya as $item) {
                $basvuru_sahibi_class_aciklama .= $item['col_goods'];
            }
        }

        //-------------------------------- İtiraz edilecek kişi 
        $basvuru_itiraz_class_aciklama = '';
        $basvuru_itiraz_sql  = "SELECT * FROM tbl_tm WHERE col_application_number = :applicationno";
        $basvuru_itiraz = $this->db->prepare($basvuru_itiraz_sql)->execute(['applicationno' => $applicationno])->fetchAssociative();
        $basvuru_itiraz_col_nice_classes = explode('/', $basvuru_itiraz['col_nice_classes']);
        if (count($basvuru_itiraz_col_nice_classes) > 0) {
            $basvuru_itiraz_col_nice_classes =  array_map('trim', $basvuru_itiraz_col_nice_classes);
            $basvuru_itiraz_col_nice_classes =  array_map('intval', $basvuru_itiraz_col_nice_classes);
            $basvuru_itiraz_col_nice_classes = array_filter($basvuru_itiraz_col_nice_classes, function ($item) {
                if ($item > 0) return $item;
            });
        }

        // Basvuru itiraz eşya listesi
        $basvuru_itiraz_esya_sql = "select * from tbl_tm_goods where ref_trademark = :ref_trademark";
        $basvuru_itiraz_esya = $this->db->prepare($basvuru_itiraz_esya_sql)->execute(['ref_trademark' => $basvuru_itiraz['col_id']])->fetchAllAssociative();

        foreach ($basvuru_itiraz_esya as $item) {
            $basvuru_itiraz_class_aciklama .= $item['col_goods'];
        }

        $data =  [
            'basvuru_sahibi_class_aciklama' => $basvuru_sahibi_class_aciklama,
            'basvuru_itiraz_class_aciklama' => $basvuru_itiraz_class_aciklama,
            'basvuru_sahibi_col_nice_classes' => $basvuru_sahibi_col_nice_classes,
            'basvuru_itiraz_col_nice_classes' => $basvuru_itiraz_col_nice_classes,
        ];

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Eşya listesi',
            'data' => $data
        ]);
    }

    public function getTblTm($applicationno)
    {
        $tbl_tm_sql = 'SELECT * FROM tbl_tm WHERE col_application_number = :applicationno';
        $tbl_tm = $this->db->prepare($tbl_tm_sql)->execute(['applicationno' => $applicationno])->fetchAllAssociative();

        if (count($tbl_tm) == 0) {
            return false;
        }

        return $tbl_tm[0];
    }
}
