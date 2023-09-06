<?php

namespace App\Controller;

use App\Repository\Custom\LangRepository;
use App\Validators\MainValidators;
use phpDocumentor\Reflection\Types\Void_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/settings')]
class LangMessageController extends AbstractController
{
    private $validator;

    public function __construct()
    {
        $this->validator = new MainValidators();
    }

    #[Route('/add-message', name: 'add_app_lang_message')]
    public function index(LangRepository $repo): Response
    {
        $par = [
            'lang' => 'tr'
        ];
        $data = $repo->getAllLangMessage($par);


        return $this->render('settings/messages/addNewLanguage.html.twig', [
            'controller_name' => 'LangMessageController',
            'data' => $data
        ]);
    }

    #[Route('/add-message-json', name: 'add_app_lang_message_json', methods: ['POST', 'GET'])]
    public function dictionaryListJson(LangRepository $repo): JsonResponse
    {
        $par = [
            'lang' => 'tr'
        ];

        $data = $repo->getAllLangMessage($par);

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Metinler başarıyla getirildi',
            'data' => $data
        ], 200);
    }

    #[Route('/delete-message', name: 'delete_app_lang_message', methods: ['POST', 'GET'])]
    public function deleteMessage(Request $request, LangRepository $repo)
    {
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
            $result =  $repo->deleteLangMessage($request->request->all());

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Metin başarıyla silindi',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    #[Route('/add-lang', name: 'add_app_lang', methods: ['POST', 'GET'])]
    public function addLang(LangRepository $repo, Request $request)
    {
        // verileri al
        $data = $request->request->all();
        // token kontrolü

        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid('delete-item', $submittedToken)) {
            //  throw new \Exception('Invalid CSRF token');
            return json_encode([
                'status' => 'error',
                'message' => 'Invalid CSRF token',
                'data' => []
            ], 500);
        }
        // try catch
        try {
            $langKey = $data['key'];
            $langValu = $data['value'];
            $lang = $data['lang'] = 'tr';
            $data['id'] = 1; // validatorde gerekli olduğu için ekledim burada hiçbir anlam ifade etmiyor

            // form validation
            $validation =  $this->validator->getLangMessageValidator($data);

            if (count($validation) > 0) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Hay aksi! Formda bazı hatalar var.',
                    'validations' => $validation
                ], 500);
            }

            // lang ekle
            $lang = $repo->addLangMessage([
                'key' => $langKey,
                'value' => $langValu,
                'lang' => $lang
            ]);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Metin başarıyla eklendi',
                'data' => $lang
            ], 200);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }


    #[Route('/update-lang', name: 'update_app_lang', methods: ['POST', 'GET'])]
    public function updateLang(LangRepository $repo, Request $request)
    {
        // verileri al
        $data = $request->request->all();
        // token kontrolü

        $submittedToken = $request->request->get('token');
        if (!$this->isCsrfTokenValid('delete-item', $submittedToken)) {
            //  throw new \Exception('Invalid CSRF token');
            return json_encode([
                'status' => 'error',
                'message' => 'Invalid CSRF token',
                'data' => []
            ], 500);
        }
        // try catch
        try {
            $langKey = $data['key'];
            $langValu = $data['value'];
            $lang = $data['lang'] = 'tr';
            $id = $data['id'];

            // form validation
            $validation =  $this->validator->getLangMessageValidator($data);

            if (count($validation) > 0) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Hay aksi! Formda bazı hatalar var.',
                    'validations' => $validation
                ], 500);
            }

            // lang ekle
            $lang = $repo->updateLangMessage([
                'key' => $langKey,
                'value' => $langValu,
                'lang' => $lang,
                'id' =>  $id,
            ]);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Metin başarıyla güncellendi',
                'data' => $lang
            ], 200);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
