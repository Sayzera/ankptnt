<?php

namespace App\Controller;

use App\Repository\Custom\SystemSettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SystemSettingsController extends AbstractController
{

    #[Route('/system/settings', name: 'app_system_settings')]
    public function index(SystemSettingsRepository $repo): Response
    {
        return $this->render('system_settings/pre-application-registration.html.twig', [
            'controller_name' => 'SystemSettingsController',
        ]);
    }
}
