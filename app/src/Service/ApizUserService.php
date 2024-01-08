<?php

namespace App\Service;

use DateInterval;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ApizUserService
{
    private  $db;
    public function __construct(private ManagerRegistry $registry,  private UrlGeneratorInterface $router,)
    {
        $this->db = $this->registry->getConnection('apz');
    }

    public function getPatiTblEmployee(): array
    {
        // get tbl_employee select
        $patiTblEmployeeUsers = $this->db->prepare('SELECT * FROM tbl_employee')->execute();
        $patiUsers =  $patiTblEmployeeUsers->fetchAllAssociative();
        return  $patiUsers;
    }


    public function getApizUsers()
    {
        $users = $this->db->prepare('SELECT * FROM tbl_user ')->execute();
    }


    public function whatsappBusinessFindUser($username, $password)
    {
        $sql = "SELECT t1.*,  t3.col_id as firma_id , t3.col_title as firma_adi FROM tbl_user t1 
        LEFT JOIN tbl_user_account t2 ON t1.col_id = t2.ref_user
        LEFT JOIN tbl_account t3 ON t2.ref_account = t3.col_id
        WHERE t1.col_username = :username AND t1.col_password = :password";
        $stmt = $this->db->prepare($sql)->execute([
            'username' => $username,
            'password' => $password
        ])->fetchAssociative();



        if ($stmt) {
            return $stmt;
        } else {
            return false;
        }
    }

    public function createUser($account_id, $col_id, $firma_adi)
    {

        // 15 haneli random şifre oluştur zamanıda kullan
        $password = $this->generateRandomStringCustom(8);

        $insert = "INSERT INTO 
        public.tbl_user (
            col_id,
            col_is_deleted, 
            col_email, 
            col_name, 
            col_password, 
            col_surname, 
            col_username, 
            col_last_login, 
            col_department_id, 
            col_title, 
            col_version, 
            col_disclaimer_approved,
            col_poll_count, 
            col_poll_date) 
            VALUES (
            $col_id,
            false,
            'gozlem_2',
            'gozlem_1', 
            '$password', 
            'gozlem_1',
            '$firma_adi',
            null,
            null, 
            null,
            null, 
            false, 
            null, 
            null);";

        $stmt =  $this->db->prepare($insert)->execute();
        // get last insert Id 
        $lastInsertId = $col_id;


        // tbl_user_account

        $insert = "INSERT INTO  
         public.tbl_user_account (
             ref_user,
             ref_account
            )
            VALUES (
                $lastInsertId,
                $account_id
            );";

        $stmt =  $this->db->prepare($insert)->execute();
    }

    // benzersi şifre oluşturucu
    public function generateRandomString($length = 15)
    {
        $uzunluk = $length;

        // Şifre karakterleri
        $karakterler = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        // Rastgele şifre oluştur
        $random_sifre = '';
        for ($i = 0; $i < $uzunluk; $i++) {
            $random_sifre .= $karakterler[rand(0, strlen($karakterler) - 1)];
        }

        // Şu anki zamanı al
        $zaman = time();

        // Şifreye zaman bilgisini ekle
        $benzersiz_sifre = $zaman . $random_sifre;

        return $benzersiz_sifre;
    }


    // user account bul 
    public function findUserAccount($sql)
    {
        $stmt = $this->db->prepare($sql)->execute();
        $user = $stmt->fetchAssociative();
        return $user['ref_user'];
    }

    public function changeUsername($username, $user_id)
    {
        $password = $this->generateRandomStringCustom(8);
        $sql = "UPDATE public.tbl_user SET col_password = '$password' WHERE col_id = $user_id";
        $stmt = $this->db->prepare($sql)->execute();
    }

    public function changePassword()
    {
        $sql = "select * from tbl_user where col_name = 'gozlem_1'";
        $stmt = $this->db->prepare($sql)->execute()->fetchAllAssociative();

        foreach ($stmt as $key => $item) {
            $password = $this->generateRandomStringCustom();

            $sql = "UPDATE tbl_user SET col_password = '$password' WHERE col_id = {$item['col_id']}";
            $stmt = $this->db->prepare($sql)->execute();
        }
    }

    public function generateRandomStringCustom($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $randomIndex = mt_rand(0, strlen($characters) - 1);
            $password .= $characters[$randomIndex];
        }

        return $password;
    }

    // Apiz kullanıcı girişi log verileri ekleme
    public function addApizUserLoginLog($data)
    {
        // localizasyon
        $date  = new DateTime();
        $date->add(new DateInterval('PT3H'));
        $data['created_at'] = $date->format('Y-m-d H:i:s');


        $username = $data['username'];
        $user_approval = $data['user_approval'] ? 'true' : 'false';
        $created_at = $data['created_at'];
        $sql = "INSERT INTO tbl_login_logs (username, created_at, user_approval) VALUES ('$username', '$created_at', $user_approval)";
        $stmt = $this->db->prepare($sql)->execute();
        return $stmt;
    }

    public function userActiveControl($username, $password) {
        $user =  $this->db->prepare(
            'select * from tbl_user where col_username = :username and col_password = :password'
        )->execute([
            'username' => $username,
            'password' => $password
        ])->fetchAssociative();
     
        if(!$user) {
            return false;
        } 


        // control 
        $control = $this->db->prepare(
            'select * from apiz_user_block where user_id = :user_id'
        )->execute([
            'user_id' => $user['col_id']
        ])->fetchAssociative();


        return [
            'control' => $control,
            'user' => $user
        ];

    }


    // Şifremi unuttum 
    public function forgotPassword($username)
    {
        $sql = "SELECT col_id,col_email FROM tbl_user WHERE col_username = :username";
        $stmt = $this->db->prepare($sql)->execute([
            'username' => $username
        ])->fetchAssociative();

        if(!$stmt) {
            return [
                'status' => false,
                'message' => 'Kullanıcı bulunamadı'
            ];
        }

        $user_id = $stmt['col_id'];
        $userEmail =$stmt['col_email'];
        $token = $this->generateRandomStringCustom(15);
        // // $date->add(new DateInterval('PT1H'));
        $date = new DateTime();
        $date->add(new DateInterval('P7D'));
        $date = $date->format('Y-m-d H:i:s');
        $token_suresi = $date;
        $mails = [];


        $findUserMailListForUser= "select * from nil_send_mail_list t1 where t1.username = :username";

        $mailList = $this->db->prepare($findUserMailListForUser)
        ->execute([
            'username' => $username
        ])->fetchAllAssociative();


        if(!$mailList) {

            // önce user tablosunda mail var mı kontrol et
            if(filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
                $mails = [$userEmail];
            } else {
            // eğer yoksa user_account tablosunda mail var mı kontrol et
            $sql = "select * from tbl_user_account t1 where t1.ref_user = :user_id";
            $stmt = $this->db->prepare($sql)->execute([
                'user_id' => $user_id
            ])->fetchAssociative();

            if(!$stmt) {
                return [
                    'status' => false,
                    'message' => 'Gönderilecek mail hesabı bulunamadı'
                ];
            }

            $account_id = $stmt['ref_account'];

            $sql = "select * from tbl_account t1 where t1.col_id = :account_id";
            $stmt = $this->db->prepare($sql)->execute([
                'account_id' => $account_id
            ])->fetchAssociative();

            if(!$stmt) {
                return [
                    'status' => false,
                    'message' => 'Gönderilecek mail hesabı bulunamadı'
                ];
            }

            // validate email 
            $mail = filter_var($stmt['col_account_email_address'], FILTER_VALIDATE_EMAIL);

            if($mail) {
                $mails = [$stmt['col_account_email_address']];
            } else {
                return [
                    'status' => false,
                    'message' => 'Gönderilecek mail hesabı bulunamadı'
                ];
            }

            }
          
        }
       if(count($mailList) > 0) {
        $mails = array_column($mailList, 'mail');
       }
    

      // insert token to db

      $sql = 'insert into apiz_forgot_password (token_suresi, token, user_id, kullanici_adi, status, emails) 
      values (
            :token_suresi,
            :token,
            :user_id,
            :kullanici_adi,
            :status,
            :emails )';
      // emails type _varchar
      $stmt = $this->db->prepare($sql)->execute([
          'token_suresi' => $token_suresi,
          'token' => $token,
          'user_id' => $user_id,
          'kullanici_adi' => $username,
          'status' => 0,
          'emails' =>  str_replace('"', "'", json_encode($mails))
      ]);

      $lastInsertId = $this->db->lastInsertId();

      if($stmt) {
   
        $url = $this->router->generate('app_auth_reset_password', [
            'token' => $token,
            'id' => $lastInsertId,
            'username' => $username,
        ], UrlGeneratorInterface::ABSOLUTE_URL);

      }

        if(!$stmt) {
            return [
                'status' => false,
                'message' => 'Şifre sıfırlama işlemi başarısız'
            ];
        }

    
        $html = include('mail-templates/sifremi-unuttum.php');

        foreach($mails as $mailName) {
            $this->sendForgotPasswordMail($html['html'], $mailName);
        }


        return [
            'status' => true,
            'message' => 'Şifre sıfırlama linki mail adresinize gönderildi'
        ];


      
    }


    public function sendForgotPasswordMail($html, $mailname)
    {
        // phpmailer
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = false;                     //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.office365.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'itirazbildirim@ankarapatent.com';                                  //SMTP username
            $mail->Password   = 'm3P1hE!d@TpD';                               //SMTP password
            $mail->SMTPSecure = 'tls';            //Enable implicit TLS encryption
            $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            $mail->CharSet = 'UTF-8';
            $mail->setFrom('itirazbildirim@ankarapatent.com', 'Ankara Patent');
            $mail->addAddress($mailname);     //Add a recipient


            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Ankara Patent - Şifre Sıfırlama';
            $mail->Body    = $html;

            $mail->send();
        } catch (Exception $e) {
            echo $mailname . ' - ' . $e->getMessage();
            // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    public function tokenControl($token, $id, $username) {
        // böyle bir token var mı kontrol et
        $sql = "select * from apiz_forgot_password where token = :token and id = :id and kullanici_adi = :username and status = :status";
        $stmt = $this->db->prepare($sql)->execute([
            'token' => $token,
            'id' =>  $id,
            'username' => $username,
            'status' => 0
        ])->fetchAssociative();

        if(!$stmt) {
            return [
                'status' => false,
                'message' => 'Şifre sıfırlama linki geçersiz'
            ];
        }

        // token süresi geçmiş mi kontrol et
        $token_suresi = $stmt['token_suresi'];
        $date = new DateTime();
        $date = $date->format('Y-m-d H:i:s');

        if($date > $token_suresi) {
            return [
                'status' => false,
                'message' => 'Şifre sıfırlama linki süresi geçmiş'
            ];
        }
        return [
            'status' => true,
            'message' => 'Şifre sıfırlama linki geçerli'
        ];
    }

    public function resetPassword($password, $id, $username, $token){
        $sql = "select * from apiz_forgot_password where id =:id and token =:token and status =:status";
        $stmt = $this->db->prepare($sql)->execute([
            'id' => $id,
            'token' => $token,
            'status' => 0
        ])->fetchAssociative();

        if(!$stmt) {
            return [
                'status' => false,
                'message' => 'Şifre sıfırlama linki geçersiz'
            ];
        }


        $user_id = $stmt['user_id'];
        $sql = "update tbl_user set col_password = :password where col_id = :user_id";
        $stmt = $this->db->prepare($sql)->execute([
            'password' => $password,
            'user_id' => $user_id
        ]);

        if(!$stmt) {
            return [
                'status' => false,
                'message' => 'Şifre sıfırlama işlemi başarısız'
            ];
        }

        $sql = "update apiz_forgot_password set status = :status where id = :id";
        $stmt = $this->db->prepare($sql)->execute([
            'status' => 1,
            'id' => $id
        ]);

        if(!$stmt) {
            return [
                'status' => false,
                'message' => 'Şifre sıfırlama işlemi başarısız'
            ];
        }

        return [
            'status' => true,
            'message' => 'Şifre sıfırlama işlemi başarılı'
        ];

        
    }

    /**
     * Kullanıcı şifresini en az bir kez değiştirmiş mi 
     */
    public function changePasswordControl($user) {

    
        $user_id = $user->getColId();
        $sql = "select * from change_password_logs where user_id = :user_id";
        $stmt = $this->db->prepare($sql)->execute([
            'user_id' => $user_id
        ])->fetchAssociative();

        if(!$stmt) {
            return [
                'status' => false,
                'message' => 'Şifrenizi değiştirmeniz gerekmektedir'
            ];
        }

        return [
            'status' => true,
            'message' => 'Şifrenizi değiştirmeniz gerekmektedir'
        ];
    }


    /**
     * Kullanıcının şifresini en az 1 kez değiştir 
     */

    public function changePassword2($password, $user) {
        $user_id = $user->getColId();

        $sql = "update tbl_user set col_password = :password where col_id = :user_id";
        $stmt = $this->db->prepare($sql)->execute([
            'password' => $password,
            'user_id' => $user_id
        ]);

        if(!$stmt) {
            return [
                'status' => false,
                'message' => 'Şifre değiştirme işlemi başarısız'
            ];
        }

        $sql = "insert into change_password_logs (user_id, status) values (:user_id, :status)";
        $stmt = $this->db->prepare($sql)->execute([
            'user_id' => $user_id,
            'status' => 1
        ]);

        if(!$stmt) {
            return [
                'status' => false,
                'message' => 'Şifre değiştirme işlemi başarısız'
            ];
        }

        return [
            'status' => true,
            'message' => 'Şifre değiştirme işlemi başarılı'
        ];
      
    }
}
