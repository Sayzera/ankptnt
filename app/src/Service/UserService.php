<?php

namespace App\Service;


use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

class  UserService
{

    private $db;
    public function __construct(private ManagerRegistry $registry, private Security $security)
    {
        $this->db = $this->registry->getConnection();
    }

    public function getTblUserAccount()
    {
        $userId = $this->security->getUser()->getColId();
        $query = 'select t1.*, t2.col_title as firma_adi from tbl_user_account t1
        left join tbl_account t2 on t1.ref_account = t2.col_id
        where t1.ref_user = ' . $userId;

        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();

        $tblUserAccount = $stmt->fetchAllAssociative();

        return $tblUserAccount[0] ?? [];
    }

    public function getApizUsers()
    {
        $query = "
        select 
        concat(max(t1.col_name) , ' ', max(t1.col_surname)) as full_name,
        string_agg(t3.col_title,' -') as company_name,
        max(t1.col_email) as email,
        t1.col_id as id,
        max(t1.col_username) as username,
        case when max(t4.user_id) is null then false 
                else true end  as is_deleted
        
        from tbl_user t1 
        left join tbl_user_account t2 on t2.ref_user = t1.col_id
        left join tbl_account t3 on t3.col_id  = t2.ref_account  
        left join apiz_user_block t4 on t4.user_id = t1.col_id  
        where t1.col_is_deleted = false AND t1.col_username != '' 
        group by t1.col_id     
        ";

        $stmt = $this->db->prepare($query)->execute()->fetchAllAssociative();



        foreach ($stmt as $key => $data) {
            if (empty($stmt[$key]['company_name'])) {
                continue;
            }

            $sirketler =  explode('-', $stmt[$key]['company_name']) ?? [];


            $tempHtml = '<ul>';
            foreach ($sirketler as $key => $item) {
                $tempHtml .= '<li>-' . $item . '</li>';
            }

            $tempHtml .= '</ul>';


            $stmt[$key]['company_name'] = $tempHtml;
        }


        return $stmt;
    }

    public function editUser($data)
    {

        $errors = [];

        if (empty($data['ePosta'])) {
            $errors[] = 'E-posta boş olamaz';
        }


        if (empty($data['username'])) {
            $errors[] = 'Kullanıcı adı boş olamaz';
        }


        if (!empty($errors)) {
            return new JsonResponse([
                'success' => false,
                'errors' => $errors,
                'data' => [],
                "message" => "Hata oluştu"
            ]);
        }


        // şifre boşsa eski şifreyi al
        $query = "UPDATE tbl_user
            SET
                col_username = :col_username,
                col_email = :col_email,
                col_password = COALESCE(:col_password, col_password)
            WHERE col_id = :col_id
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue('col_username', $data['username']);
        $stmt->bindValue('col_email', $data['ePosta']);
        $stmt->bindValue('col_password', $data['password'] == "" ?  null : $data['password']);
        $stmt->bindValue('col_id', $data['userId']);
        $stmt = $stmt->execute();






        if ($stmt) {
            return new JsonResponse([
                'success' => true,
                'errors' => [],
                'data' => [],
                "message" => "Kullanıcı başarıyla güncellendi"
            ]);
        } else {
            return new JsonResponse([
                'success' => false,
                'errors' => [],
                'data' => [],
                "message" => "Kullanıcı güncellenirken hata oluştu"
            ]);
        }
    }

    public function deleteUser($data)
    {
        $userId = $data['userId'];
        $table = "apiz_user_block";


        // exists kontrolü yap
        $query = "SELECT * FROM $table WHERE user_id = $userId";
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $stmt = $stmt->fetchAllAssociative();

        if (!empty($stmt)) {
            // delete yap
            $query = "DELETE FROM $table WHERE user_id = $userId";
            $stmt = $this->db->prepare($query);
            $stmt = $stmt->execute();

            return new JsonResponse([
                'success' => true,
                'errors' => [],
                'data' => [],
                "message" => "Kullanıcı aktif hale getirildi"
            ]);
        }

        $query = "INSERT INTO $table (user_id) VALUES ($userId)";
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();

        if ($stmt) {
            return new JsonResponse([
                'success' => true,
                'errors' => [],
                'data' => [],
                "message" => "Kullanıcı pasif hale getirildi"
            ]);
        } else {
            return new JsonResponse([
                'success' => false,
                'errors' => [],
                'data' => [],
                "message" => "Kullanıcı silinirken hata oluştu"
            ]);
        }
    }

    public function getAuditLog()
    {
        $query = "SELECT * FROM tbl_login_logs ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $stmt = $stmt->fetchAllAssociative();

        return $stmt;
    }

