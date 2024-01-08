<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

class AutoObservationService
{
    private  $db;
    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->db = $this->registry->getConnection();
    }


    /**
     * Firmanın yurtiçi ve yurtdışı markalarını getirir
     * @param $id int
     * @param $is_foreign_company bool
     */
    public function getTrademarkList($id)
    {
        $trademarkList = [];

        /**
         * --------------------------- YIM MARKA LİSTESİ ---------------------------
         */
        $trademarkList = $this->db->prepare('SELECT * FROM tbl_trademark_yim_file WHERE ref_account_id = :id
            AND  col_is_deleted is not true  AND  col_watch_status is true')
            ->execute(['id' => $id])
            ->fetchAllAssociative();
        /**
         * Tüm elemanlarına yim_marka mı diye bir key ekliyoruz
         */
        foreach ($trademarkList as $key => $value) {
            $trademarkList[$key]['yim_marka'] = true;
        }

        /**
         * --------------------------- YDA MARKA LİSTESİ ---------------------------
         */

        $yda_trademarkList = [];

        $yda_trademarkList = $this->db->prepare("SELECT * FROM tbl_trademark_yda_file 
                WHERE ref_account_id = :id 
                AND  
                    col_is_deleted is not true
                AND 
                    col_trademark != 'Genel Dosya'
                ")
            ->execute(['id' => $id])
            ->fetchAllAssociative();

        // tüm elemanlarına yda_marka mı diye bir key ekliyoruz
        foreach ($yda_trademarkList as $key => $value) {
            $yda_trademarkList[$key]['yda_marka'] = true;
        }


        /**
         * yim ve yda markalarını birleştir
         */
        $concatTrademarkList = array_merge($trademarkList, $yda_trademarkList);

        return $concatTrademarkList;
    }

    /**
     * Otomatik gözlem listesini getirir 
     */

    public function getAutoObservationList()
    {
        $result = $this->db->prepare('SELECT * FROM tbl_automatic_observation ')
            ->execute()
            ->fetchAllAssociative();


        if (count($result) > 0) {
            return  $result;
        } else {
            return [];
        }
    }

    /**
     * Gözlem Sonucu Oluştur
     */
    public function createObservationResult()
    {
        $observationResults = [];
        // gözlemi bitmiş firmalar [1]
        $finishedObservationList = $this->getFinishedObservationList();

        // Firmaları döngüye al  [2]
        foreach ($finishedObservationList as $key => $account) {
            // Örnek firma 
            // [3]
            $companyTrademarkList = $this->getCompanyTrademarkList($account['ref_account_id']);
            /**
             * Firmanın gözlemi bitmiş markaları
             */
            foreach ($companyTrademarkList as $key => $trademark) {

                $observationResult = $this->getObservationResult($trademark['id']);
                /**
                 * ref_account_id
                 *  -> trademark_id
                 *      -> gözlem sonucları
                 */
                $observationResults[$account['ref_account_id']][$trademark['trademark_id'] . '/' . $trademark['searchedword']] = $observationResult;
            }
        }

        dd($observationResults);
    }


    /**
     * Gözlemi bitmiş firmalari getir
     */
    public function getFinishedObservationList()
    {
        // TODO: bulletinNo dinamik olarak gelecek
        $bulletinNo = 427;

        $result = $this->db->prepare("SELECT distinct ref_account_id FROM observationcachemainlist where bulletinno =  '$bulletinNo' ")
            ->execute()
            ->fetchAllAssociative();

        if (count($result) > 0) {
            return  $result;
        } else {
            return [];
        }
    }

    /**
     * Firmanın Markalarını bul
     */
    public function getCompanyTrademarkList($id)
    {
        $result = $this->db->prepare("SELECT * FROM observationcachemainlist WHERE ref_account_id = :id ")
            ->execute(['id' => $id])
            ->fetchAllAssociative();

        if (count($result) > 0) {
            return  $result;
        } else {
            return [];
        }
    }


    /**
     * Markayla Eşleşen Gözlem Sonuçlarını Getir
     */

    public function getObservationResult($observationcachemainlist_id)
    {
        $result = $this->db->prepare("SELECT * FROM observationcache WHERE observationcachemainlist_id = :observationcachemainlist_id")
            ->execute(['observationcachemainlist_id' => $observationcachemainlist_id])
            ->fetchAllAssociative();

        if (count($result) > 0) {
            return  $result;
        } else {
            return [];
        }
    }
}
