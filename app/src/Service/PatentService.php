<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

use function PHPSTORM_META\type;

class PatentService
{
    private $account_ref = null;
    public function __construct($registry, $request)
    {
        $this->db = $registry->getConnection();
        $this->account_ref = $request->getSession()->get('ref_account');
    }
    /**
     * Patent başvuru listesi
     */
    public function getPatentApplicationList()
    {
        $sql = ' SELECT t1.col_id AS id,
         t1.col_account_referance_number AS firma_referans_numarasi,
         t1.col_application_date AS basvuru_tarihi,
         t1.col_application_number AS basvuru_numarasi,
         t1.col_is_deleted AS silinmis,
         t1.col_c_file_number AS dosya_numarasi,
         t1.col_file_number AS ilk_dosya_no,
         t1.col_file_secondary_number AS ikincil_dosya_no,
         t1.col_comment_string AS aciklama,
         t1.col_title AS baslik,
         t2.col_id AS firma_id,
         t2.col_title AS firma_unvan,
         t2.col_code AS firma_kod,
         t3.col_name AS ulke,
         t4.col_comment AS ek_patent_aciklama,
         t4.col_additional_patent_appnum AS ek_patent_basvuru_numarasi,
         t4.col_additional_patent_filenum AS ek_patent_dosya_numarasi,
         t4.col_additional_patent_filling_date AS ek_patent_dosyalama_tarihi,
         t4.col_divided_patent_appnum AS bolunmus_patent_basvuru_numarasi,
         t4.col_divided_patent_filenum AS bolunmus_patent_dosya_numarasi,
         t4.col_divided_patent_filling_date AS bolunmus_patent_dosyalama_tarihi,
         t5.col_epapplication_date AS ep_basvuru_tarihi,
         t5.col_epapplication_number AS ep_basvuru_numarasi,
         t5.col_epapproval_date AS ep_onay_tarihi,
         t5.col_eppublication_date AS ep_yayin_tarihi,
         t5.col_eppublication_number AS ep_yayin_numarasi,
         t5.col_epvalidity_duedate AS ep_gecerlilik_son_tarihi,
         t5.col_pctapplication_date AS pct_basvuru_tarihi,
         t5.col_pctapplication_number AS pct_basvuru_numarasi,
         t5.col_pctpart_info AS pct_kisim_bilgisi,
         t5.col_pctpublication_date AS pct_yayin_tarihi,
         t5.col_pctpublication_number AS pct_yayin_numarasi,
         t6.col_id AS son_durum_id,
         t6.col_detail AS son_durum_detay,
         t7.col_patent_number AS patent_numarasi,
         t7.col_patent_paper_cert_date AS patent_belge_sertifika_tarihi,
         t7.col_patent_paper_date AS patent_belge_tarihi,
         t8.col_detail AS basvuru_sistemi,
         t9.col_detail AS basvuru_turu,
         t10.col_last_paid_date AS son_odenen_taksit_tarihi,
         t10.col_last_paid_year AS son_odenen_taksit_yili,
         t10.col_next_paid_date AS gelecek_taksit_tarihi,
         t10.col_next_paid_year AS gelecek_taksit_yili FROM tbl_patent_file t1 JOIN tbl_account t2 ON t2.col_id = t1.ref_account_id LEFT JOIN tbl_country t3 ON t3.col_id = t1.ref_country_id LEFT JOIN tbl_patent_file_additional t4 ON t4.col_id = t1.ref_patent_additional_id LEFT JOIN tbl_patent_file_country_entrance t5 ON t5.col_id = t1.ref_patent_countryentrance_id LEFT JOIN tbl_patent_file_last_status t6 ON t6.col_id = t1.ref_patentfile_last_status_id LEFT JOIN tbl_patent_file_patentfm t7 ON t7.col_id = t1.ref_patent_id LEFT JOIN tbl_patent_application_system t8 ON t8.col_id = t1.ref_applicationsystem_id LEFT JOIN tbl_patent_application_type t9 ON t9.col_id = t1.ref_applicationtype_id LEFT JOIN tbl_patent_file_installment t10 ON t10.col_id = t1.ref_patent_installment_id WHERE t1.ref_account_id =' . $this->account_ref . ' ';
        $stmt = $this->db->prepare($sql);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();

        // Sonuç var mı ? 
        if (count($result) > 0) {
            return $result;
        } else {
            return $result;
        }
    }


