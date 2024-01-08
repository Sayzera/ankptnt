<?php

namespace App\Controller\Design;

use App\Service\DesignService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DesignController extends AbstractController
{
    private $designService;



    #[Route('design/design', name: 'app_design_design')]
    public function index(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $designList =  $this->designService = new DesignService($managerRegistry, $request);

        return $this->render('design/application_document.html.twig', [
            'controller_name' => 'DesignController',
            'designList' => $designList->getDesingList($request)
        ]);
    }

    #[Route('/design-detail-modal/{id}/{type}', name: 'app_design_detail_modal')]
    public function designDetailModal(Request $request, ManagerRegistry $managerRegistry)
    {
        $id = $request->get('id');
        $type = $request->get('type');
        $this->designService = new DesignService($managerRegistry, $request);


        switch ($type) {
            case 'designInfo':
                $result = $this->designService->findByDesignInfo($id);
                return $result;
                break;
        }
    }

    /**
     * Tasarım Modal Dosyalar JSON
     */
    #[Route('/design-files-modal', name: 'app_design_files_modal', methods: ['GET'])]
    public function designFilesModal(Request $request, ManagerRegistry $managerRegistry)
    {
        $id = $request->query->get('col_id');
        $this->designService = new DesignService($managerRegistry, $request);

        $result = $this->designService->findByDesignFiles($id);
        return $result;
    }

    /**
     * Tasarımlar
     */
    #[Route('/design-designs-modal', name: 'app_design_designs_modal', methods: ['GET'])]
    function designDesignsModal(Request $request, ManagerRegistry $managerRegistry)
    {
        $id = $request->query->get('col_id');
        $this->designService = new DesignService($managerRegistry, $request);

        $result = $this->designService->findByDesignDesigns($id);
        return $result;
    }

    /**
     * Tasarım Modal Rüçhan JSON
     */
    #[Route('/design-priority-modal', name: 'app_design_priority_modal', methods: ['GET'])]
    public function designPriorityModal(Request $request, ManagerRegistry $managerRegistry)
    {
        $id = $request->query->get('col_id');
        $this->designService = new DesignService($managerRegistry, $request);

        $result = $this->designService->findByDesignPriority($id);
        return $result;
    }
    /**
     * Tasarım Faturalar JSON
     */
    #[Route('/design-invoice-modal', name: 'app_design_invoice_modal', methods: ['GET'])]
    public function designInvoiceModal(Request $request, ManagerRegistry $managerRegistry)
    {
        $id = $request->query->get('col_id');
        $this->designService = new DesignService($managerRegistry, $request);

        $result = $this->designService->findByDesignInvoices($id);
        return $result;
    }

    /**
     * Tasarım işlemleri JSON
     */
    #[Route('/design-process-modal', name: 'app_design_process_modal', methods: ['GET'])]
    public function designProcessModal(Request $request, ManagerRegistry $managerRegistry)
    {
        $id = $request->query->get('col_id');
        $this->designService = new DesignService($managerRegistry, $request);

        $result = $this->designService->getDesignDetailActions($id);
        return $result;
    }
}
