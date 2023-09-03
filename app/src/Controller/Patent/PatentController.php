<?php

namespace App\Controller\Patent;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PatentController extends AbstractController
{
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

    #[Route('/patent/application-document', name: 'app_patent_application_document')]
    public function applicationDocument(): Response
    {
        return $this->render('patent/application_document.html.twig', [
            'controller_name' => 'PatentController',
        ]);
    }

    #[Route('/patent/application-document-detail', name: 'app_patent_application_document_detail')]
    public function applicationDocumentDetail(): Response
    {
        return $this->render('patent/application_document_detail.html.twig', [
            'controller_name' => 'PatentController',
        ]);
    }
    #[Route('/patent/documents', name: 'app_patent_documents')]
    public function documents(): Response
    {
        return $this->render('patent/documents.html.twig', [
            'controller_name' => 'PatentController',
        ]);
    }
}
