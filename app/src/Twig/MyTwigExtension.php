<?php
// src/Twig/MyTwigExtension.php

namespace App\Twig;

use App\Service\GeneralService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MyTwigExtension extends AbstractExtension
{
    private $myService;

    public function __construct(GeneralService $myService)
    {
        $this->myService = $myService;
    }

    // Bu fonksiyon ile twig içerisinde kullanacağımız fonksiyonları tanımlıyoruz.
    public function getFunctions()
    {
        return [
            new TwigFunction('my_service', [$this, 'getMyService']),
        ];
    }

    public function getMyService()
    {
        return $this->myService;
    }
}
