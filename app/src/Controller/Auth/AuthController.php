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
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AuthController extends AbstractController
{
    public function __construct(
        private ApizUserService $apizUserService,
        private UserRepository  $userRepo,
        private Security $security,
        private TokenStorageInterface $tokenStorage
    ) {
    }


    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {


        $error = $authenticationUtils->getLastAuthenticationError();

        if ($request->isMethod('POST')) {
            echo '1';
        }



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
        exit("Burada olmamanız gerekiyor");
        return $this->render('auth/register.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }

    #[Route('/whatsapp-business-find-user', name: 'app_auth_whatsapp_business_find_user', methods: ['POST'])]
    public function whatsappBusinessFindUser(Request $request): Response
    {
        // get post data
        $username = $request->request->get('username');
        $password = $request->request->get('password');

        $data =  $this->apizUserService->whatsappBusinessFindUser($username, $password);

        if ($data) {
            return new JsonResponse([
                'success' => true,
                'message' => "Kullanıcı başarıyla kaydedildi, whatsapp kanalımızı kullanabilirsiniz",
                'data' => $data
            ], 200);
        } else {
            return new JsonResponse([
                'success' => false,
                'message' => 'Kullanıcı bulunamadı, lütfen bilgilerinizi kontrol ediniz',
                'data' => [],
                "postData" => $request->request->all()
            ], 500);
        }
    }


    #[Route('/get-pati-emplyee', name: 'app_auth_get_pati_emplyee')]
    public function getPatiEmplyee()
    {
        exit("Burada olmamanız gerekiyor");
        $tblEmployee = $this->apizUserService->getPatiTblEmployee();
        $this->userRepo->registerUser($tblEmployee);
        // redirect to login page
        return $this->redirectToRoute('app_login');
    }

    #[Route('/get-apiz-users', name: 'app_auth_get_apiz_users')]
    public function getApizUsers()
    {
        exit("Burada olmamanız gerekiyor");
        $tblEmployee = $this->apizUserService->getPatiTblEmployee();
        $this->userRepo->registerUser($tblEmployee);
        // redirect to login page
        return $this->redirectToRoute('app_login');
    }

    /**
     * Otomatik Kullanıcı oluştur 
     */

    #[Route('/create-user-from-excel', name: 'app_auth_create_user')]
    public function xslx()
    {
        //exit("Burada olmamanız gerekiyor");

        $path = 'otomatik-gozlem-ek.xlsx';
        # open the file
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open($path);
        # read each cell of each row of each sheet
        $data = [];
        // tb_userdan son kaydın 1 fazlasını alarak başlat
        $col_id = 3134;

        foreach ($reader->getSheetIterator()  as $rowCounter =>  $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $firma_id = $row->getCells()[0]->getValue();
                $firma_adi = $row->getCells()[1]->getValue();


                $data[] = [
                    'firma_id' => $firma_id,
                    'firma_adi' => $firma_adi,
                ];
            }
        }

        $reader->close();

        // dizinin ilk elemanı başlık olduğu için sil anahtar değerleri değişitr
        array_shift($data);




        foreach ($data as $key => $item) {
            $this->apizUserService->createUser($item['firma_id'], $col_id++, $firma_adi);
        }

        dd('Tüm kullanıcılar aktarıldı');
    }

    // Kullanıcı adlarını değiştir 
    #[Route('/change-username', name: 'app_auth_change_username')]
    public function changeUsername()
    {
        $path = 'otomatik-gozlem-ek.xlsx';
        # open the file
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open($path);
        # read each cell of each row of each sheet
        $data = [];
        $col_id = 1297;

        foreach ($reader->getSheetIterator()  as $rowCounter =>  $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $firma_id = $row->getCells()[0]->getValue();
                $firma_adi = $row->getCells()[1]->getValue();


                $data[] = [
                    'firma_id' => $firma_id,
                    'firma_adi' => $firma_adi,
                ];
            }
        }
        $reader->close();

        // dizinin ilk elemanı başlık olduğu için sil anahtar değerleri değişitr
        array_shift($data);



        foreach ($data as $key => $item) {
            $tbl_user_account = "select * from tbl_user_account where ref_account = {$item['firma_id']}";
            $user_id = $this->apizUserService->findUserAccount($tbl_user_account);


            $this->apizUserService->changeUsername($item['firma_adi'], $user_id);
        }

        dd('Firma isimleri başarıyla değiştirildi');
    }

    /**
     * Otamatik gözlemdeki kullanıcıların şifresini değiştirmel için
     */
    #[Route('/change-password', name: 'app_auth_change_password')]
    public function changePassword()
    {

        exit("Burada olmamanız gerekiyor");
        $this->apizUserService->changePassword();

        dd('Şifreler başarıyla değiştirildi');
    }

    /**
     * Add login logs
     */
    #[Route('/add-login-logs', name: 'app_auth_add_login_logs')]
    public function addLoginLogs()
    {
        // get userınfo
        $data = [];
        $user = $this->security->getUser();
        $data['username'] = $user->getColUsername();
        $data['user_approval'] = true;

        $this->apizUserService->addApizUserLoginLog($data);

        return $this->redirectToRoute('app_main');
    }

    #[Route('/login-error', name: 'app_auth_login_redirect')]
    public function loginRedirect(Request $request)
    {
        return $this->redirectToRoute('app_logout');
    }

    // şifremi unuttum 
    #[Route('/forgot-password', name: 'app_auth_forgot_password')]
    public function forgotPassword(Request $request)
    {
        return $this->render('auth/forgot-password.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }

    // şifremi unuttum
    #[Route('/forgot-password-send', name: 'app_auth_forgot_password_send')]
    public function forgotPasswordSend(Request $request)
    {

        // get json data
        $secret_key = '6LcskkkpAAAAAAwo-nFGezYq0ZVX0gxyuCq3mDb5';
        $recaptcha = $_POST['g-recaptcha-response'];

        $url = 'https://www.google.com/recaptcha/api/siteverify?secret='
            . $secret_key . '&response=' . $recaptcha;

        $response = file_get_contents($url);
        $response = json_decode($response);


        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('forgot-password', $submittedToken)) {
            $_username = $request->request->get('_username');

            if ($response->success == false) {
                $this->addFlash('error', 'Lütfen robot olmadığınızı doğrulayınız');
                return $this->redirectToRoute('app_auth_forgot_password');
            }

            if (empty($_username)) {
                $this->addFlash('error', 'Lütfen kullanıcı adınızı giriniz');
                return $this->redirectToRoute('app_auth_forgot_password');
            }

            ob_start();
            $user = $this->apizUserService->forgotPassword($_username);
            ob_end_clean();
            if (isset($user['status'])  && $user['status']) {
                $this->addFlash('success', 'Şifre sıfırlama linki mail adresinize gönderildi');
                return $this->redirectToRoute('app_auth_forgot_password');
            } else {
                $this->addFlash('error', 'Mail adresiniz sistemde kayıtlı değil');
                return $this->redirectToRoute('app_auth_forgot_password');
            }
        } else {
            dd('token geçersiz');
        }
    }

    #[Route('/reset-password', name: 'app_auth_reset_password', methods: ['GET'])]
    public function resetPassword(Request $request)
    {

        $token = $request->query->get('token');
        // apiz_forgot_password tablosundaki id 
        $id = $request->query->get('id');
        // Kullanıcı Adı 
        $username = $request->query->get('username');
        $error = [];

        if (empty($token) || empty($id) || empty($username)) {
            $error[] = 'Şifre sıfırlama linki geçersiz';
        }

        $control =  $this->apizUserService->tokenControl($token, $id, $username);
        if (!$control['status']) {
            $error[] = 'Şifre sıfırlama linki geçersiz';
        }


        return $this->render('auth/reset-password.html.twig', [
            'controller_name' => 'AuthController',
            'error' => count($error) > 0 ? $error[0] : false,
        ]);
        // http://localhost:8080/reset-password?token=m8FwVrYcvoTswrL&id=18&username=sezer135

    }


    #[Route('/reset-password', name: 'app_auth_reset_password_post', methods: ['POST'])]
    public function resetPasswordPost(Request $request)
    {

        // get json data
        $secret_key = '6LcskkkpAAAAAAwo-nFGezYq0ZVX0gxyuCq3mDb5';
        $recaptcha = $_POST['g-recaptcha-response'];

        $url = 'https://www.google.com/recaptcha/api/siteverify?secret='
            . $secret_key . '&response=' . $recaptcha;

        $response = file_get_contents($url);
        $response = json_decode($response);



        $submittedToken = $request->request->get('csrf-password-token');
        if ($this->isCsrfTokenValid('reset-password', $submittedToken)) {
            $password = $request->request->get('password');
            $password2 = $request->request->get('re-password');
            $id = $request->request->get('id');
            $username = $request->request->get('username');
            $token = $request->request->get('token');


            if ($response->success == false) {
                return $this->redirectToRoute('app_auth_reset_password', [
                    'token' => $token,
                    'id' => $id,
                    'username' => $username,
                    'message' => 'Lütfen robot olmadığınızı doğrulayınız'
                ]);
            }



            if (empty($password) || empty($password2)) {
                return $this->redirectToRoute('app_auth_reset_password', [
                    'token' => $token,
                    'id' => $id,
                    'username' => $username,
                    'message' => 'Lütfen şifre alanlarını doldurunuz'
                ]);
            }

            if ($password != $password2) {
                return $this->redirectToRoute('app_auth_reset_password', [
                    'token' => $token,
                    'id' => $id,
                    'username' => $username,
                    'message' => 'Şifreler uyuşmuyor'
                ]);
            }


            $resetPasswordStatus = $this->apizUserService->resetPassword($password, $id, $username, $token);

            return $this->redirectToRoute(
                'app_login',
                [
                    'message' => 'Şifreniz başarıyla değiştirildi'
                ]
            );
        } else {
            dd('token geçersiz');
        }
    }


    /**
     * Şifre değiştirme ekranı 
     */
    #[Route('/user-security-change-password', name: 'user_app_change_password2')]
    public function changePasswordScreen(Request $request): Response
    {
        return $this->render('auth/change-password.html.twig', [
            'controller_name' => 'AuthController',
            'error' => null
        ]);
    }

    #[Route('/user-change-password-post', name: 'user_app_change_password_post', methods: ['POST'])]
    public function changePasswordPost(Request $request)
    {

        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('change-password', $submittedToken)) {
            $password = $request->request->get('password');
            $password2 = $request->request->get('re-password');

            if (empty($password) || empty($password2)) {
                return $this->redirectToRoute('user_app_change_password2', [
                    'error' => 'Lütfen şifre alanlarını doldurunuz'
                ]);
            }

            if ($password != $password2) {
                return $this->redirectToRoute('user_app_change_password2', [
                    'error' => 'Şifreler uyuşmuyor'
                ]);
            }

            $user = $this->security->getUser();


            $resetPasswordStatus = $this->apizUserService->changePassword2($password, $user);


            if ($resetPasswordStatus['status']) {
                return $this->redirectToRoute(
                    'app_main',
                    [
                        'message' => 'Şifreniz başarıyla değiştirildi'
                    ]
                );
            } else {
                return $this->redirectToRoute('user_app_change_password2', [
                    'error' => $resetPasswordStatus['message']
                ]);
            }
        }
    }
}
