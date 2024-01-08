<?php

namespace App\Controller\User;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    public function __construct(private UserService $userService)
    {   
    }

    #[Route('/get-all-accounts', name: 'get_all_accounts', methods:['GET'])]
    public function getAllAccounts(Request $request)
    {   
      $q = $request->query->get('q') ?? '';
      $page = $request->query->get('page') ?? 1;

       return  $this->userService->getAllAccountsForCreateUser($q,$page);
    }


    #[Route('/user/list', name: 'app_user_list')]
    public function index(): Response
    {
        $data = $this->userService->getApizUsers();


        return $this->render('user/user/index.html.twig', [
            'controller_name' => 'UserController',
            'data' => $data,
        ]);
    }

    #[Route('/user/edit/{id}', name: 'app_user_edit', methods:['POST'])]
    public function edit(Request $request)
    {
        $userId = $request->get('id');
        $companyName = $request->get('companyName');
        $ePosta = $request->get('ePosta');
        $username = $request->get('username');
        $password = $request->get('password');

        $data = [
            'userId' => $userId,
            'companyName' => $companyName,
            'ePosta' => $ePosta,
            'username' => $username,
            'password' => $password
        ];


      return $this->userService->editUser($data);
 
    }

 

    #[Route('/user/delete/{id}', name: 'app_user_delete', methods:['POST'])]
    public function delete(Request $request)
    {
        $userId = $request->get('id');

        $data = [
            'userId' => $userId
        ];

        return $this->userService->deleteUser($data);
    }

    #[Route('/audit/log', name: 'audit_log')]
    public function auditLog()
    {
        $data = $this->userService->getAuditLog();

        return $this->render('user/logs/index.html.twig', [
            'controller_name' => 'UserController',
            'data' => $data
        ]);
    }

    #[Route('/theme-settings', name: 'theme_settings', methods:['GET'])]
    public function themeSettings()
    {
        return $this->render('user/user/theme-settings/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/create', name: 'user_create_admin', methods:['POST'])]
    public function createUser(Request $request)
    {
        $request = $request->request->all();
        return $this->userService->createUser($request);

    }

    #[Route('/user/profile', name: 'user_profile', methods:['GET'])]
    public function profile()
    {   
        $data = $this->userService->getProfile();


        return $this->render('user/user/profile/index.html.twig', [
            'controller_name' => 'UserController',
            'data' => $data,
        ]);
    }

    #[Route('/user/update-profile', name: 'user_profile_edit', methods:['POST'])]
    public function updateProfile(Request $request)
    {
        $request = $request->request->all();

        
        return $this->userService->updateProfile($request);
    }

    /**
     * ingilizce çevir
     * marka_itiraz_listesi = marka objection list 
     */
    
     #[Route('/trademark-objection-list', name: 'trademark_objection_list', methods:['GET'])]
        public function trademarkObjectionList(Request $request)
        {
            $data = $this->userService->getTrademarkObjectionList();
            
            return $this->render('user/logs/trademark-objection-list.html.twig', [
                'controller_name' => 'UserController',
                'data' => $data,
            ]);
        }

     

    
}
