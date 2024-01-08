<?php

namespace App\Controller\Mail;

use App\Service\SendMailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SendMailController extends AbstractController
{
    public function __construct(
        private SendMailService $sendMailService
    ) {
    }

    /**
     * Gönderilecek mailleri dbye kayıt eder
     */
    #[Route('/insert-mail-list', name: 'app_insert_mail_list')]
    public function index()
    {
        exit;
      
        $this->sendMailService->insertMailList('otomatik-gozlem-ek.xlsx');
    }


    #[Route('/mail-test-control', name: 'mail_test_control')] 
    public function mailTestControl()
    {
        dd($this->sendMailService->mailTestControl());
    }


    #[Route('/send-mail/5343rt3244412e1243qe132', name: 'app_send_mail')]
    public function sendMail()
    {
        exit;
        $this->sendMailService->sendMail();

        dd('mail gönderildi');
    }

    #[Route('/duyuru-send-mail/5343rt3244412e1243qe132', name: 'app_duyuru_send_mail')]
    public function duyuruSendMail()
    {
        $this->sendMailService->duyuruSendMail();

        dd('duyuru mail gönderildi');
    }

    #[Route('/duyuru-send-mail/manuel/5343rt3244412e1243qe132', name: 'app_duyuru_send_mail_manuel')]
    public function duyuruSendMailManuel()
    {
        exit;
        $this->sendMailService->duyuruSendMailCustom();

        dd('Mail Gönderildi');
    }

    #[Route('/update-mail-list', name: 'app_update_mail_list')]
    public function updateMailList()
    {
        exit;
        $this->sendMailService->updateMailList('otomatik-gozlem-bildirim.xlsx');

        dd('Mail List Güncellendi');
    }

    #[Route('/send-mail/change', name: 'app_send_mail_change')]
    public function sendMailChange()
    {
        exit;
        $this->sendMailService->sendMailChange();
    }

    #[Route('/nil-send-mail-list-update', name: 'nil_send_mail_list')]
    public function nilSendMailList()
    {
        $this->sendMailService->updateNilSendMailList('aktif-mail-listesi.xlsx');
        
    }
}
