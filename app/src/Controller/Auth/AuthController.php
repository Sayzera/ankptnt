<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route('/auth', name: 'app_auth_auth')]
    public function index(): Response
    {
        return $this->render('auth/pre-application-registration.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }

    #[Route('/login', name: 'app_auth_login')]
    public function login(): Response
    {
        return $this->render('auth/login.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }


    #[Route('/register', name: 'app_auth_register')]
    public function register(): Response
    {
        return $this->render('auth/register.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }
    
}
