<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

class DomesticBrandService
{
    private  $db;

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



    public function __construct(private ManagerRegistry $registry)
    {
        $this->db = $this->registry->getConnection('apz');
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

    public function getTrademarkTotalCount()
    {
        $query = 'SELECT COUNT(*) as trademarkCount FROM v_trademark WHERE is_deleted = false';
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();
        return $result;
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

    public function trademakCount($query, $pageCount = 1, $whereStmt = '')
    {
        $query .= $whereStmt;
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();
        return $result;
    }

    public function getTrademark($tableData)
    {
        // DataTable parametreleri 
        $this->logo = $this->getQueryParams($tableData, 0);
        $this->marka = trim(mb_strtolower($this->getQueryParams($tableData, 1), 'UTF-8'));
        $this->sinif = trim($this->getQueryParams($tableData, 2));
        $this->dosyaNo = trim($this->getQueryParams($tableData, 3));
        $this->basvuruNo = trim($this->getQueryParams($tableData, 4));
        $this->basvuruTarihi = trim($this->getQueryParams($tableData, 5));
        $this->tescilNo = trim($this->getQueryParams($tableData, 6));
        $this->yenilemeTarihi = trim($this->getQueryParams($tableData, 7));
        $this->dosyaSonDurum = trim($this->getQueryParams($tableData, 8));
        $this->sayfaNumarasi = trim(intval($tableData['start'] / 10 + 1));
        $this->limit = trim($tableData['length']) ?? 10;




        // where 
        $whereCase = '';
        if ($this->marka) {
            // trademark
            $whereCase .= " AND LOWER(trademark) LIKE '%" . $this->marka . "%'";
        }
        if ($this->dosyaNo) {
            // trademark
            $whereCase .= " AND file_number LIKE '%" . $this->dosyaNo . "%'";
        }
        if ($this->basvuruTarihi) {
            // trademark
            $whereCase .= " AND application_date LIKE '%" . $this->basvuruTarihi . "%'";
        }
        if ($this->sinif) {
            // trademark
            $whereCase .= " AND class LIKE '%" . $this->sinif . "%'";
        }


        // Pagination 
        $query = 'SELECT * FROM v_trademark WHERE is_deleted = false';
        $queryTrademarkCount = 'SELECT COUNT(*) as trademarkCount FROM v_trademark WHERE is_deleted = false';
        $tradeMark = $this->pagination($query, $this->sayfaNumarasi, $whereCase);
        // Toplam sonuç sayısı
        $this->totalCount = $this->trademakCount($queryTrademarkCount, $this->sayfaNumarasi, $whereCase)[0]['trademarkcount'] ?? 0;
        // Filtrelenmiş sonuç sayısı
        $this->filteredCount = $this->totalCount;



        return new JsonResponse([
            'draw' => intval($tableData['draw']),
            'recordsTotal' => intval($this->totalCount),
            'recordsFiltered' => intval($this->totalCount),
            'data' => $tradeMark,
            'request' => $tableData,
            'sayfaNumarasi' => $this->sayfaNumarasi,
            'columns' => [
                'logo' => $this->logo,
                'trademark' =>   $this->totalCount,
            ],

        ]);
    }

    /**
     * table içinde array olan parametreleri döndürür
     */
    public function getQueryParams($tableData, $index)
    {
        if (!isset($tableData['columns'][$index])) {
            return false;
        }
        $columns = preg_replace('/\(\(\(\(/', '', $tableData['columns'][$index]['search']['value']);
        $columns = preg_replace('/\)\)\)\)/', '', $columns);

        return $columns;
    }
}
