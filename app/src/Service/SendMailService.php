<?php

namespace App\Service;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Doctrine\Persistence\ManagerRegistry;
use PHPMailer\PHPMailer\PHPMailer;

class SendMailService
{

    private  $db;

    public function __construct(private ManagerRegistry $registry)
    {
        $this->db = $this->registry->getConnection();
    }

    public function xslx($filePath)
    {
        $path = $filePath;
        # open the file
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open($path);
        # read each cell of each row of each sheet
        $data = [];

        foreach ($reader->getSheetIterator()  as $rowCounter =>  $sheet) {

            foreach ($sheet->getRowIterator() as $row) {
                $mails = [];
                $firma_id = $row->getCells()[0]->getValue();
                $username = $row->getCells()[1]->getValue();

            
                $password =  $this->db->prepare('select * from tbl_user where col_username = :username')->execute(['username' => $username])->fetchAssociative()['col_password'] ?? '';
            
                $firma = $row->getCells()[3]->getValue();

                $mails[] = isset($row->getCells()[4]) ? $row->getCells()[4]->getValue() : ''; // ?? ''
                $mails[] = isset($row->getCells()[5]) ? $row->getCells()[5]->getValue() : '';
                $mails[] = isset($row->getCells()[6]) ? $row->getCells()[6]->getValue() : '';
                $mails[] = isset($row->getCells()[7]) ? $row->getCells()[7]->getValue() : '';
                $mails[] = isset($row->getCells()[8]) ? $row->getCells()[8]->getValue() : '';
                $mails[] = isset($row->getCells()[9]) ? $row->getCells()[9]->getValue() : '';
                $mails[] =  isset($row->getCells()[10]) ? $row->getCells()[10]->getValue() : '';
                $mails[] =  isset($row->getCells()[11]) ? $row->getCells()[11]->getValue() : '';
                $mails[] =  isset($row->getCells()[12]) ? $row->getCells()[12]->getValue() : '';
                $mails[] =  isset($row->getCells()[13]) ? $row->getCells()[13]->getValue() : '';

                $data[] = [
                    'firma_id' => $firma_id,
                    'username' => $username,
                    'password' => $password,
                    'firma' => $firma,
                    'mails' => array_filter($mails, fn ($mail) => $mail !== '')
                ];
            }
        }

        // delet first array
        array_shift($data);

        $reader->close();

        return $data;
    }

    public function updateXslx($filePath)
    {
        $path = $filePath;
        # open the file
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open($path);
        # read each cell of each row of each sheet
        $data = [];

        foreach ($reader->getSheetIterator()  as $rowCounter =>  $sheet) {

            foreach ($sheet->getRowIterator() as $row) {
                $sifre = $row->getCells()[0]->getValue();
                $username = $row->getCells()[1]->getValue();
                $firma_adi = $row->getCells()[2]->getValue();
                $durum = (isset($row->getCells()[3]) && is_numeric(trim($row->getCells()[3])))  
                ? $row->getCells()[3]->getValue() : 1;
         

                $data[] = [
                    'sifre' => $sifre,
                    'username' => $username,
                    'firma_adi' => $firma_adi,
                    'durum' => $durum,
                ];
            }
        }

        // delet first array
        array_shift($data);

        $reader->close();

        return $data;
    }

