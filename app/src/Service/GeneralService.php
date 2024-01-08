<?php

namespace  App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;


class GeneralService
{


    public $name = 'Sezer';


    public function getKeyword($keyword)
    {
        return [
            'keyword' => $keyword,
            'name' => $this->name
        ];
    }

    public function setSession($name, $value)
    {
        $_SESSION[$name] = $value;
    }


    public function getSession($name)
    {
        dump($_SESSION[$name]);
    }
}
