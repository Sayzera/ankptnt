<?php

namespace App\Controller\Observartion;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ObservationController extends AbstractController
{
    private $templatePath = 'observation/';

    #[Route('/company/observation', name: 'app_company_observation')]
    public function index(): Response
    {
        return $this->render('observation/company-observation.html.twig', [
            'controller_name' => 'ObservationController',
        ]);
    }



}