    /**
     * Kullanıcı ekleme aşamasında uygun firmayi seçmesi için 
     */
    public function getAllAccountsForCreateUser($q = "", $page = 1)
    {

        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $where = "";
        $q = strtolower($q);
        if (!empty($q)) {
            $where = " AND lower(col_title) LIKE '%$q%' ";
        }

        $query = "SELECT col_id as id, col_title as text FROM tbl_account WHERE col_title != ''
        AND col_is_deleted = false $where
         group by col_title, col_id 
         limit $perPage offset $offset
          ";
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $stmt = $stmt->fetchAllAssociative();

        if (empty($stmt)) {
            return new JsonResponse([
                'success' => false,
                'errors' => [],
                'items' => [],
                "message" => "Firmalar getirilirken hata oluştu"
            ]);
        }

        return new JsonResponse([
            'success' => true,
            'errors' => [],
            'items' => $stmt,
            "message" => "Firmalar başarıyla getirildi"
        ]);
    }

    public function createUser($request)
    {
        $data = [
            'firma-id' => $request['firma-id'],
            'user-ad' => $request['user-ad'],
            'user-lastname' => $request['user-lastname'],
            'user-name' => $request['user-name'],
            'e-posta' => $request['e-posta'],
            'password-create' => $request['password-create'],
            'password_again' => $request['password_again']
        ];

        // validation
        $errors = [];
        if (empty($data['firma-id'])) {
            $errors[] = 'Firma seçimi yapmalısınız';
        }

        if (empty($data['user-ad'])) {
            $errors[] = 'Ad boş olamaz';
        }

        if (empty($data['user-lastname'])) {
            $errors[] = 'Soyad boş olamaz';
        }

        if (empty($data['user-name'])) {
            $errors[] = 'Kullanıcı adı boş olamaz';
        }

        if (empty($data['e-posta'])) {
            $errors[] = 'E-posta boş olamaz';
        }

        if (empty($data['password-create'])) {
            $errors[] = 'Şifre boş olamaz';
        }

        if (empty($data['password_again'])) {
            $errors[] = 'Şifre tekrarı boş olamaz';
        }

        if ($data['password-create'] != $data['password_again']) {
            $errors[] = 'Şifreler uyuşmuyor';
        }

        if (!empty($errors)) {
            return new JsonResponse([
                'success' => false,
                'errors' => $errors,
                'data' => [],
                "message" => "Hata oluştu"
            ]);
        }



        return  $this->addUser($data);
    }

    public function addUser($data)
    {   
        // firmayı bul
        $sql = "select col_id, col_title from tbl_account where col_id = :col_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('col_id', $data['firma-id']);
        $stmt = $stmt->execute()->fetchAllAssociative();

        if (empty($stmt)) {
            return new JsonResponse([
                'success' => false,
                'errors' => [],
                'data' => [],
                "message" => "Firma bulunamadı"
            ]);
        }

            // Kullanıcı daha önce eklenmiş mi kontrol et
            $sql = "select * from tbl_user where col_surname = :col_surname AND col_email = :col_email";
            $userExist = $this->db->prepare($sql)->execute([
                'col_surname' => $data['user-lastname'],
                'col_email' => $data['e-posta']
            ])->fetchAllAssociative();

            if(!empty($userExist)){
                return new JsonResponse([
                    'success' => false,
                    'errors' => [],
                    'data' => [],
                    "message" => "Kullanıcı daha önce eklenmiş"
                ]);
            }

            $firma_adi = $stmt[0]['col_title'];
            $lastInsertId = $this->db->prepare('SELECT COALESCE(MAX(col_id), 0) + 1 as lastInsertId FROM tbl_user')
            ->execute()->fetchAllAssociative()[0]['lastinsertid'];


            // kullanıcıyı Ekle 
            $sql = "insert into tbl_user (col_id, col_email, col_name, col_password, col_surname, col_username, col_title,col_is_deleted)
        VALUES ( :col_id , :col_email, :col_name, :col_password, :col_surname, :col_username, :col_title, :col_is_deleted)";

            $stmt = $this->db->prepare($sql)
                ->execute([
                    'col_id' => $lastInsertId,
                    'col_email' => $data['e-posta'],
                    'col_name' => $data['user-ad'],
                    'col_password' => $data['password-create'],
                    'col_surname' => $data['user-lastname'],
                    'col_username' => $data['user-name'],
                    'col_title' => $firma_adi,
                    'col_is_deleted' => 0
                ]);


            $insert = "INSERT INTO  
           public.tbl_user_account (
               ref_user,
               ref_account
              )
              VALUES (
                :ref_user,
                :ref_account
              );";

            $stmt =  $this->db->prepare($insert)->execute([
                'ref_user' => $lastInsertId,
                'ref_account' => $data['firma-id']
            ]);

            if ($stmt) {
                return new JsonResponse([
                    'success' => true,
                    'errors' => [],
                    'data' => [],
                    "message" => "Kullanıcı başarıyla eklendi"
                ]);
            } else {
                return new JsonResponse([
                    'success' => false,
                    'errors' => [],
                    'data' => [],
                    "message" => "Kullanıcı eklenirken hata oluştu"
                ]);
            }
    }


