<?php

namespace App\Controller\Observation;

use App\Service\AutoObservationService;
use App\Service\ObservationService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AutoObservationController extends AbstractController
{
    private AutoObservationService $autoObservationService;
    private $observationResult = [];
    private $account_ref_id;
    public function __construct()
    {
    }

    /**
     * Otomatik gözlem yap
     */
    #[Route('/company/observation/auto', name: 'app_company_observation_auto')]
    public function start_automatic_observation(ManagerRegistry $registry, ObservationService $observationService, Request $request)
    {
        // İşlemin başlangıç zamanını alın
        $startTime = microtime(true);
        $data = [];

        $this->autoObservationService = new AutoObservationService($registry);


        /**
         * Otomatik gözlem yapılacak markaların listesi
         */
        $automaticObservationList = $this->autoObservationService->getAutoObservationList();
        // dd($automaticObservationList);

        if (count($automaticObservationList) ==  0) {
            dd('Otomatik gözlem yapılacak marka bulunamadı');
        }


        /**
         * Otomatik gözlem için tüm markaları döngüye aldık
         */
        foreach ($automaticObservationList as $item) {

            // TODO: bulletinNo dinamik olarak gelecek
            $account_ref_id = $item['company_id'];
            // $account_ref_id = 4527568;
            $bulletinNo = 427;




            // Marka listesini getir
            $trademarks =  $this->autoObservationService->getTrademarkList($account_ref_id);



            /**
             * Eğer marka var ise gözlem yap
             */
            if (count($trademarks) > 0) {
                foreach ($trademarks as $trademark) {
                    /**
                     * Gözlem için gerekli parametreler
                     */
                    $data = [
                        'TrademarkName' => $trademark['col_trademark'],
                        'NiceClasses' =>
                        array_map(fn ($trademark) => ['No' => $trademark], explode(',', $trademark['col_class_string'])),
                        'BulletinNo' => $bulletinNo,
                        'trademark_id' => $trademark['col_id'],
                        'account_id' => $account_ref_id,
                        'yda_marka' => isset($trademark['yim_marka']) ? false : true,
                        'yim_marka' => isset($trademark['yim_marka']) ? true : false,
                    ];


                    /**
                     * Eğer yabanci bir firma ise yabancı firma email adresini al
                     */
                    if ($item['foreign_company_email'] && $item['is_foreign_company']) {
                        $data['foreign_company_email'] = $item['foreign_company_email'];
                        $data['is_foreign_company'] = $item['is_foreign_company'];
                    } else {
                        $data['foreign_company_email'] = null;
                        $data['is_foreign_company'] = false;
                    }


                    /**
                     * Yapılan gözlemin sonucu
                     */
                    $result =   $observationService->getObservationListFile($data);


                    // $this->observationResult[$account_ref_id][isset($trademark['yim_marka']) ? 'yim' : 'yda'][$trademark['col_id']] = $result;
                }
            }
        }

        // İşlemin son zamanını alın
        $endTime = microtime(true);

        // İki zaman arasındaki farkı hesaplayın
        $elapsedTime = $endTime - $startTime;

        // dakika olarak hesapla
        $elapsedTime = $elapsedTime / 60;


        dd($this->observationResult, $elapsedTime, 'gözlem tamamlandı');
        /**
         * Markanın Gözlem Sonuçlarını al
         */
    }


    /**
     * Gözlem Sonucu oluşturucu
     */
    #[Route('/company/observation/auto/result', name: 'app_company_observation_auto_result')]
    public function create_observation_result(ManagerRegistry $registry, ObservationService $observationService, Request $request)
    {
        $this->autoObservationService = new AutoObservationService($registry);

        $data = $this->observationResult;

        $this->autoObservationService->createObservationResult($data);
    }
}