    /**
     * Marka Fatura Listesi
     */
    public function getPreApplicationRegistrationDetailInvoice($file_number)
    {

        /**
         * col_file_id = 
         */
        $query = "
        select *
        FROM v_transaction_for_apiz_file where col_file_id = " . $file_number . " 
        ";
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();

        if (count($result) > 0) {
            return new JsonResponse([
                'data' => $result,
                'message' => 'Fatura bilgisi başarıyla getirildi',
                'success' => true
            ], 200);
        } else {
            return new JsonResponse([
                'data' => [],
                'message' => 'Fatura bilgisi bulunamadı',
                'success' => false
            ], 200);
        }
    }


    /**
     * Patent başvuru listesi json
     */
    public function getPatentApplicationListJson($request)
    {
        $where = ' AND 1=1 ';
        $type = $request->query->get('type');


        if($type != "null") {
            $where .= " AND t1.son_durum_detay = '$type' ";
        }


        
        $sql = "SELECT * from v_patent_file t1
        where t1.firma_id = " . $this->account_ref . "
        AND silinmis =false
        $where
        ";
        $stmt = $this->db->prepare($sql);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();


        // Sonuç var mı ? 
        if (count($result) > 0) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Patent başvuru listesi',
                'data' => $result
            ]);
        } else {
            return new JsonResponse([
                'success' => false,
                'message' => 'Patent başvuru listesi bulunamadı',
                'data' => []
            ]);
        }
    }


    /**
     * Paten başvuru modal detail bilgileri
     */
    public function getPatentApplicationDetail($id)
    {
        $col_id = $id;
        $query = 'select * from v_patent_file 
        
        vpf where vpf.id = ' . $col_id . '';

        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();

        // tbl_patent_file
        $patent_file_query = '
        select 
        t1.*,
        t1.col_id as patent_id,
        t2.*, 
        t3.* ,
        t4.*,
        t4.col_detail as tubitak_type,
        t5.*,
        t7.col_title as account_title
        from tbl_patent_file t1 
            left join tbl_user t2 on t2.col_id = t1.ref_responsible
            left join tbl_patent_file_research_report t3 on t3.col_id = t1.ref_patentfile_research_report_id 
            left join tbl_patent_tubitak_app_type t4 on t4.col_id = t1.ref_tubitak_status_type_id
            left join tbl_patent_file_ipc t5 on t5.ref_patentfile_id = t1.col_id 
            left join tbl_account t6 on t6.col_id = t1.ref_account_id 
            left join tbl_patent_file_applicant t7 on t7.ref_patentfile_id = t1.col_id
                where t1.col_id = ' . $col_id . ' ';


        $stmt = $this->db->prepare($patent_file_query);
        $stmt = $stmt->execute();
        $patent_file_result = $stmt->fetchAllAssociative();


        // Buluşcular

        $inventor_query = 'select * from tbl_patent_file_inventor t1 
        where t1.ref_patentfile_id = ' . $col_id . ' ';

        $stmt = $this->db->prepare($inventor_query);
        $stmt = $stmt->execute();
        $inventor_result = $stmt->fetchAllAssociative();



        if (count($result) > 0) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Patent başvuru detay bilgisi',
                'data' => $result[0],
                'dosya' => count($patent_file_result) > 0 ? $patent_file_result[0] : [],
                'inventor' => count($inventor_result) > 0 ? $inventor_result : [],
            ]);
        } else {
            return new JsonResponse([
                'success' => false,
                'message' => 'Patent başvuru detay bilgisi bulunamadı',
                'data' => []
            ]);
        }
    }

    /**
     * Patent Rüçhan Listesi
     */
    public function getOppositionPriority($patent_id)
    {
        $query = 'select t1.*, t2.col_name as ulke  from tbl_patent_file_priority t1
        left join tbl_country t2 on t2.col_id = t1.country_col_id
        
        where t1.ref_patentfile_id = ' . $patent_id . ' ';
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();
        if (count($result) > 0) {
            return new JsonResponse([
                'data' => $result,
                'message' => 'Rüçhan listesi başarıyla getirildi',
                'success' => true
            ], 200);
        } else {
            return new JsonResponse([
                'data' => [],
                'message' => 'Rüçhan listesi bulunamadı',
                'success' => false
            ], 200);
        }
    }

    /**
     * Patent başvuru detay işlemleri (Actions)
     */

    public function getPatentDetailActions($id)
    {
        $col_id = $id;
        $query = '
        select t1.*, t2.col_description  as islem,  
        concat(t3.col_name,\' \', t3.col_surname) as kullanici_adi,
        t4.col_detail as detay
        from v_patent_file_process t1 
        left join tbl_f_feelist t2 on  t2.col_id = t1.ref_feelist 
        left join tbl_employee t3 on t3.col_id = t1.employee_col_id 
        left join tbl_patent_file_last_status t4 on t4.col_id = t1.ref_patentfile_process_last_status_id 
        where t1.ref_patentfile_id = ' . $col_id . ' ';

        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();

        $result = $stmt->fetchAllAssociative();

        if (count($result) > 0) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Patent başvuru detay işlemleri',
                'data' => $result
            ]);
        } else {
            return new JsonResponse([
                'success' => false,
                'message' => 'Patent başvuru detay işlemleri bulunamadı',
                'data' => []
            ]);
        }
    }

    /**
     * Patent başvuru detay dosyalar
     */

    public function getPatentDetailFiles($id)
    {
        $col_id = $id;
        $query = 'select * from tbl_patent_file_document 
        where ref_patentfile_id = ' . $col_id . ' ';

        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();

        $result = $stmt->fetchAllAssociative();

        if (count($result) > 0) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Patent başvuru dosyaları getirildi',
                'data' => $result
            ]);
        } else {
            return new JsonResponse([
                'success' => false,
                'message' => 'Patent başvuru dosyaları bulunamadı',
                'data' => []
            ]);
        }
    }

    /**
     * Patent detay modal ülke girişi
     */
    public function getPatentDetailCountryEntrance($id)
    {
        $query = 'select * from tbl_patent_file t1 
        left join tbl_patent_file_country_entrance t2 on t2.col_id = t1.ref_patent_countryentrance_id
        where t1.col_id = ' . $id . '
        ';

        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();

        $result = $stmt->fetchAllAssociative();

        if (count($result) > 0) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Patent detay modal ülke girişi',
                'data' => $result[0]
            ]);
        } else {
            return new JsonResponse([
                'success' => false,
                'message' => 'Patent detay modal ülke girişi bulunamadı',
                'data' => []
            ]);
        }
    }

    /**
     * Başvuru öncesi detay modal bilgisi 
     */
    public function getPreApplicationRegistrationDetail($id)
    {
        $col_id = $id;
        $query = 'SELECT * FROM v_pt_temp vpt 
         WHERE vpt.col_id = ' . $col_id . ' ';
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();

        if (count($result) > 0) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Başvuru öncesi detay bilgisi',
                'data' => $result[0]
            ]);
        } else {
            return new JsonResponse([
                'success' => false,
                'message' => 'Başvuru öncesi detay bilgisi bulunamadı',
                'data' => []
            ]);
        }
    }



    /**
     * başvuru öncesi detay işlemleri
     */
    public function getPreApplicationRegistrationDetailProcess($id)
    {
        $col_id = $id;
        $query = '
            select t1.*, t2.col_name, t2.col_surname,
            t3.col_detail,
            t4.col_description as islem
            from tbl_patent_temporary_process t1 

            left join tbl_employee t2 on t2.col_id = t1.employee_col_id
            left join tbl_patent_file_process_last_status t3 on t3.col_id  = t1.ref_patent_process_last_status_id
            left join tbl_f_feelist t4 on t4.col_id = t1.ref_feelist

            where t1.ref_patent_temporary_id =' . $col_id . '
        ';
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();

        if (count($result) > 0) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Başvuru öncesi detay işlemleri',
                'data' => $result
            ]);
        } else {
            return new JsonResponse([
                'success' => false,
                'message' => 'Başvuru öncesi detay işlemleri bulunamadı',
                'data' => []
            ]);
        }
    }

    /**
     * Başvuru öncesi kayıt listesi
     */
    public function getPreApplicationRegistrationList()
    {
        $query = 'SELECT * FROM v_pt_temp WHERE ref_account = ' . $this->account_ref . ' ';
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();

        if (count($result) > 0) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Başvuru öncesi kayıt listesi',
                'data' => $result
            ]);
        } else {
            return new JsonResponse([
                'success' => false,
                'message' => 'Başvuru öncesi kayıt listesi bulunamadı',
                'data' => $result
            ]);
        }
    }

    /**
     * Patent Dokümanlar json
     */

    public function getPatentDocumentListJson()
    {
        $query = 'SELECT * FROM v_patent_document WHERE ref_account = ' . $this->account_ref;
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();

        return new JsonResponse([
            'success' => true,
            'message' => 'Patent döküman listesi',
            'data' => $result
        ]);
    }
    /**
     * Patent modal belge yayın bilgileri
     */
    public function getPatentPublicationDetail($id)
    {

        $patent_file_query = 'select 
        *, t1.col_id as patent_id,
        t4.col_no as yayin_no,
        t5.col_detail as rapor_detay,
        t6.col_detail as inceleme_detay,
        t7.col_detail as basvuru_sistemi
        from 
        tbl_patent_file t1 
        left join tbl_patent_file_patentfm t2 on t2.col_id = t1.ref_patent_id
        left join tbl_patent_file_publication t3 on t3.col_id = t1.ref_patent_publication_id
        left join tbl_publication_bulletin t4 on t4.col_id = t3.ref_patent_bulletin_id
        left join tbl_patent_file_research_report t5 on t5.col_id = t1.ref_patentfile_research_report_id
        left join tbl_patent_file_review_report t6 on t6.col_id = t1.ref_patentfile_review_report_id
        left join tbl_patent_application_system t7 on t7.col_id = t1.ref_applicationsystem_id
        
        where t1.col_id = ' . $id . ' ';
        $stmt = $this->db->prepare($patent_file_query);
        $stmt = $stmt->execute();
        $patent_result = $stmt->fetchAllAssociative();
        $ref_patent_publication_id = null;



        if (count($patent_result) > 0) {
            $ref_patent_publication_id = $patent_result[0]['ref_patent_publication_id'];
        }

        // if ($ref_patent_publication_id == null) {
        //     return new JsonResponse([
        //         'success' => false,
        //         'message' => 'Patent belge yayın bilgisi bulunamadı',
        //         'data' => []
        //     ]);
        // }

        if (count($patent_result) > 0) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Patent belge yayın detay bilgisi',
                'data' => $patent_result[0]
            ]);
        } else {
            return new JsonResponse([
                'success' => false,
                'message' => 'Patent belge yayın bilgisi bulunamadı',
                'data' => []
            ]);
        }
    }

    /**
     * Patent modal Taksit bilgileri
     */

    public function getPatentModalInstallment($id)
    {
        $patent_file_query = 'select t2.*, t1.col_id, t1.ref_patent_installment_id from 
        tbl_patent_file t1
        left join tbl_patent_file_installment t2 on 
        t2.col_id = t1.ref_patent_installment_id
         where t1.col_id = ' . $id . ' ';

        $stmt = $this->db->prepare($patent_file_query);
        $stmt = $stmt->execute();
        $patent_result = $stmt->fetchAllAssociative();


        if (count($patent_result) > 0) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Patent taksit bilgisi',
                'data' => $patent_result[0]
            ]);
        } else {
            return new JsonResponse([
                'success' => false,
                'message' => 'Patent taksit bilgisi bulunamadı',
                'data' => []
            ]);
        }
    }
    /**
     * Patent File Ek Patent Bilgileri
     */
    public function getPatentFileAdditional($id)
    {
        $patent_file_query = ' 
            select t2.*, t1.col_id, t1.ref_patent_additional_id from tbl_patent_file t1
            left join  tbl_patent_file_additional t2 on t2.col_id = t1.ref_patent_additional_id
            where t1.col_id = ' . $id . ' ';


        $stmt = $this->db->prepare($patent_file_query);
        $stmt = $stmt->execute();
        $patent_result = $stmt->fetchAllAssociative();

        if (count($patent_result) > 0) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Patent file ek patent bilgisi',
                'data' => $patent_result[0]
            ]);
        } else {
            return new JsonResponse([
                'success' => false,
                'message' => 'Patent file ek patent bilgisi bulunamadı',
                'data' => []
            ]);
        }
    }

    /**
     * Patent File Faturalar
     */
    public function getPatentFileInvoice($id)
    {
        $patent_file_query =  "
        SELECT t.*
        FROM v_transaction_for_apiz_file t
        inner JOIN tbl_patent_file tpf ON tpf.col_id  = T.col_file_id 
        where t.col_status = 'AÇIK' AND tpf.col_id = $id
        ";

        $stmt = $this->db->prepare($patent_file_query);
        $stmt = $stmt->execute();
        $patent_result = $stmt->fetchAllAssociative();

        if (count($patent_result) > 0) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Patent file fatura bilgisi',
                'data' => $patent_result
            ]);
        } else {
            return new JsonResponse([
                'success' => false,
                'message' => 'Patent file fatura bilgisi bulunamadı',
                'data' => []
            ]);
        }
    }

    public function getPatentDocumentList()
    {
        $query = 'SELECT * FROM v_patent_document WHERE ref_account = ' . $this->account_ref . ' ';
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();

        return $result;
    }
}
