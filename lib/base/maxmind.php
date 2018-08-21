<?php

namespace Inteldev\GeoIp\Base;

use Inteldev\GeoIp\Module;

class Maxmind extends Module {

    public function parse($json)
    {
        $objData = json_decode($json);

        $arData = array(
            'ip' => $objData->traits->ip_address,
            'city' => $objData->city->names->ru,
            'country' => $objData->country->iso_code,
            'region' => $objData->subdivisions[0]->names->ru,
            'lat' => $objData->location->latitude,
            'lng' => $objData->location->longitude,
        );

        return $arData;
    }

    public function getGeoDataByIp($ip)
    {
        $url = 'https://geoip.maxmind.com/geoip/v2.1/city/{ip_address}';
        $url = str_replace('{ip_address}', $ip, $url);

        $user = \COption::GetOptionString($this->moduleId, 'maxmind_user');
        $password = \COption::GetOptionString($this->moduleId, 'maxmind_key');

        if (!$user && !$password) {
            throw new \Exception('The user name or key you entered is not correct');
        }

        $options = array(
            CURLOPT_TIMEOUT => 5,
            CURLOPT_HEADER => false,
            CURLOPT_VERBOSE => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $user . ':' . $password,
        );

        $ch = curl_init();
        curl_setopt_array($ch, $options);

        $content = curl_exec($ch);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);

        $arData = $this->parse($content);
        return $arData;
    }

}