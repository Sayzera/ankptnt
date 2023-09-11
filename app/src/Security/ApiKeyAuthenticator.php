<?php

// src/Security/ApiKeyAuthenticator.php
namespace App\Security;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;


class ApiKeyAuthenticator extends AbstractAuthenticator
{
    public function __construct(UserRepository $repo)
    {
        $this->userRepo = $repo;
    }
    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        /**
         * burada genel bir kontrol yapıp herşey yolunda ise true döndürüyoruz
         * ve authenticate methoduna geçiyoruz
         */
        return (
            $request->attributes->get('_route') === 'app_auth_login_post' && $request->isMethod('POST')
        );
    }

    public function authenticate(Request $request):Passport
    {
        // get all params in form
        $params = $request->request->all();

        if(!isset($params['col_email']) || !isset($params['col_password'])) {
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        // get email and password
        $email = $params['col_email'];
        $password = $params['col_password'];
        $csrf_token = $params['_token'];

        // check database and get user
        $userExists = $this->userRepo->checkUser($email, $password);

        if(!$userExists) {
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        // Eğer kullanıcıyı veritabanından alıyorsanız:
        $userEntity = $this->userRepo->findOneBy(['col_email' => $email]);

    // Kullanıcı nesnesini UsernamePasswordToken'e geçirin
        $token = new UsernamePasswordToken(
            $userEntity,
            $password,
            ['ROLE_USER'] // The names of the firewalls in which this token should be active
        );


        // request set header
        $request->headers->set('X-AUTH-TOKEN', $token);

        $userBadge = new UserBadge(
            $email,
            function (string $userIdentifier) {
                // Kullanıcıyı veritabanından bulun
                $user = $this->userRepo->findOneBy(['col_email' => $userIdentifier]);

                // Kullanıcı bulunamazsa null döndürün
                return $user ?: null;
            }
        );



         return new Passport($userBadge,
            new PasswordCredentials($password),
            [
                // and CSRF protection using a "csrf_token" field
                new CsrfTokenBadge('csrf_token', $csrf_token),
            ]
         );



    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {

        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
