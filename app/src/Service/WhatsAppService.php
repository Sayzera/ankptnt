<?php
namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;

class  WhatsAppService {
    private $db;
    public function __construct(private ManagerRegistry $registry, private Security $security)
    {
        $this->db = $this->registry->getConnection();
    }


    public function resultFileStatus($col_application_number)
    {
        $result = $this->db->prepare("SELECT * FROM v_tm_yim_file where col_application_number = :col_application_number")
        ->execute(['col_application_number' => $col_application_number])
        ->fetchAssociative();

        if(!$result) {
            return new JsonResponse([
                'data' => null,
                'success' => false,
                'message' => 'İşlem Başarısız'
            ], 200);
        }

        return new JsonResponse([
            'data' => $result,
            'success' => true,
            'message' => 'İşlem Başarılı'
        ], 200);
    }


}