<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
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
    private $limit = 20;
    private $filteredCount;


    public function __construct(private ManagerRegistry $registry, private Security $security)
    {
        $this->db = $this->registry->getConnection('apz');
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

        $domesticBrand = $this->db->prepare('SELECT * FROM v_trademark_ydn_file  ' . $where . ' LIMIT 50');
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

        $domesticBrand = $this->db->prepare('SELECT * FROM v_trademark_yim_file  ' . $where . ' LIMIT 50');
        // get all
        $stmt = $domesticBrand->execute();
        $domesticBrand = $stmt->fetchAllAssociative();

        return $domesticBrand;
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

    /**
     * fatura bilgilerini getirir
     */
    public function findByInvoices($account_ref)
    {
//        tfai.ref_invoice_cost_currency
        $request = new Request();
        $query = 'select * from tbl_user_account tua 
            left join tbl_f_invoice tfi on tfi.ref_int_transaction_account  = tua.ref_account 
            left join tbl_f_awaiting_invoice f on f.ref_int_transaction_account  = tua.ref_account 
            where tua.ref_account = '.$account_ref.' AND f.col_is_deleted = false limit 5 
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
        $query = 'select ttyf.*, vtyf.* from tbl_trademark_yim_file ttyf 
         inner join  v_tm_yim_file vtyf on ttyf.col_id = vtyf.col_id
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

    public function getTrademarkYdnFile($request)
    {
        $account_ref = $request->getSession()->get('ref_account');
        $query = 'Select * from v_tm_ydn_file where ref_account = ' . $account_ref . ' AND col_is_deleted = false ';
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();

        return new JsonResponse([
            'data' => $result,
            'request' => [],
        ]);
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

