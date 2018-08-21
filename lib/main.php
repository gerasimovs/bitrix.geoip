<?php

namespace Inteldev\GeoIp;

use Inteldev\GeoIp\DataTable;
use Inteldev\GeoIp\Module;

class Main extends Module {

    public function __construct()
    {
        $geobase = \COption::GetOptionString($this->moduleId, 'geobase');

        if ($geobase == false) {
            throw new \Exception('GeoIp not selected!');
        }

        $geoclass = __NAMESPACE__ . '\\Base\\' . ucfirst($geobase);

        $this->geo = new $geoclass();
        $this->ip = $this->getIp();
    }

    public function getIp()
    {
        $ip = getenv('HTTP_CLIENT_IP');
        if ($ip && strcasecmp($ip, 'unknown'))
            return $ip;

        $ip = getenv('HTTP_X_FORWARDED_FOR');
        if ($ip && strcasecmp($ip, 'unknown'))
            return $ip;

        $ip = getenv('REMOTE_ADDR');
        if ($ip && strcasecmp($ip, 'unknown'))
           return $ip;

        return 'unknown';
    }

    public function getGeoData()
    {

        if ($this->checkBots()) return false;

        if (!filter_var($this->ip, FILTER_VALIDATE_IP)) return false;

        $arData = $this->getCityByIp($this->ip);

        if (empty($arData)) {

            $arData = $this->geo->getGeoDataByIp($this->ip);

            if (is_array($arData) && isset($arData['city'])) {
                $this->addCityById($this->ip, $arData);
            }

        } elseif (is_array($arData) && isset($arData['id'])) {

            $iDataTimestamp = $arData['updated_at']->getTimestamp();
            $iNowTimestamp = (new \Bitrix\Main\Type\DateTime)->getTimestamp();

            $iDeltaTemestamp = $iNowTimestamp - $iDataTimestamp;
            $iUpdateInterval = 60 * 60 * 24 * 30;

            if ($iDeltaTemestamp > $iUpdateInterval) {

                $arData = $this->geo->getGeoDataByIp($this->ip);
                
                if (is_array($arData) && isset($arData['city'])) {
                    $this->updateCityById($arData['id'], $arData);
                }
            }

        }

        return $arData;
    }
    
    public function checkBots()
    {
        if (empty(getenv('HTTP_USER_AGENT'))) {
            return false;
        }

        $botsOption = \COption::GetOptionString($this->moduleId, 'bots');
        $bots = preg_split('/\r\n|[\r\n]/', $botsOption);

        foreach($bots as $bot) {
            if(stripos(getenv('HTTP_USER_AGENT'), $bot) !== false){
                return $bot;
            }
        }

        return false;
    }

    public function getCityByIp($ip) {

        if (CheckVersion($version, '15.00.00')) {
            $result = DataTable::getList(
                    array(
                        'select' => array('*'),
                        'filter' => array('=ip' => $ip)
            ));
        } else {
            $connection = \Bitrix\Main\Application::getConnection();
            $dataTable = DataTable::getTableName();
            $sql = "SELECT * FROM `{$dataTable}` WHERE ip = '{$ip}';";
            $result = $connection->query($sql);
        }


        return $result->fetch();
    }

    public function addCityById($ip, $data)
    {
        $result = DataTable::add(array(
            'ip' => $ip,
            'city' => $data['city'],
            'country' => isset($data['country']) ? $data['country'] : '',
            'region' => isset($data['region']) ? $data['region'] : '',
            'district' => isset($data['district']) ? $data['district'] : '',
            'lat' => isset($data['lat']) ? $data['lat'] : '',
            'lng' => isset($data['lng']) ? $data['lng'] : '',
        ));

        if ($result->isSuccess())
        {
            return $result->getId();
        } else
            return false;
    }

    public function updateCityById($id, $data)
    {
        $res = DataTable::update($id, array(
            'city' => $data['city'],
            'country' => isset($data['country']) ? $data['country'] : '',
            'region' => isset($data['region']) ? $data['region'] : '',
            'district' => isset($data['district']) ? $data['district'] : '',
            'lat' => isset($data['lat']) ? $data['lat'] : '',
            'lng' => isset($data['lng']) ? $data['lng'] : '',
            'updated_at' => date('Y-m-d'),
        ));

    }
}
