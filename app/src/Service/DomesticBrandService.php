<?php

namespace App\Service;

use App\Config\TrademarkFilter;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Util\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class DomesticBrandService
{
    private $db;

    // dataTableParams;
    private $logo;
    private $marka;
    private $sinif;
    private $dosyaNo;
    private $basvuruNo;
    private $basvuruTarihi;
    private $tescilNo;
    private $yenilemeTarihi;
    private $dosyaSonDurum;
    private $sayfaNumarasi = 0;

    // pagination
    private $totalCount;
    private $limit = 10;
    private $filteredCount;


    public function __construct(private ManagerRegistry $registry, private Security $security)
    {
        $this->db = $this->registry->getConnection('apz');
    }

    /**
     * kayıtlı olan bültenleri ve son tarihlerini getirir 
     */
    public function getBulletinDate()
    {
        $sql = "SELECT 
                    bulten_no,
                    bulten_tarihi,
                    CASE
                        WHEN bulten_tarihi::date > CURRENT_DATE THEN bulten_tarihi
                        ELSE bulten_tarihi
                    END AS bulten_tarihi_durumu
                FROM
                bulten_tarihleri
        ";

        $stmt = $this->db->prepare($sql)->execute()->fetchAllAssociative();

        return $stmt;
    }

    /**
     * yurt dışı markam
     */
    public function getYdnTrademark($id = 0)
    {
        $where = 'WHERE 1=1 AND';
        $where .= ' silinmis = false AND';

        if ($id > 0) {
            $where .= ' id = ' . $id . ' ';
        }

        $where = rtrim($where, 'AND');

        $domesticBrand = $this->db->prepare('SELECT * FROM v_trademark_yda_file  ' . $where);
        // get all
        $stmt = $domesticBrand->execute();
        $domesticBrand = $stmt->fetchAllAssociative();

        return $domesticBrand;
    }


    public function getDomesticBrand($id = 0)
    {
        $where = 'WHERE 1=1 AND';
        $where .= ' silinmis = false AND';

        if ($id > 0) {
            $where .= ' id = ' . $id . ' ';
        }

        $where = rtrim($where, 'AND');

        $domesticBrand = $this->db->prepare("SELECT * ,     
        CASE
            WHEN tescil_tarihi <= CURRENT_DATE - INTERVAL '5 years' THEN true
            ELSE false
        END AS tescil_tarihi_durumu 
        FROM v_trademark_yim_file 
    
         " . $where);
        // get all
        $stmt = $domesticBrand->execute();
        $domesticBrand = $stmt->fetchAllAssociative();

        return $domesticBrand;
    }




    /**
     * fatura bilgilerini getirir
     */
    public function findByInvoices($account_ref)
    {
        $request = new Request();
        $query = 'select * from v_transaction_for_apiz_file where ref_account = ' . $account_ref . ' 
         AND col_is_deleted = false order by col_lks_date DESC
         ';


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
     * Eşya listesini getirir
     */
    public function findByClasses($col_id)
    {
        $query = 'select * from tbl_trademark_yim_classes where ref_trademark_id = ' . $col_id . ' ';
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();

        if (count($result) > 0) {
            return new JsonResponse([
                'data' => $result,
                'message' => 'Eşya listesi başarıyla getirildi',
                'success' => true
            ], 200);
        } else {
            return new JsonResponse([
                'data' => [],
                'message' => 'Eşya listesi bulunamadı',
                'success' => false
            ], 200);
        }
    }

    /**
     * Marka Fatura Listesi
     */
    public function getInvoiceList($id)
    {

        $query = "
        select *
        FROM v_transaction_for_apiz_file where col_file_id = " . $id . " 

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
     * Marka itiraz faturalar
     */
    public function getOppInvoiceList($id)
    {



        $query = " select tpf.col_id as id, t.* FROM v_transaction_for_apiz_file t
        left join tbl_trademark_yim_opposition tpf ON tpf.col_id = t.col_file_id  
        where tpf.col_id = " . $id . " ";


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
     * İşlemleri getirir
     */
    public function findByActions($col_id)
    {
        $query = 'select 
        department,
        col_process_date,
        col_process_due_date,
        description,
        col_name,
        col_surname,
        processlaststatus
    from v_yim_file_process vyfp
        left join v_trademark_process vtp on vyfp.col_id = vtp.processid 
        left  join tbl_trademark_ydn_process_last_status ttypl on vyfp.ref_trademark_process_last_status_id = ttypl.col_id
        left join tbl_employee te on vyfp.employee_col_id = te.col_id
        left join v_department_processes vd on vtp.processid = vd.processid 
        where vyfp.ref_trademark_id = ' . $col_id . ' 
        ORDER BY col_process_due_date  DESC
        ';

        // $query = 'select * from v_yim_file_process vyfp where vyfp.ref_trademark_id = ' . $col_id . '';

        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();

        if (count($result) > 0) {
            return new JsonResponse([
                'data' => $result,
                'message' => 'İşlemler başarıyla getirildi',
                'success' => true
            ], 200);
        } else {
            return new JsonResponse([
                'data' => [],
                'message' => 'İşlemler bulunamadı',
                'success' => false
            ], 200);
        }
    }




    public function findByBrandInfo($col_id)
    {
        // ref_applicationsystem_id
        $query = 'select ttyf.*, vtyf.*, t3.col_detail as basvuru_sistemi from tbl_trademark_yim_file ttyf 
         left join  v_tm_yim_file vtyf on ttyf.col_id = vtyf.col_id
         left join  tbl_trademark_application_system t3 on t3.col_id = ttyf.ref_applicationsystem_id
         where ttyf.col_id = ' . $col_id . ' ';
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();


        if (count($result) > 0) {
            return new JsonResponse([
                'data' => $result[0],
                'message' => 'Marka bilgisi başarıyla getirildi',
                'success' => true
            ], 200);
        } else {
            return new JsonResponse([
                'data' => [],
                'message' => 'Marka bilgisi bulunamadı',
                'success' => false
            ], 200);
        }
    }


    public function getTrademarkYimFile($request)
    {
        $account_ref = $request->getSession()->get('ref_account');
        $query = 'Select * from v_tm_yim_file where ref_account = ' . $account_ref . ' AND col_is_deleted = false ';
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();

        return new JsonResponse([
            'data' => $result,
            'request' => [],
        ]);
    }

    public function getTrademarkYdaFile($request)
    {
        $account_ref = $request->getSession()->get('ref_account');
        $type = $request->query->get('type'); 

        $where = ' AND 1=1';


        if($type != null) {
            $where .= " AND col_last_status = '$type' " ;
        }

        $query = 'Select * from v_tm_yda_file where ref_account = ' . $account_ref . ' AND col_is_deleted = false
        '.$where.'
        ';
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();


        return new JsonResponse([
            'data' => $result,
            'request' => [],
        ]);
    }

    public function pagination($query, $pageCount = 1, $whereStmt = '')
    {
        $offset = ($pageCount - 1) * $this->limit;
        $query .= $whereStmt . ' LIMIT ' . $this->limit . ' OFFSET ' . $offset;
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();
        return $result;
    }

    public function getQueryCount($query)
    {
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();
        return $result;
    }

    /**
     * Marka İtirazları
     */
    public function getOppositionList($request)
    {

        $account_ref = $request->getSession()->get('ref_account');
        $query = "SELECT * FROM v_tm_yim_opp 
        WHERE ref_account =  $account_ref ";



        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();




        if (count($result) > 0) {
            return new JsonResponse([
                'data' => $result,
                'message' => 'İtirazlar başarıyla getirildi',
                'success' => true,
            ], 200);
        } else {
            return new JsonResponse([
                'data' => [],
                'message' => 'İtirazlar bulunamadı',
                'success' => false
            ], 200);
        }
    }

    /**
     * Marka itiraz detay işlemler
     */
    public function findByYimOppActions($col_id)
    {
        $query = '
        select *, t2.col_detail as son_durum,
        t1.col_is_deleted as is_deleted
        from tbl_trademark_yim_opposition_process t1
        left join tbl_trademark_yim_opposition_process_last_status t2 on t2.col_id = t1.ref_trademark_opposition_process_last_status_id
        left join tbl_f_feelist t3 on t3.col_id = t1.ref_feelist
        where ref_trademark_opposition_id = ' . $col_id . ' and t1.col_is_deleted is not null order by t1.col_process_date DESC
        ';


        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();

        if (count($result) > 0) {
            return new JsonResponse([
                'data' => $result,
                'message' => 'İşlemler başarıyla getirildi2',
                'success' => true
            ], 200);
        } else {
            return new JsonResponse([
                'data' => [],
                'message' => 'İşlemler bulunamadı',
                'success' => false
            ], 200);
        }
    }
    /**
     * Başvuru itiraz modola tıklayınca gelen bilgiler 
     */
    public function getOppositionDetail($request)
    {

        $col_id = $request->query->get('col_id');

        $query = "select * from v_tm_yim_opp
         where col_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute([
            $col_id
        ]);


        $result = $stmt->fetchAllAssociative();

        $itirazEdilecekMarka = 'select t2.col_id as marka_id from tbl_trademark_yim_groundfilenumber t1 
        left join tbl_trademark_yim_file t2 on t2.col_application_number = t1.col_application_number
        where t1.ref_trademarkopposition_id = ' . $col_id . ' ';

        $itirazEdilecekMarkaStmt = $this->db->prepare($itirazEdilecekMarka)->execute()->fetchAssociative();





        if (count($result) > 0) {
            return new JsonResponse([
                'data' => $result[0],
                'itiraz_edilecek_marka_id' => $itirazEdilecekMarkaStmt['marka_id'],
                'message' => 'İtiraz bilgisi başarıyla getirildi',
                'success' => true
            ], 200);
        } else {
            return new JsonResponse([
                'data' => [],
                'message' => 'İtiraz bilgisi bulunamadı',
                'success' => false,
                'col_id' => $col_id
            ], 200);
        }
    }

    /**
     * Marka itiriaz eşya listesi
     */
    public function getOppositionGoods($request)
    {

        $params = $request->query->all();
        $col_id = $params['col_id'] ?? 0;

        $sql = 'select * from tbl_trademark_yim_opposition_classes where ref_opposition_id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt = $stmt->execute([
            $col_id
        ]);

        $result = $stmt->fetchAllAssociative();
        $tempData = [];

        if (count($result) > 0) {
            foreach ($result as $key => $value) {
                $col_class_code = trim($value['col_class_code']);
                if (empty($tempData[$col_class_code])) {
                    $tempData[$col_class_code] =  $value['col_goods_text'];
                } else {
                    $tempData[$col_class_code] .= $value['col_goods_text'];
                }
            }
            return new JsonResponse([
                'data' => $tempData,
                'message' => 'Eşya listesi başarıyla getirildi',
                'success' => true
            ], 200);
        } else {
            return new JsonResponse([
                'data' => [],
                'message' => 'Eşya listesi bulunamadı',
                'success' => false
            ], 200);
        }
    }



    /**
     * YurtDışı marka bilgileri
     */

    public function findByYDABrandInfo($col_id)
    {
        $query = 'select ttyf.*, vtyf.* from tbl_trademark_yda_file ttyf 
          left join  v_tm_yda_file vtyf on ttyf.col_id = vtyf.col_id
          where ttyf.col_id = ' . $col_id . ' ';
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();



        if (count($result) > 0) {
            return new JsonResponse([
                'data' => $result[0],
                'message' => 'Marka bilgisi başarıyla getirildi',
                'success' => true
            ], 200);
        } else {
            return new JsonResponse([
                'data' => [],
                'message' => 'Marka bilgisi bulunamadı',
                'success' => false
            ], 200);
        }
    }


    /**
     * Yurtdışı Başvuru dosyaları
     */

    public function domesticAppliactionFiles($id)
    {
        $sql = '
        select * from tbl_trademark_yda_file_document_content t1
        left join tbl_trademark_yda_file_document t2 on t1.ref_document_id = t2.col_id 
        where t1.ref_trademark_file_id  = :id and t1.ref_document_id is not null 
        ';
        $stmt = $this->db->prepare($sql);
        $stmt =  $stmt->execute(['id' => $id]);
        $result = $stmt->fetchAllAssociative();

        if (count($result) > 0) {
            return new JsonResponse([
                'status' => true,
                'message' => 'Dosyalar başarıyla getirildi',
                'data' => $result
            ]);
        } else {
            return new JsonResponse([
                'status' => false,
                'message' => 'Dosya bulunamadı',
                'data' => null
            ]);
        }
    }

    /**
     * Yurtdışı Eşya listesini getirir
     */
    public function findByYDAClasses($col_id)
    {
        $query = 'select * from tbl_trademark_yda_classes where ref_trademark_id = ' . $col_id . ' ';
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();

        if (count($result) > 0) {
            return new JsonResponse([
                'data' => $result,
                'message' => 'Eşya listesi başarıyla getirildi',
                'success' => true
            ], 200);
        } else {
            return new JsonResponse([
                'data' => [],
                'message' => 'Eşya listesi bulunamadı',
                'success' => false
            ], 200);
        }
    }
    /**
     * YURT DIŞI İşlemleri getirir
     */
    public function findByYDAActions($col_id)
    {
        $query = '
        select *, t2.col_detail as son_durum,
        t1.col_is_deleted as is_deleted
        from tbl_trademark_yda_process t1
        left join tbl_trademark_yda_process_last_status t2 on t2.col_id = t1.ref_trademark_process_last_status_id
        left join tbl_f_feelist t3 on t3.col_id = t1.ref_feelist
        where ref_trademark_id = ' . $col_id . ' and t1.col_is_deleted is not null order by t1.col_process_date DESC
        ';


        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();

        if (count($result) > 0) {
            return new JsonResponse([
                'data' => $result,
                'message' => 'İşlemler başarıyla getirildi2',
                'success' => true
            ], 200);
        } else {
            return new JsonResponse([
                'data' => [],
                'message' => 'İşlemler bulunamadı',
                'success' => false
            ], 200);
        }
    }

    /**
     * Yurt Dışı itiraz listesi
     */

    public function getYDAOppList($request)
    {
        $account_ref = $request->getSession()->get('ref_account');
        $query = 'select * from v_tm_yda_opp where ref_account = ' . $account_ref . ' AND col_is_deleted is not null';

        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();

        $result = $stmt->fetchAllAssociative();

        if (count($result) > 0) {
            return new JsonResponse([
                'data' => $result,
                'message' => 'İtirazlar başarıyla getirildi',
                'success' => true,
            ], 200);
        } else {
            return new JsonResponse([
                'data' => [],
                'message' => 'İtirazlar bulunamadı',
                'success' => false
            ], 200);
        }
    }

    /**
     * Yurtdışı itiraz info
     */
    public function getYDAOppositionDetail($request)
    {

        $col_id = $request->query->get('col_id');

        $query = "
        select t1.*, t2.col_account_referance_number as firma_referans_numarasi,t2.col_application_date as basvuru_tarihi, t2.col_publication_date as yayin_tarihi, t3.col_detail as basvuru_sistemi from v_tm_yda_opp t1 
        left join tbl_trademark_yda_opposition t2 on t1.col_id  = t2.col_id 
        left join tbl_trademark_application_system t3 on t3.col_id = t2.ref_applicationsystem  
        where t2.col_id  = ?
        ";
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute([
            $col_id
        ]);

        $dayandirilanMarkaSql = "select * from tbl_trademark_yda_opposition_based_on where ref_opposition_id = :col_id";
        $dayandirilanMarkaStmt = $this->db->prepare($dayandirilanMarkaSql)->execute(['col_id' => $col_id])->fetchAllAssociative();


        $result = $stmt->fetchAllAssociative();

        if (count($result) > 0) {
            return new JsonResponse([
                'data' => $result[0],
                "dayandirilan_markalar" => $dayandirilanMarkaStmt,
                'message' => 'İtiraz bilgisi başarıyla getirildi',
                'success' => true
            ], 200);
        } else {
            return new JsonResponse([
                'data' => [],
                'message' => 'İtiraz bilgisi bulunamadı',
                'success' => false,
                'col_id' => $col_id
            ], 200);
        }
    }

    /**
     * yurtdışı itiraz eşya listesi
     */
    public function getYDAOppGoods($request)
    {

        $params = $request->query->all();
        $col_id = $params['col_id'] ?? 0;

        $sql = 'select * from tbl_trademark_yda_opposition_classes where ref_opposition_id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt = $stmt->execute([
            $col_id
        ]);

        $result = $stmt->fetchAllAssociative();
        $tempData = [];

        if (count($result) > 0) {
            foreach ($result as $key => $value) {
                $col_class_code = trim($value['col_class_code']);
                if (empty($tempData[$col_class_code])) {
                    $tempData[$col_class_code] =  $value['col_goods_text'];
                } else {
                    $tempData[$col_class_code] .= $value['col_goods_text'];
                }
            }
            return new JsonResponse([
                'data' => $tempData,
                'message' => 'Eşya listesi başarıyla getirildi',
                'success' => true
            ], 200);
        } else {
            return new JsonResponse([
                'data' => [],
                'message' => 'Eşya listesi bulunamadı',
                'success' => false
            ], 200);
        }
    }

    /**
     * Rüçhan listesi
     */

    public function getOppositionPriority($trademark_id)
    {
        $query = 'select t1.*, t2.col_name as ulke  from tbl_trademark_yda_priority t1
        left join tbl_country t2 on t2.col_id = t1.country_col_id
        
        where t1.ref_trademark_id = ' . $trademark_id . ' ';
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
     * Yurtdışı itiraz işlemler
     */

    /**
     * Yurtdışı İtiraz Dosyaları
     */
    public function domesticOppAppliactionFiles($id)
    {
        $sql = '
         select * from tbl_trademark_yda_opposition_document_content t1
         left join tbl_trademark_yda_opposition_document t2 on t1.ref_document_id = t2.col_id 
         where t1.ref_trademark_opposition_id  = :id AND 
         t1.ref_document_id is not null
         ';
        $stmt = $this->db->prepare($sql);
        $stmt =  $stmt->execute(['id' => $id]);
        $result = $stmt->fetchAllAssociative();

        if (count($result) > 0) {
            return new JsonResponse([
                'status' => true,
                'message' => 'Dosyalar başarıyla getirildi',
                'data' => $result
            ]);
        } else {
            return new JsonResponse([
                'status' => false,
                'message' => 'Dosya bulunamadı',
                'data' => null
            ]);
        }
    }
    /**
     * YURT DIŞI İşlemleri getirir
     */
    public function findByYDAOppActions($col_id)
    {
        $query = '
        select *, t2.col_detail as son_durum,
        t1.col_is_deleted as is_deleted
        from tbl_trademark_yda_opposition_process t1
        left join tbl_trademark_yda_opposition_process_last_status t2 on t2.col_id = t1.ref_trademark_opposition_process_last_status_id
        left join tbl_f_feelist t3 on t3.col_id = t1.ref_feelist
        where ref_trademark_opposition_id = ' . $col_id . ' and t1.col_is_deleted is not null order by t1.col_process_date DESC
        ';


        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();

        if (count($result) > 0) {
            return new JsonResponse([
                'data' => $result,
                'message' => 'İşlemler başarıyla getirildi2',
                'success' => true
            ], 200);
        } else {
            return new JsonResponse([
                'data' => [],
                'message' => 'İşlemler bulunamadı',
                'success' => false
            ], 200);
        }
    }


    /**
     * ----------------- YDA AND YIM MARKA İŞLEMLERİ -----------------
     */


     // Kullanıcının tüm marklarını getir 

     public function yda_and_yim_get_all_trademark_yim_file($request) {

        try {
            $account_ref = $request->getSession()->get('ref_account');
            $query = "
            SELECT t1.*
            FROM v_tm_yim_file t1
            WHERE t1.ref_account = $account_ref  AND t1.col_is_deleted = false  AND t1.col_last_status != 'Red'
            ";
    
            $stmt = $this->db->prepare($query)->execute()->fetchAllAssociative();
         
            $data = [];
    
            foreach ($stmt as $key => $value) {
                $classString = str_replace(",", "-", $value['col_class_string']);
                $classString = explode("-", $classString);
                $classString = array_map(function($item) {
                return intval($item);
                }, $classString);
                $classString = implode("-", $classString);

                // sonunda soru işareti varsa sil 
                $trademark = $value['col_trademark'];
                $trademark = rtrim($trademark, '?');

    
                //  let url = `/company/observation/${classString}/${trademark}/${id}?type=${item.type}`;
                $url = '/company/observation/'.$classString.'/' . $trademark . '/' . $value['col_id'] . '?type=1';
    
                $data[] = [
                    'order' => $key + 1,
                    'trademark' => $trademark,
                    'id' => $value['col_id'],
                    'url' => $url,
                    'classString' => $classString
                ];
            }
    
            return new JsonResponse([
                'data' => $data,
                'message' => "Markalar başarıyla getirildi",
                'success' => true
            ]);
        }catch(\Exception $e) {
            return new JsonResponse([
                'data' => [],
                'message' => 'Markalar getirilirken bir hata oluştu',
                'success' => false,
                'error' => $e->getMessage()
            ], 200);
        }
      
     }

    

    public function yda_and_yim_getTrademarkYimFile($request, $secili_bulten_no)
    {
        $bulletionNo = $request->query->get('bulten') == '' ?  $secili_bulten_no : $request->query->get('bulten');
 
        $account_ref = $request->getSession()->get('ref_account');
        $type = $request->query->get('type');
        // get attribute
        $gozlem_durumu = $request->query->get('gozlem_durumu');

        $where = ' AND 1=1 ';

        /**
         * 1 = Gözlemi yapılmamış markalar
         * 2 = Gözlemi yapılmış markalar
         * 3 = Aktif Markalar
         * 4 = Pasif Markalar
         */


        if($type == 0 && $gozlem_durumu == '') {
            // Gözlemi yapılmamış markalar
            // $where = ' AND t2.bulletinno is null ';
        }
        else if($type == 1 && $gozlem_durumu == '') {
            $where .= ' AND t2.bulletinno is null ';
        } else if ($type == 2 && $gozlem_durumu == '') {
            $where .= ' AND t2.bulletinno is not null ';
        } else if ($type == 3 && $gozlem_durumu == '') {
            $where .= ' AND t3.status is not null ';
        } else if ($type == 4 && $gozlem_durumu == '') {
            $where .= ' AND t3.status is null ';
        } else if ($type == 1 && $gozlem_durumu == 3) {
            $where .= ' AND t2.bulletinno is null AND t3.status is not null ';
        } else if ($type == 1 && $gozlem_durumu == 4) {
            $where .= ' AND t2.bulletinno is null AND t3.status is null ';
        } else if ($type == 2 && $gozlem_durumu == 3) {
            // Gözlemi yapılmış markalar ve aktif markalar
            $where .= ' AND t2.bulletinno is not null AND t3.status is not null ';
        } else if ($type == 2 && $gozlem_durumu == 4) {
            // Gözlemi yapılmış markalar ve pasif markalar
            $where .= ' AND t2.bulletinno is not null AND t3.status is null ';
        }  else if($gozlem_durumu == 3) {
            $where .= ' AND t3.status is not null ';
        } else if($gozlem_durumu == 4) {
            $where .= ' AND t3.status is null ';
        }

        // Listede gözükmemesi gerekn markalar 
        // conver array to string

        $where .= ' AND t1.col_id not in (' . TrademarkFilter::getTradeMarkList() . ') ';
     

        $query = "
        SELECT t1.*, t2.bulletinno as bulten_numarasi, t3.status as gozlem_durumu,
            CASE
            WHEN t1.col_registration_date <= CURRENT_DATE - INTERVAL '5 years' THEN true
            ELSE false
            END AS tescil_tarihi_durumu
        FROM v_tm_yim_file t1
        LEFT JOIN observationcachemainlist t2 ON t2.bulletinno = '$bulletionNo' AND cast( t2.trademark_id as int) = t1.col_id
        LEFT JOIN trademark_observation_status t3 ON t3.trademark_id = t1.col_id
        WHERE t1.ref_account = $account_ref  AND t1.col_is_deleted = false  AND t1.col_last_status != 'Red'  $where ORDER BY t1.col_registration_date DESC
        ";

   
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $yim = $stmt->fetchAllAssociative();
    

        foreach ($yim as $key => $value) {
            $yim[$key]['type'] = 1;
        }

        // $query = "Select * from v_tm_yda_file 
        // where ref_account = $account_ref 
        // AND col_is_deleted = false 
        // ANd col_last_status != 'Red'
        // ";
        // $stmt = $this->db->prepare($query);
        // $stmt = $stmt->execute();
        // $yda = $stmt->fetchAllAssociative();

        // foreach ($yda as $key => $value) {
        //     $yda[$key]['type'] = 2;
        // }

        $yda = [];

        $result = array_merge($yim, $yda);


        return new JsonResponse([
            'data' => $result,
            'request' => [],
        ]);
    }


    /**
     * Tüm Markalar 
     */
    public function yda_and_yim_getTrademarkYimFileAll($request, $secili_bulten_no)
    {
        $account_ref = $request->getSession()->get('ref_account');
        $type = $request->query->get('type');

        
        $where = ' AND 1=1 ';

        if($type != null) {
            $where .= " AND t1.col_last_status = '$type' ";
        }

 
        $query = "
        SELECT t1.*,
            CASE
            WHEN t1.col_registration_date <= CURRENT_DATE - INTERVAL '5 years' THEN true
            ELSE false
            END AS tescil_tarihi_durumu
        FROM v_tm_yim_file t1
        WHERE t1.ref_account = $account_ref  AND t1.col_is_deleted = false  
        $where 
        ORDER BY t1.col_registration_date DESC
        ";

   
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $yim = $stmt->fetchAllAssociative();
        

        foreach ($yim as $key => $value) {
            $yim[$key]['type'] = 1;
        }

      

        $yda = [];

        $result = array_merge($yim, $yda);


        return new JsonResponse([
            'data' => $result,
            'request' => [],
        ]);
    }


    /*
        Marka Bilgisi
    */

    public function markaBilgim($col_id)
    {

        $yda = $this->db->prepare('SELECT 
        col_account_title,
        col_trademark,
        col_application_number
        FROM v_tm_yda_file WHERE col_id = ' . $col_id . ' ')
            ->execute()
            ->fetchAllAssociative();

        if (count($yda) > 0) {
            return $yda[0];
        }

        $yim = $this->db->prepare('SELECT
        col_account_title,
        col_trademark,
        col_application_number
        FROM v_tm_yim_file WHERE col_id = ' . $col_id . ' ')
            ->execute()
            ->fetchAllAssociative();


        if (count($yim) > 0) {
            return $yim[0];
        }
    }

    /***
     * Markanın gözlem durumunu aktif pasif yapma
     */
    public function observationStatus($col_id) {
            // Eğer hiç oluşamamış ise 
            $query = "SELECT * FROM trademark_observation_status WHERE trademark_id = :trademark_id";
            $gozlem_durumu = $this->db->prepare($query)->execute([
                'trademark_id' => $col_id
            ])->fetchAllAssociative();
          

            if(count($gozlem_durumu) == 0) {
                // insert
                $query = "INSERT INTO trademark_observation_status (trademark_id, status) VALUES (:trademark_id, :status)";
                $stmt = $this->db->prepare($query)->execute([
                    'trademark_id' => $col_id,
                    'status' => true
                ]);

                return new JsonResponse([
                    'status' => true,
                    'message' => 'Gözlem durumu başarıyla güncellendi',
                    'durum'  => 1
                ]);
               
            } else {
                // delete
                $query = "DELETE FROM trademark_observation_status WHERE trademark_id = :trademark_id";
                $stmt = $this->db->prepare($query)->execute([
                    'trademark_id' => $col_id
                ]);

                return new JsonResponse([
                    'status' => true,
                    'message' => 'Gözlem durumu başarıyla güncellendi',
                    'durum'  => 0
                ]);

            }

         
    }




    //    public function getTrademark($tableData)
    //    {
    //        // DataTable parametreleri
    //        $this->logo = $this->getQueryParams($tableData, 0);
    //        $this->marka = trim(mb_strtolower($this->getQueryParams($tableData, 1), 'UTF-8'));
    //        $this->sinif = trim($this->getQueryParams($tableData, 2));
    //        $this->dosyaNo = trim($this->getQueryParams($tableData, 3));
    //        $this->basvuruNo = trim($this->getQueryParams($tableData, 4));
    //        $this->basvuruTarihi = trim($this->getQueryParams($tableData, 5));
    //        $this->tescilNo = trim($this->getQueryParams($tableData, 6));
    //        $this->yenilemeTarihi = trim($this->getQueryParams($tableData, 7));
    //        $this->dosyaSonDurum = trim($this->getQueryParams($tableData, 8));
    //        $this->sayfaNumarasi = trim(intval($tableData['start'] / 10 + 1));
    //        $this->limit = trim($tableData['length']) ?? 10;
    //
    //
    //        // user id
    //
    //        $user_col_id = $this->security->getUser()->getColId();
    //
    //        // where
    //        $whereCase = '';
    //        if ($this->marka) {
    //            // trademark
    //            $whereCase .= " AND LOWER(trademark) LIKE '%" . $this->marka . "%'";
    //        }
    //        if ($this->dosyaNo) {
    //            // trademark
    //            $whereCase .= " AND file_number LIKE '%" . $this->dosyaNo . "%'";
    //        }
    //        if ($this->basvuruTarihi) {
    //            // trademark
    //            $whereCase .= " AND application_date LIKE '%" . $this->basvuruTarihi . "%'";
    //        }
    //        if ($this->sinif) {
    //            // trademark
    //            $whereCase .= " AND class LIKE '%" . $this->sinif . "%'";
    //        }
    //
    //
    //        //        $_query = 'select * from tbl_user_account tua where tua.ref_user_id = ' . $user_col_id . '  ';
    //        //        $stmt = $this->db->prepare($_query);
    //        //        $stmt = $stmt->execute();
    //        //        $result = $stmt->fetchAllAssociative();
    //
    //
    //
    //        // Pagination
    //        $query = 'select  vt.* as trademark_col_id
    //        from tbl_employee te
    //        inner join  tbl_trademark_yim_file ttyf ON te.col_id = ttyf.ref_created_by
    //        inner join  v_trademark vt on ttyf.col_application_number = vt.application_number
    //        where vt.is_deleted = false AND te.col_id = ' . $user_col_id . '
    //        ';
    //        $queryTrademarkCount = '
    //        select  COUNT(*) as trademarkcount
    //        from tbl_employee te
    //        inner join  tbl_trademark_yim_file ttyf ON te.col_id = ttyf.ref_created_by
    //        inner join  v_trademark vt on ttyf.col_application_number = vt.application_number
    //        where vt.is_deleted = false AND te.col_id = ' . $user_col_id . '
    //        ';
    //        $tradeMark = $this->pagination($query, $this->sayfaNumarasi, $whereCase);
    //        // Toplam sonuç sayısı
    //        $this->totalCount = $this->trademakCount($queryTrademarkCount, $this->sayfaNumarasi, $whereCase)[0]['trademarkcount'] ?? 0;
    //        // Filtrelenmiş sonuç sayısı
    //        $this->filteredCount = $this->totalCount;
    //
    //
    //
    //        return new JsonResponse([
    //            'draw' => intval($tableData['draw']),
    //            'recordsTotal' => intval($this->totalCount),
    //            'recordsFiltered' => intval($this->totalCount),
    //            'data' => $tradeMark,
    //            'request' => $tableData,
    //            'sayfaNumarasi' => $this->sayfaNumarasi,
    //            'columns' => [
    //                'logo' => $this->logo,
    //                'trademark' =>   $this->totalCount,
    //                'employe_id' => $user_col_id,
    //
    //            ],
    //
    //        ]);
    //    }

    /**
     * table içinde array olan parametreleri döndürür
     */
    //    public function getQueryParams($tableData, $index)
    //    {
    //        if (!isset($tableData['columns'][$index])) {
    //            return false;
    //        }
    //        $columns = preg_replace('/\(\(\(\(/', '', $tableData['columns'][$index]['search']['value']);
    //        $columns = preg_replace('/\)\)\)\)/', '', $columns);
    //
    //        return $columns;
    //    }

    //    public function trademakCount($query, $pageCount = 1, $whereStmt = '')
    //    {
    //        $query .= $whereStmt;
    //        $stmt = $this->db->prepare($query);
    //        $stmt = $stmt->execute();
    //        $result = $stmt->fetchAllAssociative();
    //        return $result;
    //    }

    //    public function getTrademarkTotalCount()
    //    {
    //        $query = 'SELECT COUNT(*) as trademarkCount FROM v_trademark WHERE is_deleted = false';
    //        $stmt = $this->db->prepare($query);
    //        $stmt = $stmt->execute();
    //        $result = $stmt->fetchAllAssociative();
    //        return $result;
    //    }
}
