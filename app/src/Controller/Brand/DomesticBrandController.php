<?php

namespace App\Controller\Brand;

use App\Service\DomesticBrandService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DomesticBrandController extends AbstractController
{
    public function __construct(private DomesticBrandService $domesticBrandService)
    {
    }

    // Yurtiçi Başvuru Dosyası
    #[Route('/brand/domestic', name: 'app_brand_domestic_brand')]
    public function index(): Response
    {
        // Yurt İçi Marka Başvuru Dosyası
        $yim =  $this->domesticBrandService->getDomesticBrand();


        return $this->render('brand/domestic_brand/index.html.twig', [
            'controller_name' => 'DomesticBrandController',
            'yim' => $yim
        ]);
    }

    #[Route('/brand/domestic/brand', name: 'app_brand_domestic_brand_brand', methods: ['GET'])]
    public function brand(Request $request)
    {

        //get all $request
        $tableData = $request->query->all();

        return $this->domesticBrandService->getTrademark($tableData);
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
