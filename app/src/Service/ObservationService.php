<?php

namespace  App\Service;

use App\Entity\Enum\Observation;
use App\Repository\ObservationCacheMainListRepository;
use App\Repository\ObservationCacheRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @description : Gözlem listesi için gerekl olan servis isteklerini yapan sınıf
 */
class ObservationService
{
    private $db;
    public function __construct(
        private HttpClientInterface $client,
        private ObservationCacheRepository $observationCacheRepository,
        private  ObservationCacheMainListRepository $observationCacheMainListRepository,
        private ManagerRegistry $managerRegistry
    ) {
        $this->db = $this->managerRegistry->getConnection();
    }

    // Get Token
    public function getToken()
    {
        $url = Observation::GET_TOKEN_URL;
        $data = Observation::TOKEN_DATA;
        $method = Observation::GET_TOKEN_METHOD;

        $response = $this->client->request(
            $method,
            $url,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode($data),
            ]
        );
        return $response->toArray()['token'];
    }

    public function getTrademarkById($id)
    {
        $sql = "SELECT col_trademark FROM tbl_trademark_yim_file WHERE col_id = $id";
        $result = $this->db->prepare($sql)->execute()
            ->fetchAssociative();
        return $result['col_trademark'];
    }

    // Get Observation List
    public  function getObservationList($data)
    {
        $response = [];
        $url = Observation::RESEARCH_SERVICE_URL;
        $method = Observation::RESEARCH_SERVICE_METHOD;
        $token = $this->getToken();


        /**
         * Gözlem daha önce aranmış mı ?
         */
        $searchedWord = $data['TrademarkName'];
        $niceClasses = $data['NiceClasses'];
        $bulletinNo = $data['BulletinNo'];

        $exists = $this->observationCacheMainListRepository->existsObservationCacheMain($searchedWord, $niceClasses, $bulletinNo, $data);


        /**
         * Bu gözlem daha önceden aranmamış ise sonuçları kendi veritabanımıza kaydet
         */
        if (!$exists) {
            $response = $this->client->request(
                $method,
                $url,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type' => 'application/json',
                    ],
                    'body' => json_encode($data),
                ],

            );
            $response = $response->toArray();



            /**
             * Gözlem daha önce aranmış ise arama sonucunu kaydeder
             */
            $this->observationCacheMainListRepository->insertObservatinCacheMain($data, $response);
        } else {


            foreach ($exists->getObservationCache() as $item) {

                /**
                 * Gözlemi yapılmış markaları belirle
                 */
                $_siniflar = $item->getNiceClasses();
                $_application_no = $item->getApplicationNo();
                $_bulletinNo = $item->getBulletinNo();
                $str = $item->getTrademarkName();
                // eğer içinde tek tırnak varsa sil
                $_trademarkName =  str_replace("'", "", $str);
                $sql = "SELECT * FROM tbl_n_trademark_objections 
                WHERE sinif = '$_siniflar' 
                AND application_no = '$_application_no'
                AND itiraz_edilen_marka_adi LIKE  '%$_trademarkName%'
                AND marka_adi = '$searchedWord'
                ";

                $_result = $this->db->prepare($sql)->execute()->fetchAllAssociative();


                $nerde_kaldim_sql = "SELECT * FROM nerde_kaldim 
                WHERE sinif = '$_siniflar' 
                AND application_no = '$_application_no'
                AND itiraz_edilen_marka_adi LIKE  '%$_trademarkName%'
                AND marka_adi = '$searchedWord'
                ";

                $nerde_kaldim_result = $this->db->prepare($nerde_kaldim_sql)->execute()->fetchAllAssociative();

                $response[] = [
                    'dataSource' => $item->getDataSource(),
                    'searchedWord' => $item->getSearchedWord(),
                    'searchedWordHtml' => $item->getSearchedWordHtml(),
                    'trademarkName' => $item->getTrademarkName(),
                    'trademarkNameHtml' => $item->getTrademarkNameHtml(),
                    'niceClasses' => $item->getNiceClasses(),
                    'applicationNo' => $item->getApplicationNo(),
                    'applicationDate' => $item->getApplicationDate(),
                    'registerDate' => $item->getRegisterDate(),
                    'protectionDate' => $item->getProtectionDate(),
                    'holderName' => $item->getHolderName(),
                    'bulletinNo' => $item->getBulletinNo(),
                    'bulletinPage' => $item->getBulletinPage(),
                    'fileStatus' => $item->getFileStatus(),
                    'shapeSimilarity' => $item->getShapeSimilarity(),
                    'phoneticSimilarity' => $item->getPhoneticSimilarity(),
                    'isPriority' => $item->getIsPriority(),
                    'created_at' => $_result ? $_result[0]['created_at'] : null,
                    'nerde_kaldim' => $nerde_kaldim_result ? $nerde_kaldim_result[0]['created_at'] : null,

                ];
            }
        }






        return $response;
    }



    // Get Observation List
    public  function getObservationListFile($data)
    {
        $response = [];
        $url = Observation::RESEARCH_SERVICE_URL;
        $method = Observation::RESEARCH_SERVICE_METHOD;
        $token = $this->getToken();

        // $exists = $this->observationCacheMainListRepository->existsObservationCacheMain($searchedWord, $niceClasses, $bulletinNo, $data);
        /**
         * Bu gözlem daha önceden aranmamış ise sonuçları kendi veritabanımıza kaydet
         */
        $response = $this->client->request(
            $method,
            $url,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode($data),
            ],

        );
        $response = $response->toArray();

        $this->writeToTxt($response, $data);




        return $response;
    }


    public function writeToTxt($veriler, $data)
    {

        // $veriler = [
        //     [
        //         "id" => 0,
        //         "dataSource" => 0,
        //         "searchedWord" => "ash",
        //         "searchedWordHtml" => "<strong>ash</strong>",
        //         "trademarkName" => "ttt inovasyon enerji a.ş.",
        //         "trademarkNameHtml" => "<strong></strong>",
        //         "niceClasses" => "01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20 21 22 23 24 25 26 27 28 29 30 31 32 33 34 35 36 37 38 39 40 41 42 43 44 45",
        //         "applicationNo" => "2023/081354",
        //         "applicationDate" => "2023-06-20T00:00:00",
        //         "registerDate" => null,
        //         "protectionDate" => "2023-06-20T00:00:00",
        //         "holderName" => "TTT İNOVASYON ENERJİ ANONİM ŞİRKETİ",
        //         "bulletinNo" => 427,
        //         "bulletinPage" => 1124,
        //         "fileStatus" => "",
        //         "shapeSimilarity" => "%95",
        //         "phoneticSimilarity" => 0,
        //         "isPriority" => true,
        //         'trademark_id' => 1211
        //     ],
        //     // Diğer veriler buraya eklenebilir
        // ];

        // public içinde veriler.sql 

        $dosyaAdi = "veriler.sql";

        $dosya = fopen($dosyaAdi, 'a');



        if ($dosya) {
            foreach ($veriler['trademarkSearchList'] as $veri) {


                $dataSource = $veri["dataSource"];
                $searchedWord = str_replace("'", "",  $veri["searchedWord"]);
                $searchedWordHtml = str_replace("'", "", $veri["searchedWordHtml"]);
                $trademarkName = str_replace("'", "", $veri["trademarkName"]);
                $trademarkNameHtml = str_replace("'", "", $veri["trademarkNameHtml"]);
                $niceClasses = $veri["niceClasses"] ?? null;
                $applicationNo = $veri["applicationNo"] ?? null;
                $applicationDate = $veri["applicationDate"] ?? null;
                $registerDate = $veri["registerDate"] ?? null;
                $protectionDate = $veri["protectionDate"] ?? null;
                $holderName = str_replace("'", "", $veri["holderName"]);
                $bulletinNo = $veri["bulletinNo"] ?? null;
                $bulletinPage = $veri["bulletinPage"] ?? null;
                $fileStatus = $veri["fileStatus"] ?? null;
                $shapeSimilarity = $veri["shapeSimilarity"] ?? null;
                $phoneticSimilarity = $veri["phoneticSimilarity"] ?? null;
                $isPriority = $veri["isPriority"] ?? null;
                $trademark_id = $data['trademark_id'] ?? null;
                $account_id = $data['account_id'] ?? null;
                $foreign_company_email = $data['foreign_company_email'] ?? null;
                $TrademarkName = $data['TrademarkName'];
                $trademark_BulletinNo = $data['BulletinNo'] ?? null;


                $sql = "INSERT INTO tbl_fileobservation_auto_result (dataSource, searchedWord, searchedWordHtml, trademarkName, trademarkNameHtml, niceClasses, applicationNo, applicationDate, registerDate, protectionDate, holderName, webiz_bulletinNo, bulletinPage, fileStatus, shapeSimilarity, phoneticSimilarity, isPriority, trademark_id, account_id, foreign_company_email, search_trademark, trademark_bulletinno) VALUES ( '$dataSource', '$searchedWord', '$searchedWordHtml', '$trademarkName', '$trademarkNameHtml', '$niceClasses', '$applicationNo', '$applicationDate', '$registerDate', '$protectionDate', '$holderName', '$bulletinNo', '$bulletinPage', '$fileStatus', '$shapeSimilarity', '$phoneticSimilarity', '$isPriority', '$trademark_id', '$account_id', '$foreign_company_email', '$TrademarkName', '$trademark_BulletinNo'); \n";

                fwrite($dosya, $sql);
            }


            fclose($dosya);
        } else {
            echo "Dosya açma hatası!";
        }
    }


    /**
     * Gözlem için itiraz kaydı
     */

    public function objection($data)
    {

        /**
         * columns 
         * sinif, marka_adi, marka_id, application_no, created_at, bulletin_no
         *
         * tbl = tbl_n_trademark_objections insert
         */

        $siniflar = $data['siniflar'];
        $marka_adi = $data['marka-adi'];
        $application_no = $data['application_no'];
        $created_at = $data['created_at'];
        $bulten = $data['bulten-no'];
        $marka_id = $data['marka_id'];
        $itirazEdilenMarka = $data['itirazEdilenMarka'];

        $sql = "INSERT INTO tbl_n_trademark_objections (sinif, marka_adi, application_no, created_at, bulletin_no, marka_id, itiraz_edilen_marka_adi) VALUES ('$siniflar', '$marka_adi', '$application_no', '$created_at', '$bulten', '$marka_id', '$itirazEdilenMarka');";

        $result = $this->db->prepare($sql)->execute();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Nerde Kaldım
     */

    public function nerde_kaldim($data)
    {

        /**
         * columns 
         * sinif, marka_adi, marka_id, application_no, created_at, bulletin_no
         *
         * tbl = tbl_n_trademark_objections insert
         */

        $siniflar = $data['siniflar'];
        $marka_adi = $data['marka-adi'];
        $application_no = $data['application_no'];
        $created_at = $data['created_at'];
        $bulten = $data['bulten-no'];
        $marka_id = $data['marka_id'];
        $itirazEdilenMarka = $data['itirazEdilenMarka'];

        // exists nerde kaldım

        $sql = "SELECT * FROM nerde_kaldim WHERE  marka_adi = '$marka_adi' AND application_no = '$application_no'  AND marka_id = '$marka_id' AND itiraz_edilen_marka_adi = '$itirazEdilenMarka'";
        $exists = $this->db->prepare($sql)->execute()->fetchAllAssociative();

        if (count($exists) > 0) {
            // delete 
            $sql = "DELETE FROM nerde_kaldim WHERE  marka_adi = '$marka_adi' AND application_no = '$application_no'  AND marka_id = '$marka_id' AND itiraz_edilen_marka_adi = '$itirazEdilenMarka'";
            $this->db->prepare($sql)->execute();
            return false;
        }


        $sql = "INSERT INTO nerde_kaldim (sinif, marka_adi, application_no, created_at, bulletin_no, marka_id, itiraz_edilen_marka_adi) VALUES ('$siniflar', '$marka_adi', '$application_no', '$created_at', '$bulten', '$marka_id', '$itirazEdilenMarka');";

        $result = $this->db->prepare($sql)->execute();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }


    // Benzerlik oranı 
    public function benzerlikOrani($result)
    {
        $result['trademarkSearchList'] = array_map(function ($item) {
            $item['shapeSimilarity'] = explode('%', $item['shapeSimilarity'])[1];
            $item['shapeSimilarity'] = (int) $item['shapeSimilarity'];
            


            $benzerlik = $item['shapeSimilarity'];
            if ($benzerlik != null) {
                $benzerlik = (float) $benzerlik;
                if ($benzerlik <= 40) {
                    $benzerlik = "<span class='badge badge-rounded badge-warning' style='font-size: 18px'>%$benzerlik</span>";
                } else if ($benzerlik > 40 && $benzerlik <= 60) {
                    $benzerlik = "<span class='badge badge-rounded badge-primary' style='font-size: 18px'>%$benzerlik</span>";
                } else if ($benzerlik > 60 && $benzerlik <= 80) {
                    $benzerlik = "<span class='badge badge-rounded badge-warning' style='font-size: 18px'>%$benzerlik</span>";
                } else if ($benzerlik > 80) {
                    $benzerlik = "<span class='badge badge-rounded badge-danger' style='font-size: 18px'>%$benzerlik</span>";
                }
            }
            $item['benzerlikOrani'] =mb_convert_encoding( $benzerlik, 'UTF-8', 'UTF-8');

            return $item;
        }, $result['trademarkSearchList']);

        return $result;
    }

    // Bülten Tarihleri
    public function bultenTarihleri($tarihler, $result)
    {
        // $bultenTarihleri = $this->domesticBrandService->getBulletinDate();
        $bultenTarihleri = $tarihler;


        $result['trademarkSearchList'] = array_map(function ($item) use ($bultenTarihleri) {
            $item['bultenTarihi'] = array_filter($bultenTarihleri, function ($bultenItem) use ($item) {
              
                return intval($bultenItem['bulten_no']) === intval($item['bulletinNo']);
            });
            // indeksleri sıfırla
            $item['bultenTarihi'] = array_values( $item['bultenTarihi']);
            return $item;
        }, $result['trademarkSearchList']);



        return $result;
    }

    // Benzer Sınıflar
    public function benzerSiniflar($markaninSiniflari, $result)
    {
        $result['trademarkSearchList'] = array_map(function ($item) use ($markaninSiniflari) {
            $gozlem_siniflari = explode(" ", $item['niceClasses']);
            $marka_siniflari = $markaninSiniflari;


            $gozlem_siniflari = array_map(function ($item) {
                return (int) $item;
            }, $gozlem_siniflari);

            $marka_siniflari = array_map(function ($item) {
                return (int) $item;
            }, $marka_siniflari);

            $sinifHtmlContent = '';

            foreach ($gozlem_siniflari as $key => $value) {
                if (in_array($value, $marka_siniflari)) {
                    $sinifHtmlContent .= "<span class='badge badge-danger' style='margin-left:1px'>$value</span>";
                } else {
                    $sinifHtmlContent .= "<span class='badge badge-dark' style='margin-left:1px'>$value</span>";
                }
            }
           
            $item['siniflar'] = mb_convert_encoding( $sinifHtmlContent, 'UTF-8', 'UTF-8'); 

            return $item;
        }, $result['trademarkSearchList']);

        return $result;
    }

    // Benzer Markalar 
    public function benzerHarfleriRenklendir($similarKeyword, $mainKeyword) {
        $mainKeywordArr = str_split($mainKeyword);
        $similarKeywordArr = str_split($similarKeyword);
        $temp = [];
        $count = 0;
    
        // Benzerliği aranan kelimeyi dolaş
        for ($i = 0; $i < count($mainKeywordArr); $i++) {
            $matchFound = false;
    
            for ($j = 0; $j < count($similarKeywordArr); $j++) {
                if (
                    $mainKeywordArr[$i] === $similarKeywordArr[$j] &&
                    isset($mainKeywordArr[$i + 1]) &&
                    isset($similarKeywordArr[$j + 1]) &&
                    $mainKeywordArr[$i + 1] === $similarKeywordArr[$j + 1]
                ) {
                    $matchFound = true;
    
                    // Ardışık benzer 4 harfi kontrol et
                    $k = $i;
                    $l = $j;
    
                    while (
                        $k < count($mainKeywordArr) &&
                        $l < count($similarKeywordArr) &&
                        $mainKeywordArr[$k] === $similarKeywordArr[$l]
                    ) {
                        $temp[] = '<span style="color: red;">' . $mainKeywordArr[$k] . '</span>';
                        $k++;
                        $l++;
                        $count++;
                    }
    
                    // Ardışık 4 harfi bulduğumuzda, döngüde devam etmesini sağlamak için i'yi güncelle
                    $i = $k - 1;
    
                    break;
                } else {
                    // count kadar olan kelimeleri ekle
                    $count = 0;
                }
            }
    
            if (!$matchFound) {
                $temp[] = $mainKeywordArr[$i];
            }
        }
    
        return implode("", $temp);
    }
}
