<?php

namespace Inteldev\GeoIp;

use Bitrix\Main\Entity;
use Bitrix\Main\Type;
use Bitrix\Main\Localization\Loc;

/**
* Подключаем языковые константы
*/
Loc::loadMessages(__FILE__);

class DataTable extends Entity\DataManager
{

   /**
    * Returns the path to the file containing the class definition.
    *
    * @return string
    */
    public static function getFilePath()
    {
       return __FILE__;
    }

   /**
    * Returns DB table name for entity.
    *
    * @return string
    */
   public static function getTableName()
   {
      return 'inteldev_geoip';
  }

   /**
    * Returns entity map definition.
    *
    * @return array
    */
   public static function getMap()
   {
      return array(
        'id' => array(
            'data_type' => 'integer',
            'primary' => true,
            'autocomplete' => true,
            'title' => Loc::getMessage('INTELDEV_GEOIP_ID_FIELD'),
        ),
        'ip' => array(
            'data_type' => 'text',
            'required' => true,
            'title' => Loc::getMessage('INTELDEV_GEOIP_IP_FIELD'),
        ),
        'country' => array(
            'data_type' => 'text',
            'required' => false,
            'title' => Loc::getMessage('INTELDEV_GEOIP_COUNTRY_FIELD'),
        ),
        'city' => array(
            'data_type' => 'text',
            'required' => false,
            'title' => Loc::getMessage('INTELDEV_GEOIP_CITY_FIELD'),
        ),
        'region' => array(
            'data_type' => 'text',
            'required' => false,
            'title' => Loc::getMessage('INTELDEV_GEOIP_REGION_FIELD'),
        ),
        'district' => array(
            'data_type' => 'text',
            'required' => false,
            'title' => Loc::getMessage('INTELDEV_GEOIP_DISTRICT_FIELD'),
        ),
        'lat' => array(
            'data_type' => 'text',
            'required' => false,
            'title' => Loc::getMessage('INTELDEV_GEOIP_LAT_FIELD'),
        ),
        'lng' => array(
            'data_type' => 'text',
            'required' => false,
            'title' => Loc::getMessage('INTELDEV_GEOIP_LNG_FIELD'),
        ),
        'updated_at' => array(
            'data_type' => 'datetime',
            'title' => Loc::getMessage('INTELDEV_GEOIP_UPDATED_FIELD'),
        ),
     );
  }
}