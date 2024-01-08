<?php

// src/EventSubscriber/FormPreSubmitSubscriber.php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FormPreSubmitSubscriber implements EventSubscriberInterface
{


    public function __construct(
        private Security $security
    ) {
    }

    public static function getSubscribedEvents()
    {

        // form submit edilmeden önce çalışır
        return [
            // FormEvents::PRE_SUBMIT => 'onPreSubmit',
            // // herhangi bir request geldiğinde çalışır
            // KernelEvents::REQUEST => 'onKernelRequest',
            // // formlogin submit
            // FormEvents::POST_SUBMIT => 'onPostSubmit',
            // SecurityEvents::INTERACTIVE_LOGIN => 'onAuthenticationSuccess',

        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {

        // post ise

        if ($event->isMainRequest()) {


            return;
        }

        // ...
    }




    public function onAuthenticationSuccess()
    {

        // redirect 
        if (isset($_POST) && !isset($_POST['kullanici_sozlesmesi'])) {

            return  header('Location: /login-error');
            exit;
        } else {
            return header('Location: /add-login-logs');
            exit;
        }
    }
}
