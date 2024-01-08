<?php

namespace App\Security;

use App\Service\ApizUserService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;

use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Core\Security;


class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public function  __construct(UrlGeneratorInterface $router, private ApizUserService $apizUserService)
    {
        $this->router = $router;
    }

    protected function getLoginUrl(Request $request): string
    {

       return $this->router->generate('app_login');

    }

    public function authenticate(Request $request): Passport
    {
        $password = $request->request->get('_password');
        $username = $request->request->get('_username');
        $csrfToken = $request->request->get('_csrf_token');

        // ... validate no parameter is empty

        // eğer kullanıcı sözleşmesini onaylamadıysa login yapma
        if (!$request->request->get('kullanici_sozlesmesi')) {
            throw new CustomUserMessageAuthenticationException('Kullanıcı sözleşmesini onaylamadınız.');
        }

        // Kullanıcı engelli mi 
        $user = $this->apizUserService->userActiveControl($username, $password);

        if(isset($user['control']) && $user['control']){
        throw new CustomUserMessageAuthenticationException('
        Kullanıcı engellendi. Lütfen bilgi için iletişime geçiniz.
        ');
        // kullanıcının diğer oturumlarını kapat
        }


        

        $data['username'] = $username;
        $data['user_approval'] = true;
        $this->apizUserService->addApizUserLoginLog($data);

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($password),
            [new CsrfTokenBadge('authenticate', $csrfToken)]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {

        // Kullanıcı şifresini en az bir kez değiştirmiş mi
        $changePasswordControl = $this->apizUserService->changePasswordControl($token->getUser());

        if(!$changePasswordControl['status']){
            return new RedirectResponse(
                $this->router->generate('user_app_change_password2')
            );
        }

        if ($target = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($target);
        }
        return new RedirectResponse(
            $this->router->generate('app_main')
        );
    }
    
}
