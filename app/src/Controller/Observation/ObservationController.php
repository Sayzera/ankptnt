<?php

namespace App\Controller\Observation;

use App\Repository\ObservationCacheRepository;
use App\Service\DomesticBrandService;
use App\Service\ObservationService;
use App\Validators\ObservationValidators;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ObservationController extends AbstractController
{
    public function __construct(private  ObservationService $observationService,
                                private ObservationValidators $observationValidators)
    {
    }
    #[Route('/company/observation/{sinif}/{marka}/{id}', name: 'app_company_observation',
        defaults: ['sinif' => '1', 'marka' => 'test'],
        requirements: [
            'sinif' => '.*',
            'marka' => '.*',
            'id' => '\d+'
        ]
    )]
    public function index(Request $request, DomesticBrandService $domesticBrandService): Response
    {
        $id = $request->attributes->get('id');

        $data = [];
        if($id) {
            $data['my_brand'] = $domesticBrandService->getDomesticBrand($id)[0];
        }


        return $this->render('observation/company-observation.html.twig', [
            'controller_name' => 'ObservationController',
            'data' => $data
        ]);
    }
    #[Route('/company/observation/observation-list', name: 'app_company_observation_list', methods: ['POST'])]
    public function observationList(Request $request, ObservationCacheRepository $observationCacheRepository): Response
    {
        $marka = $request->request->get('marka-adi');
        $bulten = $request->request->get('bulten-no');
        $siniflar = $request->request->get('siniflar') ?
            explode(',', $request->request->get('siniflar'))
            : [];
        $siniflar = array_map(function ($sinif) {
            return ['No' => $sinif];
        }, $siniflar);

        // validations
        $validation =  $this->observationValidators->checkObservationValidator([
            'marka-adi' => $marka,
            'bulten-no' => $bulten,
            'siniflar' => $siniflar,
            'token' => $request->request->get('token')
        ]);
        if (count($validation) > 0) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Hay aksi! Formda bazı hatalar var.',
                'validations' => $validation
            ], 500);
        }

        // CSRF token kontrolü
        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid('delete-item', $submittedToken)) {
            //  throw new \Exception('Invalid CSRF token');
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Invalid CSRF token',
                'data' => []
            ], 500);
        }


        try {
            $data = [
                "IncludeRelatedNiceClassesToTrademarkSearch" => false,
                "TrademarkName" => $marka,
                "BulletinNo" =>  $bulten,
                "CalculateTotalRowCountWithoutPaging" => false,
                "NiceClasses" => $siniflar,
            ];
            // Gözlem sonucu

            $result = $this->observationService->getObservationList($data);

            return new JsonResponse(
                [
                    'status' => 'success',
                    'message' => 'Gözlem listesi başarıyla getirildi',
                    'data' => $result
                ]
            );
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
