<?php

namespace App\Controller\Patent;

use App\Service\PatentService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PatentController extends AbstractController
{

    /**
     * Başvuru öncesi kayıt listesi
     */
    #[Route('/patent/pre-application-registration-json', name: 'app_patent_pre_application_registration_json')]
    public function preApplicationRegistrationJson(ManagerRegistry $registry, Request $request)
    {
        $patentService = new PatentService($registry, $request);

        return  $patentService->getPreApplicationRegistrationList();
    }

    /**
     * başvuru öncesi detay bilgisi
     */
    #[Route('/patent/pre-application-registration-detail-json', name: 'app_patent_pre_application_registration_detail_json')]
    public function preApplicationRegistrationDetailJson(ManagerRegistry $registry, Request $request)
    {
        $patentService = new PatentService($registry, $request);
        $col_id = $request->query->get('col_id');

        return  $patentService->getPreApplicationRegistrationDetail($col_id);
    }

    /**
     * başvuru öncesi detay işlemleri
     */
    #[Route('/patent/pre-application-registration-detail-process-json', name: 'app_patent_pre_application_registration_detail_process_json')]
    public function preApplicationRegistrationDetailProcessJson(ManagerRegistry $registry, Request $request)
    {
        $patentService = new PatentService($registry, $request);
        $col_id = $request->query->get('col_id');
        return  $patentService->getPreApplicationRegistrationDetailProcess($col_id);
    }

    /**
     * Başvuru Öncesi kayıt Faturalar
     */
    #[Route('/patent/pre-application-registration-detail-invoice-json', name: 'app_patent_pre_application_registration_detail_invoice_json')]
    public function preApplicationRegistrationDetailInvoiceJson(ManagerRegistry $registry, Request $request)
    {
        $patentService = new PatentService($registry, $request);
        $file_number = $request->query->get('file_number');
        return  $patentService->getPreApplicationRegistrationDetailInvoice($file_number);
    }

    #[Route('/patent/pre-application-registration', name: 'app_patent_pre_application_registration')]
    public function index(): Response
    {
        return $this->render('patent/pre-application-registration.html.twig', [
            'controller_name' => 'PatentController',
        ]);
    }

    #[Route('/patent/detail', name: 'app_patent_detail')]
    public function detail(): Response
    {
        return $this->render('patent/detail.html.twig', [
            'controller_name' => 'PatentController',
        ]);
    }

    /**
     * Patent başvuru detay bilgileri
     */

    #[Route('/patent/application-detail-json', name: 'app_patent_application_detail_json')]
    public function applicationDetailJson(ManagerRegistry $registry, Request $request)
    {
        $patentService = new PatentService($registry, $request);
        $col_id = $request->query->get('col_id');
        return  $patentService->getPatentApplicationDetail($col_id);
    }

    /**
     * Patent Rüçhan listesi
     */
    #[Route('/patent/appeal-file-priority', name: 'app_brand_domestic_brand_appeal_file_priority', methods: ['GET'])]
    public function appealFilePriority(Request $request, ManagerRegistry $registry)
    {
        // GET
        $patent_id = $request->query->get('id');;
        $patentService = new PatentService($registry, $request);
        return  $patentService->getOppositionPriority($patent_id);
    }
    /**
     * Patent başvuru detay işlemleri 
     */

    #[Route('/patent/application-detail-actions-json', name: 'app_patent_application_detail_process_json')]
    public function applicationDetailProcessJson(ManagerRegistry $registry, Request $request)
    {
        $patentService = new PatentService($registry, $request);
        $col_id = $request->query->get('col_id');
        return  $patentService->getPatentDetailActions($col_id);
    }

    /**
     * Patent başvuru detay modal dosya listesi
     */
    #[Route('/patent/application-detail-modal-files-json', name: 'app_patent_application_detail_modal_files_json')]
    public function applicationDetailModalFilesJson(ManagerRegistry $registry, Request $request)
    {
        $patentService = new PatentService($registry, $request);
        $col_id = $request->query->get('col_id');
        return  $patentService->getPatentDetailFiles($col_id);
    }

    /**
     * Patent başvuru detay modal ülke girişi
     */
    #[Route('/patent/application-detail-modal-country-json', name: 'app_patent_application_detail_modal_country_json')]
    public function applicationDetailModalCountryJson(ManagerRegistry $registry, Request $request)
    {
        $patentService = new PatentService($registry, $request);
        $col_id = $request->query->get('col_id');
        return  $patentService->getPatentDetailCountryEntrance($col_id);
    }

    /**
     * Patent Başvuru listesi json
     */
    #[Route('/patent/application-list-json', name: 'app_patent_application_list_json')]
    public function applicationListJson(ManagerRegistry $registry, Request $request)
    {
        $patentService = new PatentService($registry, $request);
        
        return  $patentService->getPatentApplicationListJson($request);
    }


    /**
     * Patent modal taksit bilgisi
     */
    #[Route('/patent/application-detail-modal-installment-json', name: 'app_patent_application_detail_modal_installment_json')]
    public function applicationDetailModalInstallmentJson(ManagerRegistry $registry, Request $request)
    {
        $patentService = new PatentService($registry, $request);
        $col_id = $request->query->get('col_id');
        return  $patentService->getPatentModalInstallment($col_id);
    }

    /**
     * Patent File Ek Bilgiler
     */
    #[Route('/patent/application-detail-modal-file-additional-json', name: 'app_patent_application_detail_modal_file_additional_json')]
    public function applicationDetailModalFileAdditionalJson(ManagerRegistry $registry, Request $request)
    {
        $patentService = new PatentService($registry, $request);
        $col_id = $request->query->get('col_id');
        return  $patentService->getPatentFileAdditional($col_id);
    }

    /**
     * Patent File Faturalar
     */
    #[Route('/patent/application-detail-modal-file-invoice-json', name: 'app_patent_application_detail_modal_file_invoice_json')]
    public function applicationDetailModalFileInvoiceJson(ManagerRegistry $registry, Request $request)
    {
        $patentService = new PatentService($registry, $request);
        $col_id = $request->query->get('col_id');
        return  $patentService->getPatentFileInvoice($col_id);
    }




    /**
     * Patent başvuru  listesi
     */
    #[Route('/patent/application-document', name: 'app_patent_application_document')]
    public function applicationDocument(ManagerRegistry $registry, Request $request): Response
    {
        $patentService = new PatentService($registry, $request);

        // $result = $patentService->getPatentApplicationList();

        return $this->render('patent/application_document.html.twig', [
            'controller_name' => 'PatentController',
            // 'data' => $result,
        ]);
    }

    #[Route('/patent/application-document-detail', name: 'app_patent_application_document_detail')]
    public function applicationDocumentDetail(): Response
    {

        return $this->render('patent/application_document_detail.html.twig', [
            'controller_name' => 'PatentController',
        ]);
    }

    /**
     * Patent Döküman json
     */
    #[Route('/patent/document-json', name: 'app_patent_document_json')]
    public function documentJson(ManagerRegistry $registry, Request $request)
    {
        $patentService = new PatentService($registry, $request);
        return  $patentService->getPatentDocumentListJson();
    }

    /**
     * Patent dökümanları
     */
    #[Route('/patent/documents', name: 'app_patent_documents')]
    public function documents(): Response
    {
        return $this->render('patent/documents.html.twig', [
            'controller_name' => 'PatentController',
        ]);
    }


    /**
     * Patent Yayın Bilgisi
     */
    #[Route('/patent/publication-information', name: 'app_patent_publication_information')]
    public function publicationInformation(ManagerRegistry $registry, Request $request)
    {
        $patentService = new PatentService($registry, $request);
        $id = $request->query->get('col_id');
        return  $patentService->getPatentPublicationDetail($id);
    }
}
