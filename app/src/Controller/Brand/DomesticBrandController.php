<?php

namespace App\Controller\Brand;

use App\Service\DomesticBrandService;
use App\Service\SearchObservationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DomesticBrandController extends AbstractController
{
    private $params;

    public function __construct(private DomesticBrandService $domesticBrandService, private SearchObservationService $searchObservationService,ContainerBagInterface $params)
    {
        $this->params = $params;
    }

    //    // Yurtiçi Başvuru Dosyası [OLD]
    //    #[Route('/brand/domestic', name: 'app_brand_domestic_brand')]
    //    public function index(): Response
    //    {
    //        // Yurt İçi Marka Başvuru Dosyası
    //        $yim =  $this->domesticBrandService->getDomesticBrand();
    //
    //
    //        return $this->render('brand/domestic_brand/index.html.twig', [
    //            'controller_name' => 'DomesticBrandController',
    //            'yim' => $yim
    //        ]);
    //    }

    #[Route('/brand/domestic/detail-modal', name: 'app_brand_domestic_detail_modal', methods: ['GET'])]
    public function detailModal(Request $request)
    {
        $type = $request->query->get('type') ?? 'yim';
        // get query params
        $col_id = $request->query->get('col_id') ?? null;

        switch ($type) {
            case 'yim':
                /**
                 * Gözlem listesi bilgileri
                 */
                $result = $this->domesticBrandService->findByBrandInfo($col_id);
                return $result;

            case 'actions':
                $result = $this->domesticBrandService->findByActions($col_id);
                return $result;

            case 'invoice':
                /**
                 * col_id = account_ref
                 *
                 * diğerlerinde ise trademark olarak çalışıyor
                 */
                $result = $this->domesticBrandService->findByInvoices($col_id);
                return $result;

            case 'classes':
                $result = $this->domesticBrandService->findByClasses($col_id);
                return $result;

            default:
                return [
                    'status' => false,
                    'message' => 'Bir hata oluştu'
                ];
        }
    }

    /**
     * Yurtdışı detail
     */
    #[Route('/brand/international/yda-detail-modal', name: 'app_brand_international_detail_modal', methods: ['GET'])]
    public function detailYDAModal(Request $request)
    {
        $type = $request->query->get('type') ?? 'yim';
        // get query params
        $col_id = $request->query->get('col_id') ?? null;

        switch ($type) {
            case 'ydn':
                $result = $this->domesticBrandService->findByYDABrandInfo($col_id);
                return $result;

            case 'actions':
                $result = $this->domesticBrandService->findByYDAActions($col_id);
                return $result;

            case 'classes':
                $result = $this->domesticBrandService->findByYDAClasses($col_id);
                return $result;

            case 'invoice':
                $result = $this->domesticBrandService->getInvoiceList($col_id);
                return $result;

            default:
                return [
                    'status' => false,
                    'message' => 'Bir hata oluştu'
                ];
        }
    }


    // Yurtiçi Başvuru Dosyası
    #[Route('/brand/domestic', name: 'app_brand_domestic_brand')]
    public function index(Request $request): Response
    {
        // Yurt İçi Marka Başvuru Dosyası
        // $yim = $this->domesticBrandService->getTrademarkYimFile($request);


        return $this->render('brand/domestic_brand/index.html.twig', [
            'controller_name' => 'DomesticBrandController',
        ]);
    }

    #[Route('/brand/domestic/brand', name: 'app_brand_domestic_brand_brand', methods: ['GET'])]
    public function brand(Request $request)
    {
        return $this->domesticBrandService->getTrademarkYimFile($request);
    }

    #[Route('/brand/international_application/brand', name: 'app_brand_international_brand_brand', methods: ['GET'])]
    public function international_brand_ajax(Request $request)
    {
        return $this->domesticBrandService->getTrademarkYdaFile($request);
    }

    // Yurtiçi Detay
    #[Route('/brand/domestic/detail', name: 'app_brand_domestic_brand_detail')]
    public function detail(): Response
    {
        return $this->render('brand/domestic_brand/detail.html.twig', [
            'controller_name' => 'DomesticBrandController',
        ]);
    }
    /**
     * Başvuru itiraz dosyaları
     */
    #[Route('/brand/domestic/appeal-file-json', name: 'app_brand_domestic_brand_appeal_file_json', methods: ['GET'])]
    public function appealFileJson(Request $request)
    {
        return  $this->domesticBrandService->getOppositionList($request);
    }

    /**
     * Başvuru itiraz detay bilgiler modala tıklayınca gelen bilgiler 
     */
    #[Route('/brand/domestic/appeal-file-detail', name: 'app_brand_domestic_brand_appeal_file_detail', methods: ['GET'])]
    public function appealFileDetail(Request $request)
    {
        return  $this->domesticBrandService->getOppositionDetail($request);
    }

    /**
     * Başvuru itiraz detay faturalar
     */
    #[Route('/brand/domestic/appeal-file-invoices', name: 'app_brand_domestic_brand_appeal_file_invoices', methods: ['GET'])]
    public function appealFileInvoices(Request $request)
    {
        $col_id = $request->query->get('col_id');
        return $this->domesticBrandService->getOppInvoiceList($col_id);
    }

    /**
     * Yurtiçi marka itiraz detay işlemleri
     */

    #[Route('/brand/domestic/appeal-file-actions', name: 'app_brand_domestic_brand_appeal_file_actions', methods: ['GET'])]
    public function appealFileActions(Request $request)
    {
        $col_id = $request->query->get('col_id');
        return $this->domesticBrandService->findByYimOppActions($col_id);
    }

    /**
     * Yurtdışı başvuru Dosyaları
     */
    #[Route('/brand/domestic/appeal-files', name: 'app_domestic_files', methods: ['GET'])]
    public function domesticFilesModal(Request $request)
    {
        $col_id = $request->query->get('id');
        return   $this->domesticBrandService->domesticAppliactionFiles($col_id);
    }

    /**
     * Yurtiçi Eşya listesini getir
     */
    #[Route('/brand/domestic/appeal-file-goods', name: 'app_brand_domestic_brand_appeal_file_goods', methods: ['GET'])]
    public function appealFileGoods(Request $request)
    {
        return  $this->domesticBrandService->getOppositionGoods($request);
    }

    /**
     * Rüçhan listesi
     */
    #[Route('/brand/domestic/get-appeal-file-priority', name: 'app_get_brand_domestic_brand_appeal_file_priority', methods: ['GET'])]
    public function appealFilePriority(Request $request)
    {
        // GET
        $trademark_id = $request->query->get('id');

        return  $this->domesticBrandService->getOppositionPriority($trademark_id);
    }

    // Yurtiçi İtiriaz html sayfası
    #[Route('/brand/domestic/appeal-file', name: 'app_brand_domestic_brand_appeal_file')]
    public function appealFile(Request $request): Response
    {
        return $this->render('brand/domestic_brand/appeal_file.html.twig', [
            'controller_name' => 'DomesticBrandController',
        ]);
    }

    // Yurtdışı Başvuru Dosyası
    #[Route('/brand/international', name: 'app_brand_international')]
    public function international_brand(): Response
    {
        return $this->render('brand/international_trademark/index.html.twig', [
            'controller_name' => 'DomesticBrandController',
        ]);
    }

    // Yurtdışı Detay
    #[Route('/brand/international/detail', name: 'app_brand_international_detail')]
    public function international_detay(): Response
    {
        return $this->render('brand/international_trademark/detail.html.twig', [
            'controller_name' => 'DomesticBrandController',
        ]);
    }

    // Yurtdışı İtiraz JSON
    #[Route('/brand/international/appeal-file-json', name: 'app_brand_international_appeal_file_json', methods: ['GET'])]
    public function international_appealFileJson(Request $request)
    {
        $result =  $this->domesticBrandService->getYDAOppList($request);
        return $result;
    }

    // Yurtdışı itiraz detay eşya listesi
    #[Route('/brand/international/appeal-file-goods', name: 'app_brand_international_appeal_file_goods', methods: ['GET'])]
    public function international_appealFileGoods(Request $request)
    {
        return  $this->domesticBrandService->getYDAOppGoods($request);
    }

    /**
     * Yurtdışı itiraz detay dosyalrı
     */
    #[Route('/brand/domestic/opp-appeal-files', name: 'app_opp_domestic_files', methods: ['GET'])]
    public function domesticOppFilesModal(Request $request)
    {
        $col_id = $request->query->get('id');
        return   $this->domesticBrandService->domesticOppAppliactionFiles($col_id);
    }

    // Yurtdışı itiraz detay işlemler
    #[Route('/brand/international/appeal-file-actions', name: 'app_brand_international_appeal_file_actions', methods: ['GET'])]
    public function international_appealFileActions(Request $request)
    {
        $col_id = $request->query->get('col_id');


        return  $this->domesticBrandService->findByYDAOppActions($col_id);
    }

    // Yurtdışı İtiraz Detay
    #[Route('/brand/yda/appeal-file-detail', name: 'app_brand_yda_brand_appeal_file_detail', methods: ['GET'])]
    public function appealYDAFileDetail(Request $request)
    {
        return  $this->domesticBrandService->getYDAOppositionDetail($request);
    }

    // Yurtdışı İtiraz
    #[Route('/brand/international/appeal-file', name: 'app_brand_international_appeal_file')]
    public function international_appealFile(Request $request): Response
    {
        return $this->render('brand/international_trademark/appeal_file.html.twig', [
            'controller_name' => 'DomesticBrandController',
        ]);
    }



    /**
     * ----------------------- YDA AND YIM -----------------------
     */

     /**
      * Kullanıcının tüm markalarını getirir 
      */
     #[Route('/brand/yda-and-yim-get-all-trademark', name: 'app_brand_yda_and_yim_get_all_trademark', methods: ['GET'])]
     public function ydaAndYim(Request $request)
     {
            return $this->domesticBrandService->yda_and_yim_get_all_trademark_yim_file($request);
     }

    #[Route('/brand/yda-and-yim', name: 'yda_and_yim_app_brand_domestic_brand_brand', methods: ['GET'])]
    public function ydaAndYimBrand(Request $request)
    {
        $secili_bulten_no = $this->params->get('secili_bulten_no');


        return $this->domesticBrandService->yda_and_yim_getTrademarkYimFile($request, $secili_bulten_no);
    }

    #[Route('/brand/yda-and-yim-all', name: 'yda_and_yim_app_brand_domestic_brand_brand_all', methods: ['GET'])]
    public function ydaAndYimBrandAll(Request $request)
    {
        $secili_bulten_no = $this->params->get('secili_bulten_no');

        return $this->domesticBrandService->yda_and_yim_getTrademarkYimFileAll($request, $secili_bulten_no);
    }


    /**
     * Yurtiçi ve yurtdışı markaları
     */
    #[Route('/brand/domestic/international', name: 'app_brand_domestic_international', methods: ['GET'])]
    public function domesticInternational(Request $request)
    {   

        $bitis_bulten_no = $this->params->get('bitis_bulten_no'); // Büyük değer
        $baslangic_bulten_no = $this->params->get('baslangic_bulten_no'); // Küçük değer
        $secili_bulten_no = $this->params->get('secili_bulten_no');
        $secilebilir_elemanlar = $this->params->get('secilebilir_elemanlar');

        
        
        return $this->render('yimAndydaObservation/domestic_brand/index.html.twig', [
            'controller_name' => 'DomesticBrandController',
            'bitis_bulten_no' => $bitis_bulten_no,
            'baslangic_bulten_no' => $baslangic_bulten_no,
            'secili_bulten_no' => $secili_bulten_no,
            'secilebilir_elemanlar' => $secilebilir_elemanlar
        ]);
    }

    /**
     * Yurtiçi Markaları
     */
    #[Route('/brand/domestic/international-all', name: 'app_brand_domestic_international_all', methods: ['GET'])]
    public function domesticInternationalAll(Request $request)
    {   
        // Şuan için kullanılmıyor
        $bitis_bulten_no = $this->params->get('bitis_bulten_no'); // Büyük değer
        $baslangic_bulten_no = $this->params->get('baslangic_bulten_no'); // Küçük değer
        $secili_bulten_no = $this->params->get('secili_bulten_no');
        
        return $this->render('yimAndydaObservation/domestic_brand/all-trademark-domestic.html.twig', [
            'controller_name' => 'DomesticBrandController',
            'bitis_bulten_no' => $bitis_bulten_no,
            'baslangic_bulten_no' => $baslangic_bulten_no,
            'secili_bulten_no' => $secili_bulten_no
        ]);
    }

    /**
     * Markanın Gözlem durumunu aktif pasif yapma
     */

    #[Route('/brand/domestic/observation-status', name: 'app_brand_domestic_observation_status', methods: ['GET'])]
    public function observationStatus(Request $request)
    {
        $col_id = $request->query->get('col_id');

        return $this->domesticBrandService->observationStatus($col_id);
    }
}
