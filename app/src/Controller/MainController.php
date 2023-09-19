<?php

namespace App\Controller;
use App\Repository\UserRepository;
use App\Service\ApizUserService;
use App\Service\DomesticBrandService;
use App\Service\UserService;
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
        private UserService $userService
    ) {
    }
    #[Route('/', name: 'app_main')]
    public function index(TranslatorInterface $translator, Request $request, DomesticBrandService $domesticBrandService): Response
    {
        // $request->setLocale('en');
        // dump($request->getLocale());
        $translated = $translator->trans('Symfony is great');


        $user = $this->security->getUser(); // null or UserInterface, if logged in
        $session = $request->getSession();
        if ($user) {
            $account =   $this->userService->getTblUserAccount();
            $ref_account =  $account['ref_account'];

            // session

            $session->set('ref_account', $ref_account);
//            $this->get('session')->set('ref_account', $ref_account);
        }

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
