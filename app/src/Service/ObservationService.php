<?php
namespace  App\Service;

use App\Entity\Enum\Observation;
use App\Repository\ObservationCacheMainListRepository;
use App\Repository\ObservationCacheRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @description : Gözlem listesi için gerekl olan servis isteklerini yapan sınıf
 */
class ObservationService {
    public function __construct(
        private HttpClientInterface $client,
        private ObservationCacheRepository $observationCacheRepository,
        private  ObservationCacheMainListRepository $observationCacheMainListRepository
    ) {
    }

    // Get Token
    public function getToken(){
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
    // Get Observation List
    public  function getObservationList($data) {
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

        $exists = $this->observationCacheMainListRepository->existsObservationCacheMain($searchedWord, $niceClasses, $bulletinNo);
        /**
         * Bu gözlem daha önceden aranmamış ise sonuçları kendi veritabanımıza kaydet
         */
        if(!$exists) {
            $response = $this->client->request(
                $method,
                $url,
                [
                    'headers' => [
                        'Authorization' => 'Bearer '.$token,
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
              $response['trademarkSearchList'][] = [
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
              ];
          }



        }






        return $response;
    }

}