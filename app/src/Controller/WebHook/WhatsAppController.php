<?php

namespace App\Controller\WebHook;

use App\Service\WhatsAppService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WhatsAppController extends AbstractController
{

    public function __construct(private WhatsAppService $whatsAppService)
    {
       
    }


    /**
     * Dosya Son Durumunu Getirir
     */
    #[Route('/web/hook/whatsapp/result-file-status', name: 'web_hook_whatsapp_result_file_status', methods: ['POST'])]
    public function result_file_status(Request $request): Response
    {   
        $col_application_number = $request->request->get('col_application_number');
        $result = $this->whatsAppService->resultFileStatus($col_application_number);
        return $result;
    }
}
