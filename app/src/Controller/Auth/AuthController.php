<?php

namespace App\Controller\Auth;

use App\Repository\UserRepository;
use App\Service\ApizUserService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Security;

class AuthController extends AbstractController
{
    public function __construct(
        private ApizUserService $apizUserService,
        private UserRepository  $userRepo,
        private Security $security,
        private UserService $userService
) {
    }


    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('auth/login.html.twig', [
            'controller_name' => 'AuthController',
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    #[Route('/register', name: 'app_auth_register')]
    public function register(): Response
    {
        return $this->render('auth/register.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }


    #[Route('/get-pati-emplyee', name: 'app_auth_get_pati_emplyee')]
    public function getPatiEmplyee()
    {
        $tblEmployee = $this->apizUserService->getPatiTblEmployee();
        $this->userRepo->registerUser($tblEmployee);
        // redirect to login page
        return $this->redirectToRoute('app_login');
    }

    #[Route('/get-apiz-users', name: 'app_auth_get_apiz_users')]
    public function getApizUsers()
    {
        $tblEmployee = $this->apizUserService->getPatiTblEmployee();
        $this->userRepo->registerUser($tblEmployee);
        // redirect to login page
        return $this->redirectToRoute('app_login');
    }
}
