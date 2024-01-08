<?php

namespace App\Controller\Observation;

use App\Repository\ObservationCacheRepository;
use App\Service\DomesticBrandService;
use App\Service\ObservationService;
use App\Service\SearchObservationService;
use App\Validators\ObservationValidators;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Security\Core\Security;

class ObservationController extends AbstractController
{

    // services.yaml içerisinden bu alanlar yönetiliyor 
    private $lastBulletinNo = 428;
    private $endBulletinNo = 430;
    private $selectedBulletinNo = 429;
    private $selectable_elements = [432, 433];
    private $bultenTarihleri = "";

    public function __construct(
        private  ObservationService $observationService,
        private ObservationValidators $observationValidators,
        private Security $security,
        private ContainerBagInterface $params,
        private DomesticBrandService $domesticBrandService
    ) {
        $this->lastBulletinNo = $this->params->get('baslangic_bulten_no');
        $this->endBulletinNo = $this->params->get('bitis_bulten_no');
        $this->selectedBulletinNo = $this->params->get('secili_bulten_no');
        $this->selectable_elements = $this->params->get('secilebilir_elemanlar');
    }
    #[Route(
        '/company/observation/{sinif}/{marka}/{id}/{ydn?}',
        name: 'app_company_observation',
        defaults: ['sinif' => '1', 'marka' => 'test'],
        requirements: [
            'sinif' => '.*',
            'marka' => '.*',
            'id' => '\d+'
        ]
    )]
    public function index(Request $request, DomesticBrandService $domesticBrandService): Response
    {
        /**
         * Yim için 1
         * Ydn için 2
         */
        $_type = $request->query->get('type');

        $id = $request->attributes->get('id');

        $data = [];

        if ($_type == 2 && $id) {
            $result = $domesticBrandService->getYdnTrademark($id);
            if (count($result) > 0) {
                $data['my_brand'] = $result[0];
            } else {
                $data['my_brand'] = [];
            }
        }

        if ($id && $_type == 1) {
            $result = $domesticBrandService->getDomesticBrand($id);
            if (count($result) > 0) {
                $data['my_brand'] = $result[0];
            } else {
                $data['my_brand'] = [];
            }
        }


        $this->bultenTarihleri = $domesticBrandService->getBulletinDate();


        return $this->render('observation/company-observation.html.twig', [
            'controller_name' => 'ObservationController',
            'data' => $data,
            'lastBulletinNo' => $this->lastBulletinNo,
            'endBulletinNo' => $this->endBulletinNo,
            'selectedBulletinNo' => $this->selectedBulletinNo,
            'selectable_elements' => $this->selectable_elements,
            'bulten_tarihleri' => $this->bultenTarihleri
        ]);
    }
    #[Route('/company/observation/observation-list', name: 'app_company_observation_list', methods: ['POST'])]
    public function observationList(Request $request, ObservationCacheRepository $observationCacheRepository): Response
    {
        $marka = $request->request->get('marka-adi');
        $bulten = $request->request->get('bulten-no');
        $arrBultenList = $request->request->get('arrBulletinNo') == "" ? [] : explode(',', $request->request->get('arrBulletinNo'));
        $trademark_id = $request->request->get('trademark_id');
        $siniflar = $request->request->get('siniflar') ?
            explode(',', $request->request->get('siniflar'))
            : [];
        $markaninSiniflari = $siniflar;
        $siniflar = array_map(function ($sinif) {
            return ['No' => $sinif];
        }, $siniflar);


        // validations
        $validation =  $this->observationValidators->checkObservationValidator([
            'marka-adi' => $marka,

            'siniflar' => $siniflar,
            'token' => $request->request->get('token')
        ]);
        if (count($validation) > 0) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Hay aksi! Formda bazı hatalar var.',
                'validations' => $validation
            ], 500);
        }

        // CSRF token kontrolü
        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid('delete-item', $submittedToken)) {
            //  throw new \Exception('Invalid CSRF token');
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Invalid CSRF token',
                'data' => []
            ], 500);
        }


        try {

            $result = "";
            if (count($arrBultenList) > 0) {
                $result = [];
                /**
                 * çoklu gözlem sonuçlarını tutar
                 */
                foreach ($arrBultenList as $key => $item) {

                    // array concat
                    $data = [
                        "IncludeRelatedNiceClassesToTrademarkSearch" => false,
                        "TrademarkName" =>  $this->observationService->getTrademarkById($trademark_id),
                        "BulletinNo" =>  $item,
                        "CalculateTotalRowCountWithoutPaging" => false,
                        "NiceClasses" => $siniflar,
                        'trademark_id' => $trademark_id,
                        'account_id' => $request->getSession()->get('ref_account')

                    ];


                    $webizData = $this->observationService->getObservationList($data);

                    if (isset($webizData['trademarkSearchList'])) {
                        $webizData = $webizData['trademarkSearchList'];
                    }

                    foreach ($webizData as $observationItemKey => $observationItem) {

                        // databas den gelen veri 
                        $result['trademarkSearchList'][] = $observationItem;
                    }
                }
            } else {
                $data = [
                    "IncludeRelatedNiceClassesToTrademarkSearch" => false,
                    "TrademarkName" =>  $this->observationService->getTrademarkById($trademark_id),
                    "BulletinNo" =>  $bulten,
                    "CalculateTotalRowCountWithoutPaging" => false,
                    "NiceClasses" => $siniflar,
                    'trademark_id' => $trademark_id,
                    'account_id' => $request->getSession()->get('ref_account')

                ];
                // Gözlem sonucu
                $webizData = $this->observationService->getObservationList($data);
                $result = [];
                if (isset($webizData['trademarkSearchList'])) {
                    $result['trademarkSearchList'] = $webizData['trademarkSearchList'];
                } else {
                    $result['trademarkSearchList'] = $webizData;
                }
            }

            // Benzerlik orranı
            $result = $this->observationService->benzerlikOrani($result);

            // Bülten tarihleri 
            $result = $this->observationService->bultenTarihleri($this->domesticBrandService->getBulletinDate(), $result);

            // Benzer Sınıflar
            $result = $this->observationService->benzerSiniflar($markaninSiniflari, $result);

            // Benzer kelimeler
            $result['trademarkSearchList'] = array_map(function ($item) use ($marka) {
                $markam =  $marka;
                $similarWords = $item['trademarkName'];
                $benzerHarfler =  $this->observationService->benzerHarfleriRenklendir($markam, $similarWords);
                $item['renklendirilmisMarka'] =mb_convert_encoding( $benzerHarfler, 'UTF-8', 'UTF-8');
                return $item;
            }, $result['trademarkSearchList']);




            return new JsonResponse(
                [
                    'status' => 'success',
                    'message' => 'Gözlem listesi başarıyla getirildi',
                    'data' => $result
                ]
            );
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    #[Route('/company/observation/objection-manuel-eposta', name: 'app_company_observation_objection_manuel_eposta', methods: ['GET'])]
    public function objectionManuelEposta(Request $request, SearchObservationService $searchObservationService, DomesticBrandService $domesticBrandService)
    {
        // itiraz metni oluştur
        $tbl_tm = $searchObservationService->getTblTm('2023/147840');
        $markam = $domesticBrandService->markaBilgim('11348744');

        $html = "
        <p>
            Sayın Yetkili,
        </p>

        <p>
           <b style='color:red'>$tbl_tm[col_application_number] </b>başvuru numaralı <b style='color:red'>$tbl_tm[col_trademark]</b> markasına ilişkin olarak 
           <b style='color:blue'> $markam[col_trademark] </b> markamızın <b style='color:blue'>$markam[col_application_number]</b> başvuru numaralı markamız ile karıştırılma ihtimali bulunmaktadır.  <b style='color:blue'> $markam[col_account_title]</b> firması olarak itirazımızı bildirmek isteriz.</p>
        ";


        $mailResult =  $this->sendMailManuel($html, 'mehmetgulerman@gmail.com');

        dd('mail gönderildi');
    }


    /**
     * Marka için itiraz yap 
     */
    #[Route('/company/observation/objection', name: 'app_company_observation_objection', methods: ['POST'])]
    public function objection(Request $request, SearchObservationService $searchObservationService, DomesticBrandService $domesticBrandService)
    {
        date_default_timezone_set('Europe/Istanbul');
        $marka_id = $request->request->get('trademark_id');
        $bulten = $request->request->get('bulletinNo');
        $siniflar = $request->request->get('classString');
        $trademark = $request->request->get('trademark');
        $itirazEdilenMarka = $request->request->get('itirazEdilenMarka');
        $created_at = new \DateTime();
        $created_at = $created_at->format('Y-m-d H:i:s');
        /**
         * İtiraz edilecek markanın bilgileri
         */
        $application_no  = $request->request->get('applicationNo');
        $tbl_tm = $searchObservationService->getTblTm($application_no);


        /**
         * Kendi markam 
         */
        $markam = $domesticBrandService->markaBilgim($marka_id);

        // itiraz metni oluştur

        $html = "
        <p>
            Sayın Yetkili,
        </p>

        <p>
           <b style='color:red'>$tbl_tm[col_application_number] </b>başvuru numaralı <b style='color:red'>$tbl_tm[col_trademark]</b> markasına ilişkin olarak 
           <b style='color:blue'> $markam[col_trademark] </b> markamızın <b style='color:blue'>$markam[col_application_number]</b> başvuru numaralı markamız ile karıştırılma ihtimali bulunmaktadır.  <b style='color:blue'> $markam[col_account_title]</b> firması olarak itirazımızı bildirmek isteriz.</p>
        ";


        $mailResult =  $this->sendMail($html, $searchObservationService);





        $params = [
            'marka-adi' => $trademark,
            'marka_id' => $marka_id,
            'bulten-no' => $bulten,
            'siniflar' => $siniflar,
            'mailResult' => $mailResult,
            'application_no' => $application_no,
            'created_at' => $created_at,
            'itirazEdilenMarka' => $itirazEdilenMarka,
        ];



        $reuslt =  $this->observationService->objection($params);
        // Mail iletilebilir

        if (!$reuslt) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'message' => 'İtiraz kaydı yapılamadı',
                    'data' => []
                ]
            );
        }


        return new JsonResponse(
            [
                'status' => 'success',
                'message' => 'İtiraz kaydı başarıyla yapıldı',
                'data' => []
            ]
        );
    }

    /**
     * Nerde Kaldım 
     */


    #[Route('/company/observation/nerder-kaldim', name: 'app_company_observation_nerde_kaldim', methods: ['POST'])]
    public function nerde_kaldim(Request $request, SearchObservationService $searchObservationService, DomesticBrandService $domesticBrandService)
    {
        date_default_timezone_set('Europe/Istanbul');
        $marka_id = $request->request->get('trademark_id');
        $bulten = $request->request->get('bulletinNo');
        $siniflar = $request->request->get('classString');
        $trademark = $request->request->get('trademark');
        $itirazEdilenMarka = $request->request->get('itirazEdilenMarka');
        $created_at = new \DateTime();
        $created_at = $created_at->format('Y-m-d H:i:s');
        /**
         * İtiraz edilecek markanın bilgileri
         */
        $application_no  = $request->request->get('applicationNo');

        $params = [
            'marka-adi' => $trademark,
            'marka_id' => $marka_id,
            'bulten-no' => $bulten,
            'siniflar' => $siniflar,
            'application_no' => $application_no,
            'created_at' => $created_at,
            'itirazEdilenMarka' => $itirazEdilenMarka,
        ];



        $reuslt =  $this->observationService->nerde_kaldim($params);
        // Mail iletilebilir

        if (!$reuslt) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'message' => 'İşaret kaldırıldı',
                    'data' => []
                ]
            );
        } else {
            return new JsonResponse(
                [
                    'status' => 'success',
                    'message' => "İşaretlendi",
                    'data' => []
                ]
            );
        }
    }


    /**
     * Gözlem sonucundan dönen markaların eşya listesini getir
     */

    #[Route('/company/observation/get-items', name: 'app_company_observation_get_items', methods: ['POST'])]
    public function getItems(Request $request, SearchObservationService $searchObservationService)
    {
        $applicationno = $request->request->get('applicationno');
        $basvuru_numaram = $request->request->get('basvuru_numarasi');


        $result = $searchObservationService->getItems($applicationno, $basvuru_numaram);

        return $result;
    }


    /**
     * Mail gönder
     */
    public function sendMail($html,  $searchObservationService)
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
            // utf-8
            $mail->CharSet = 'UTF-8';
            //Recipients
            $mail->setFrom('itirazbildirim@ankarapatent.com', 'Ankara Patent');
            $mail->addAddress('itirazbildirim@ankarapatent.com', 'Ankara Patent');     //Add a recipient
            // $mail->addAddress('ellen@example.com');               //Name is optional


            $sendEmail = $searchObservationService->findNilSendMailList($this->getUser()->getColId(), $this->getUser()->getColEmail());

            if ($sendEmail) {
                $mail->addReplyTo($sendEmail['mail']);
            }



            // $mail->addCC('cc@example.com'); // herkesin görünür olduğu 
            // $mail->addBCC('bcc@example.com'); // Gizli

            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Gözlem itirazı';
            $mail->Body    = $html;
            // $mail->AltBody = 'Gözlem itirazı yapılmıştır';

            $mail->send();
        } catch (Exception $e) {
            // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    public function sendMailManuel($html,  $email)
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
            // utf-8
            $mail->CharSet = 'UTF-8';
            //Recipients
            $mail->setFrom('itirazbildirim@ankarapatent.com', 'Ankara Patent');
            $mail->addAddress('itirazbildirim@ankarapatent.com', 'Ankara Patent');     //Add a recipient
            // $mail->addAddress('ellen@example.com');               //Name is optional

            $mail->addReplyTo($email);



            // $mail->addCC('cc@example.com'); // herkesin görünür olduğu 
            // $mail->addBCC('bcc@example.com'); // Gizli

            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Gözlem itirazı';
            $mail->Body    = $html;
            // $mail->AltBody = 'Gözlem itirazı yapılmıştır';

            $mail->send();
        } catch (Exception $e) {
            // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
