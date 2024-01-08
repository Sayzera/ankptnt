<?php 
namespace App\Config;


class TrademarkFilter
{
    /**
     * Listede gözükmemesi gereken markalar
     * 
     */
    public static $tradeMarkList = [
        [
            'marka' => 'hdd yayınları',
            'marka_id' => '420550',
        ],
        [
            'marka' => 'final holding',
            'marka_id' => '2032439',
        ],
        [
            'marka' => 'hedef',
            'marka_id' => '420288',
        ],
        [
            'marka' => 'fdd yayınları',
            'marka_id' => '420335',
        ],
        [
            'marka' => 'fnl',
            'marka_id' => '15857157',
        ],
        [
            'marka' => 'hdf koleji 1997',
            'marka_id' => '10544241',
        ],

    ];

    public static function getTradeMarkList()
    {
     $data =  array_column(self::$tradeMarkList, 'marka_id');
     $temp = "";
     
     foreach ($data as $key => $value) {
         $temp .= "'$value',";
     }
      $temp = rtrim($temp,',');
        return $temp;
    }


}