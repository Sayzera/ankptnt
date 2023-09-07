<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Contracts\Translation\TranslatorInterface;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(TranslatorInterface $translator, Request $request): Response
    {
        // $request->setLocale('en');
        // dump($request->getLocale());
        $translated = $translator->trans('Symfony is great');

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }



    #[Route('/blank', name: 'app_blank')]
    public function blank(): Response
    {
        return $this->render('main/blank.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
