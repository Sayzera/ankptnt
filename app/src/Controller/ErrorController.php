<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ErrorController extends AbstractController
{
    public function show()
    {
        return $this->render('errors/404.html.twig');
    }
}

