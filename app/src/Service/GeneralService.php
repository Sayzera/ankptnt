<?php

namespace  App\Service;
class GeneralService {

    public $name = 'Sezer';


    public function getKeyword($keyword) {
        return [
            'keyword' => $keyword,
            'name' => $this->name
        ];
    }




}