    public function updateProfile($data){
        // {
        //     "ad": "Sezer",
        //     "soyad": "Bölük",
        //     "username": "ankarapatent_0",
        //     "email": "white.code.text@gmail.com",
        //     "password": "123456",
        //     "re-password": "123456"
        // }

        $errors = [];

        if (empty($data['ad'])) {
            $errors[] = 'Ad boş olamaz';
        }

        if (empty($data['soyad'])) {
            $errors[] = 'Soyad boş olamaz';
        }

        if (empty($data['username'])) {
            $errors[] = 'Kullanıcı adı boş olamaz';
        }

        if (empty($data['email'])) {
            $errors[] = 'E-posta boş olamaz';
        }

     

        if ($data['update_password'] != $data['update-re-password']) {
            $errors[] = 'Şifreler uyuşmuyor';
        }

        if (!empty($errors)) {
            return new JsonResponse([
                'success' => false,
                'errors' => $errors,
                'data' => [],
                "message" => "Hata oluştu"
            ]);
        }

        // COALESCE(:col_password, col_password)

        $query = "UPDATE tbl_user
            SET
                col_username = :col_username,
                col_email = :col_email,
                col_password = COALESCE(:col_password, col_password),
                col_name = :col_name,
                col_surname = :col_surname
            WHERE col_id = :col_id
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue('col_username', $data['username']);
        $stmt->bindValue('col_email', $data['email']);
        $stmt->bindValue('col_password', $data['update_password'] == "" ?  null : $data['update_password']);
        $stmt->bindValue('col_name', $data['ad']);
        $stmt->bindValue('col_surname', $data['soyad']);
        $stmt->bindValue('col_id', $this->security->getUser()->getColId());

        $stmt = $stmt->execute();
        


        if($stmt){
            return new JsonResponse([
                'success' => true,
                'errors' => [],
                'data' => [],
                "message" => "Kullanıcı başarıyla güncellendi"
            ]);
        }else{
            return new JsonResponse([
                'success' => false,
                'errors' => [],
                'data' => [],
                "message" => "Kullanıcı güncellenirken hata oluştu"
            ]);
        }
        

    }

    public function getProfile() {
        $userId =  $this->security->getUser()->getColId();
        $query = "SELECT * FROM tbl_user WHERE col_id = $userId";
        $stmt = $this->db->prepare($query);
        $stmt = $stmt->execute();
        $stmt = $stmt->fetchAllAssociative();

        if(empty($stmt)){
            return new JsonResponse([
                'success' => false,
                'errors' => [],
                'data' => [],
                "message" => "Kullanıcı bulunamadı"
            ]);
        }

        $stmt = $stmt[0];

        $tbl_user_account = $this->getTblUserAccount();

        $stmt['firma_adi'] = $tbl_user_account['firma_adi'] ?? "";


        return $stmt;


        
    }

    public function getTrademarkObjectionList() {
        $sql = "
        select  t1.itiraz_edilen_marka_adi as iem_marka, t2.col_application_number  as iem_col_application_number, t1.bulletin_no  as bulten_no,
        t1.created_at as itiraz_tarihi, t1.marka_adi as benzer_marka, t1.application_no as benzer_marka_basvuru_numarasi, t3.col_title as markanin_firmasi,
        t1.id as id,
        CASE WHEN DATE(t1.created_at) = CURRENT_DATE OR DATE(t1.created_at) = CURRENT_DATE - INTERVAL '1 day' THEN true ELSE false END as is_new
        from tbl_n_trademark_objections t1  
        left join tbl_trademark_yim_file t2 on t2.col_id = t1.marka_id 
        left join tbl_account t3 on t3.col_id = t2.ref_account_id order by t1.created_at desc

        ";

        $markaItirazListesi =  $this->db->prepare($sql)->execute()->fetchAllAssociative();

        return $markaItirazListesi;
    }
}
