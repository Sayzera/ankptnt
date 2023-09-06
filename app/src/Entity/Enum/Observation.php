<?php
namespace App\Entity\Enum;
abstract class Observation {
    const GET_TOKEN_URL = 'http://37.247.100.212:90/Users/Authenticate';
    const GET_TOKEN_METHOD = 'POST';
    CONST TOKEN_DATA = [
        'username' => 'AnkaraPatent',
        'password' => 'r61watqJVHeIF2BuAzNt'
    ];
    const RESEARCH_SERVICE_URL = 'http://37.247.100.212:90/Trademark/SearchByBulletinStyle';
    const RESEARCH_SERVICE_METHOD = 'POST';

}