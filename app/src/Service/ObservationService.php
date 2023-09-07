<?php
namespace  App\Service;

use App\Entity\Enum\Observation;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @description : Gözlem listesi için gerekl olan servis isteklerini yapan sınıf
 */
class ObservationService {
    public function __construct(
        private HttpClientInterface $client,
    ) {
    }

    // Get Token
    public function getToken(){
        $url = Observation::GET_TOKEN_URL;
        $data = Observation::TOKEN_DATA;
        $method = Observation::GET_TOKEN_METHOD;

        $response = $this->client->request(
            $method,
            $url,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode($data),
            ]
        );
        return $response->toArray()['token'];
    }
    // Get Observation List
    public  function getObservationList($data) {
        $url = Observation::RESEARCH_SERVICE_URL;
        $method = Observation::RESEARCH_SERVICE_METHOD;
        $token = $this->getToken();


        $response = $this->client->request(
            $method,
            $url,
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode($data),
            ],

        );

        return $response->toArray();
    }

}