    public function updateNilSendMailList($filePath)
    {
        $path = $filePath;
        # open the file
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open($path);
        # read each cell of each row of each sheet
        $data = [];

        foreach ($reader->getSheetIterator()  as $rowCounter =>  $sheet) {

            foreach ($sheet->getRowIterator() as $row) {
           
                $mail = $row->getCells()[2]->getValue();
         
                $data[] = [
                     'mail'=> $mail,
                ];
            }
        }


        // delet first array
        array_shift($data);


        
       

        $ekli_olmayanlar = [];

        foreach ($data as $key => $item) {
            try {
                $existMail = $this->db->prepare('SELECT * FROM nil_send_mail_list WHERE mail = :mail')->execute(['mail' => $item['mail']])->fetchAllAssociative();
                
               
                if(count($existMail) > 0 ) {
                    $stmt = $this->db->prepare('
                        UPDATE nil_send_mail_list SET kullanimda_mi = :kullanimda_mi WHERE mail = :mail
                    ')->execute(['kullanimda_mi' => true, 'mail' => $item['mail']]);
                } else {
                    $ekli_olmayanlar[] = $item;
                }
          
            }catch(\Exception $e) {
                continue;
            }
       
        }
        
        dd($data);
      

        $reader->close();

        return $data;
    }



    public function insertMailList($path)
    {
        $mailList = $this->xslx($path);
        $eklenenler = [];

   

        foreach ($mailList  as $item) {
            foreach ($item['mails'] as $key => $mail) {


                // check mail
                $mailCheck = $this->db->prepare('SELECT * FROM nil_send_mail_list WHERE mail = :mail')->execute(['mail' => $mail])->fetchAllAssociative();
                if (count($mailCheck) > 0) {
                    continue;
                }

                $eklenenler[] = $mail;

                // insert 
                $stmt = $this->db->prepare('
                    INSERT INTO nil_send_mail_list (id, firma_id, username, password, firma, mail, mail_durum,kullanimda_mi)
                    VALUES ((SELECT COALESCE(MAX(id), 0) + 1 FROM nil_send_mail_list),:firma_id, :username, :password, :firma, :mail, :mail_durum, :kullanimda_mi)
                ');
                $stmt->bindValue('firma_id', $item['firma_id']);
                $stmt->bindValue('username', $item['username']);
                $stmt->bindValue('password', $item['password']);
                $stmt->bindValue('firma', $item['firma']);
                $stmt->bindValue('mail', $mail);
                $stmt->bindValue('mail_durum', 0);
                $stmt->bindValue('kullanimda_mi', 1);
                

                $stmt->execute();
            }
        }

        dd($eklenenler);
    }


    // Otomatik bildirim mail list düzenleme 
    public function updateMailList($path) {
        $mailList = $this->updateXslx($path);

        $gonderilecek_mailler = array_filter($mailList, fn($item) => $item['durum'] == 1);

        foreach ($gonderilecek_mailler as $item) {
               $mail_user=  $this->db->prepare('SELECT * FROM nil_send_mail_list WHERE username = :username')->execute(['username' => $item['username']])->fetchAllAssociative();

               foreach ($mail_user as $key => $user) {
                    $stmt = $this->db->prepare('
                        UPDATE nil_send_mail_list SET mail_durum = :mail_durum WHERE id = :id
                    ')->execute(['mail_durum' => 0, 'id' => $user['id']]);
                }

        }
  
    }


    public function sendMail()
    {   

       
        // $query = "SELECT * FROM nil_send_mail_list WHERE mail_durum = false";
        $query = "SELECT * FROM nil_send_mail_list WHERE id = 1768";
        $mails = $this->db->prepare($query)->execute()->fetchAllAssociative();

     

        for ($i = 0; $i < count( $mails); $i++) {

            $item = $mails[$i];

            $data = [
                'webSitesiBaglantisi' => 'https://gozlem.ankarapatent.com',
                'kullanimKilavuzu' => 'https://gozlem.ankarapatent.com/apiz-kullanim-kilavuzu.pdf',
                'kullaniciAdi' => $item['username'],
                'sifre' => $item['password'],
                'sirketAdi' => $item['firma'],
                'mail' => $item['mail'],
            ];

            // check mail

            $query = "SELECT * FROM nil_send_mail_list WHERE id = :id AND mail_durum = true";
            $mail = $this->db->prepare($query)->execute(['id' => $item['id']])->fetchAllAssociative();
    
            if (count($mail) > 0) {
                continue;
            }

            // check_mail_format
            if (!filter_var($data['mail'], FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $html = $this->htmlMetni($data);

            $this->sendMailSMTP($html, $data['mail']);


            $query = "UPDATE nil_send_mail_list SET mail_durum = true WHERE id = :id";
            $this->db->prepare($query)->execute(['id' => $item['id']]);

            // 2878 / 300 = 9.5


        }
    }



    public function sendMailSMTP($html, $mailname)
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
            $mail->Subject = 'Ankara Patent - Hesap Bilgisi';
            $mail->Body    = $html;

            $mail->send();
        } catch (Exception $e) {
            echo $mailname . ' - ' . $e->getMessage();
            // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }


    public function htmlMetni($data)
    {
        // Değişkenleri tanımlayın
        $webSitesiBaglantisi = $data['webSitesiBaglantisi'];
        $kullaniciAdi =  $data['kullaniciAdi'];
        $sifre = $data['sifre'];
        $sirketAdi =  $data['sirketAdi'];
        $kullanimKlavuzu = $data['kullanimKilavuzu'];
        // $iletisimBilgileri = "İletişim Bilgileri";

        $emailMetni = "

        <html>
        <head>
        <style>
            /* Kalın metin stilini tanımlayın */
            .bold {
            font-weight: bold;
            }
        </style>
        </head>
        <body>
        <p style='font-weight:bold'>
            Yeni Gözlem Programımız
        </p>

        <p class='bold'>
         $sirketAdi
        </p>

        <p>
        Değerli Müvekkillimiz,
        </p>


        <p>
        Ankara Patent olarak sizlere sunmuş olduğumuz “gözlem hizmeti” çerçevesinde yayınlanan markalar değerlendirilmekte ve markalarınıza benzer görülen başvurular itiraz hakkınızı kullanabilmeniz için siz değerli müvekillerimize ücretsiz olarak düzenli şekilde e-posta olarak teker teker gönderilmekteydi.
        <p>

        <p>
        Ancak ayda iki kere yayınlanan bültenler ve başvuru sayılarındaki ciddi artış sebebi ile bu bildirimleri sizlere mevcut sistemimiz ile iletmemiz giderek zorlaşmış ve artan maliyetler çerçevesinde bu hizmetin mevcut hali ile ücretsiz olarak sunulması imkansız hale gelmiştir.
        </p>

        <p>
        Gelinen bu nokta, uzun yıllardır teknoloji ve altyapı yatırımlarını düzenli olarak sürdüren Ankara Patent’in önemli bir altyapı çalışması yapmasına vesile olmuş, yapılan çalışmalar sonucunda gözlem programımız yeniden yapılandırılmıştır.
        </p>

        <p>
        Gözlem programımıza girişinizi <a href='$webSitesiBaglantisi'>gozlem.ankarapatent.com</a> adresi üzerinden aşağıdaki kullanıcı adı ve şifrenizi kullanarak gerçekleştirebilirsiniz.
        </p>


        <ul>
            <li>
                <span class='bold'>Kullanıcı Adı:</span> <span class='bold'>$kullaniciAdi</span>
            </li>
            <li>
                <span class='bold'>Şifre:</span> <span class='bold'>$sifre</span>
            </li>
        </ul>

        <p>
            Programın kullanımı ile ilgilli olarak bu <a href='$kullanimKlavuzu'>linki</a> tıklayarak kullanım kılavuzuna erişebilir ve her türlü sorunuz için 
            <a href='mailto:apizdestek@ankarapatent.com'>apizdestek@ankarapatent.com</a> mail adresinden bize ulaşabilirsiniz.
        </p>

        <p>
          428. Bülten itibari ile benzer marka bildirimlerinin sizlere yeni gözlem programımız aracılığı ile iletileceğini bildirir,
        </p>

        <p>
        428. Bülten için talimatlarınızı en geç <span class='bold'>10.11.2023</span> tarihine kadar
        </p>


        <p>
            429. Bülten için talimatlarınızı en geç <span class='bold'>20.11.2023</span> tarihine kadar
        </p>

        <p>
             430. Bülten için talimatlarınızı en geç <span class='bold'>01.12.2023</span> tarihine kadar
        </p>

        <p>
        sistem üzerinden iletmenizi rica ederiz. Talimatınız 48 saat içerisinde alındı teyidi ile cevaplandırılacaktır. Bu süre içerisinde herhangi bir teyit maili almadıysanız lütfen bizlerle irtibata geçiniz.
        </p>

        <p>
        Yeni gözlem programının hepimiz için daha efektif bir çalışma ortamı sunacağına inanıyor, gelişen teknoloji ile birlikte her türlü altyapı geliştirme çalışmalarımızın devam edeceğini bildirmekten memnuniyet duyuyoruz.
        </p>

        <p>
        Saygılarımızla,
        </p>
        </body>
        </html>
";

        return  $emailMetni; // E-posta metnini görüntüleme

    }


    // Duyuru 

    public function duyuruSendMail()
    {   
        $query = "SELECT * FROM nil_send_mail_list WHERE mail_durum = false AND kullanimda_mi = true";
        $mails = $this->db->prepare($query)->execute()->fetchAllAssociative();

        $html = $this->htmlMetniDuyuru();

        $data = ['mail' => 'alev.arslan@ankarapatent.com'];

        // $data = ['mail' => 'sezer.boluk@niltekyazilim.com.tr'];

        $this->sendMailSMTPDuyuru($html, $data['mail']);

        dd('ok');

        exit;


        for ($i = 0; $i < 300; $i++) {

            $item = $mails[$i];

            $data = [
                'mail' => $item['mail'],
            ];

            // check mail

            $query = "SELECT * FROM nil_send_mail_list WHERE id = :id AND mail_durum = true";
            $mail = $this->db->prepare($query)->execute(['id' => $item['id']])->fetchAllAssociative();

            if (count($mail) > 0) {
                continue;
            }

            // check_mail_format
            if (!filter_var($data['mail'], FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $html = $this->htmlMetniDuyuru();

            $this->sendMailSMTPDuyuru($html, $data['mail']);


            $query = "UPDATE nil_send_mail_list SET mail_durum = true WHERE id = :id";
            $this->db->prepare($query)->execute(['id' => $item['id']]);


            // 2878 / 300 = 9.5


        }
    }

    // Custom Mail 
    public function duyuruSendMailCustom()
    {   
        
        $mails = [
            'avaysunersoy@gmail.com',
            'fyilmaz@dorukun.com.tr',
            'gerbap@dorukun.com.tr'
        ];

        


        foreach ($mails as $key => $item) {
            $html = $this->htmlMetniDuyuruCustom();


            $this->sendMailSMTPDuyuru($html, $item);
        }

       
    }


    public function sendMailSMTPDuyuru($html, $mailname)
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
            $mail->Subject = 'Ankara Patent - Bülten Duyurusu';
            $mail->Body    = $html;

            $mail->send();
        } catch (Exception $e) {
            echo $mailname . ' - ' . $e->getMessage();
            // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

   

    // public function htmlMetniDuyuru()
    // {
       
    //     $emailMetni = include('mail-templates/duyuru.php');
    //     $emailMetni = $emailMetni['html'];
    //     return  $emailMetni; // E-posta metnini görüntüleme
    // }

    public function htmlMetniDuyuru()
    {
       
        $emailMetni = "

        <html>
        <head>
        <style>
            /* Kalın metin stilini tanımlayın */
            .bold {
            font-weight: bold;
            }
        </style>
        </head>
        <body>

        <p>
        <span class='bold'>Değerli Müvekkillimiz,</span>
        </p>
            
        <p>
        <span class='bold'>435.bülten </span> Apiz gözlem paneline yüklenmiştir.
        </p>
      
        <p>
       <span class='bold'>435.</span>bülten için talimatlarınızı en geç <span class='bold'>27.02.2024</span> tarihine kadar sistem üzerinden iletmenizi rica ederiz.
        </p>
  
        <p>
        Saygılarımızla,
        </p>

        </body>
        </html>
    ";
        return  $emailMetni; // E-posta metnini görüntüleme
    }


    public function htmlMetniDuyuruCustom()
    {

     
        $emailMetni = "

        <html>
        <head>
        <style>
            /* Kalın metin stilini tanımlayın */
            .bold {
            font-weight: bold;
            }
        </style>
        </head>
        <body>
            
        <p class='bold'>
        Yeni Gözlem Programımız
        </p>
        <p class='bold'>
        Doruk Unlu Mamuller Sanayi ve Perakende Hizmetleri Anonim Şirketi
        </p>


        <p>
        Değerli Müvekkillimiz,
        </p>

        <p>
        Ankara Patent olarak sizlere sunmuş olduğumuz “gözlem hizmeti” çerçevesinde yayınlanan markalar değerlendirilmekte ve markalarınıza benzer görülen başvurular itiraz hakkınızı kullanabilmeniz için siz değerli müvekillerimize ücretsiz olarak düzenli şekilde e-posta olarak teker teker gönderilmekteydi.
        </p>

        <p>
        Ancak ayda iki kere yayınlanan bültenler ve başvuru sayılarındaki ciddi artış sebebi ile bu bildirimleri sizlere mevcut sistemimiz ile iletmemiz giderek zorlaşmış ve artan maliyetler çerçevesinde bu hizmetin mevcut hali ile ücretsiz olarak sunulması imkansız hale gelmiştir.
        </p>

        <p>
        Gelinen bu nokta, uzun yıllardır teknoloji ve altyapı yatırımlarını düzenli olarak sürdüren Ankara Patent’in önemli bir altyapı çalışması yapmasına vesile olmuş, yapılan çalışmalar sonucunda gözlem programımız yeniden yapılandırılmıştır.
        </p>

        <p>
        Gözlem programımıza girişinizi <a href='https://gozlem.ankarapatent.com'> gozlem.ankarapatent.com</a> adresi üzerinden aşağıdaki kullanıcı adı ve şifrenizi kullanarak gerçekleştirebilirsiniz.
        </p>

        
        <ul>
                <li> 
                <span class='bold'>Kullanıcı Adı</span>:doruk_unlu_mamul
                </li>
                <li>
                <span class='bold'>Şifre</span>:zUMh7mXU
            </li>
        </ul>

        <p>
        Programın kullanımı ile ilgilli olarak bu <a href='https://gozlem.ankarapatent.com/apiz-kullanim-kilavuzu.pdf'>linki</a> tıklayarak kullanım kılavuzuna erişebilir ve her türlü sorunuz için <a href='mailto:apizdestek@ankarapatent.com'>apizdestek@ankarapatent.com</a> mail adresinden bize ulaşabilirsiniz.
        </p>


        <p>
        431. Bülten itibari ile benzer marka bildirimlerinin sizlere yeni gözlem programımız aracılığı ile iletileceğini bildirir,
        </p>
            
        <ul>
            <li>431. Bülten için talimatlarınızı en geç <span class='bold'>21.12.2023</span> tarihine kadar</li>
            <li>432. Bülten için talimatlarınızı en geç <span class='bold'>08.01.2024</span> tarihine kadar</li>
            <li>433. Bülten için talimatlarınızı en geç <span class='bold'>22.01.2024</span> tarihine kadar</li>
            <li>434. Bülten için talimatlarınızı en geç <span class='bold'>06.02.2024</span> tarihine kadar</li>
        </ul>

        <p>
        sistem üzerinden iletmenizi rica ederiz. Talimatınız 48 saat içerisinde alındı teyidi ile cevaplandırılacaktır. Bu süre içerisinde herhangi bir teyit maili almadıysanız lütfen bizlerle irtibata geçiniz.
        </p>

        <p>
        Yeni gözlem programının hepimiz için daha efektif bir çalışma ortamı sunacağına inanıyor, gelişen teknoloji ile birlikte her türlü altyapı geliştirme çalışmalarımızın devam edeceğini bildirmekten memnuniyet duyuyoruz.
        </p>
       
        <p>
        Saygılarımızla,
        </p>
        </body>
        </html>
    ";
        return  $emailMetni; // E-posta metnini görüntüleme
    }

    // public function yy()
    // {
    //     $query = "SELECT * FROM nil_send_mail_list WHERE mail_durum = false";
    //     $mails = $this->db->prepare($query)->execute()->fetchAllAssociative();


  


    //     foreach ($mails  as $item) {

    //         $tbl_user = "select * from tbl_user where col_username = :username";
    //         $user = $this->db->prepare($tbl_user)->execute(['username' => $item['username']])->fetchAssociative();


    //         // update password 
    //         $query = "UPDATE tbl_user SET col_password = :password WHERE col_username = :username";
    //         $this->db->prepare($query)->execute(['password' => $item['password'], 'username' => $item['username']]);
    //     }

    //     dd('değişti');
    // }


    public function sendMailChange()
    {
        exit;
        $query = "SELECT * FROM nil_send_mail_list WHERE mail_durum = false";
        $mails = $this->db->prepare($query)->execute()->fetchAllAssociative();

        foreach ($mails as $item) {
            $tbl_user = "select * from tbl_user where col_username = :username";
            $user = $this->db->prepare($tbl_user)->execute(['username' => $item['username']])->fetchAssociative();


            // update password
            $query = "UPDATE nil_send_mail_list SET password = :password WHERE username = :username";
            $this->db->prepare($query)->execute(['password' => $user['col_password'], 'username' => $item['username']]);
        }

        dd($mails);
    }


    public function mailTestControl() {
        return '19/12/2023';
    }



}
