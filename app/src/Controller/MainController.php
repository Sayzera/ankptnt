<?php

namespace App\Controller;

use App\Service\ApizUserService;
use App\Service\DomesticBrandService;
use App\Service\StatisticsService;
use App\Service\UserService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Security;

class MainController extends AbstractController
{
    public function __construct(
        private Security $security,
        private UserService $userService,
        private ApizUserService $apizUserService,
    ) {
    }
    #[Route('/', name: 'app_main')]
    public function index(TranslatorInterface $translator, Request $request, DomesticBrandService $domesticBrandService): Response
    {
        // $request->setLocale('en');
        // dump($request->getLocale());
        $translated = $translator->trans('Symfony is great');
        $request->getSession()->start();

        $user = $this->security->getUser(); // null or UserInterface, if logged in
        $session = $request->getSession();
        if ($user) {
            $account =   $this->userService->getTblUserAccount();
            $ref_account =  $account['ref_account'];

            // session

            $session->set('firma_adi', $account['firma_adi']);
            $session->set('ref_account', $ref_account);
            //            $this->get('session')->set('ref_account', $ref_account);
        }

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/istatistik', name: 'app_istatistik', methods: ['GET'])]
    public function istatistik(ManagerRegistry $registry, Request $request)
    {
        $statistics = new StatisticsService($registry, $request);
        return $statistics->generalStatistics();
    }



    #[Route('/blank', name: 'app_blank')]
    public function blank(): Response
    {
        return $this->render('main/blank.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
