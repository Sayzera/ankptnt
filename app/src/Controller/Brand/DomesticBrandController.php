<?php

namespace App\Controller\Brand;

use App\Service\DomesticBrandService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DomesticBrandController extends AbstractController
{
    public function __construct(private DomesticBrandService $domesticBrandService)
    {
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


    // Yurtiçi Başvuru Dosyası
    #[Route('/brand/domestic', name: 'app_brand_domestic_brand')]
    public function index(Request $request): Response
    {
        // Yurt İçi Marka Başvuru Dosyası
        $yim = $this->domesticBrandService->getTrademarkYimFile($request);


        return $this->render('brand/domestic_brand/index.html.twig', [
            'controller_name' => 'DomesticBrandController',
            'yim' => $yim
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
        return $this->domesticBrandService->getTrademarkYdnFile($request);
    }

    // Yurtiçi Detay
    #[Route('/brand/domestic/detail', name: 'app_brand_domestic_brand_detail')]
    public function detail(): Response
    {
        return $this->render('brand/domestic_brand/detail.html.twig', [
            'controller_name' => 'DomesticBrandController',
        ]);
    }

    // Yurtiçi İtiriaz
    #[Route('/brand/domestic/appeal-file', name: 'app_brand_domestic_brand_appeal_file')]
    public function appealFile(): Response
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

    // Yurtdışı İtiraz
    #[Route('/brand/international/appeal-file', name: 'app_brand_international_appeal_file')]
    public function international_appealFile(): Response
    {
        return $this->render('brand/international_trademark/appeal_file.html.twig', [
            'controller_name' => 'DomesticBrandController',
        ]);
    }
}
