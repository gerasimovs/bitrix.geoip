<?php

namespace Inteldev\GeoIp\Base;

use Inteldev\GeoIp\Module;

class Geoiplookup extends Module {

    public function getGeoDataByIp($ip) {

        $url = 'http://api.geoiplookup.net/?query={ip_address}';
        $url = str_replace('{ip_address}', $ip, $url);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        $string = curl_exec($ch);

        $data = $this->parse($string);

        return $data;
    }

    public function parse($string) {
        $params = array(
            'country' => 'countrycode', 
            'city' => 'city', 
            'lat' => 'latitude', 
            'lng' => 'longitude'
        );
        $data = $out = array();
        foreach ($params as $key => $param) {
            if (preg_match('#<' . $param . '>(.*)</' . $param . '>#is', $string, $out)) {
                $data[$key] = trim($out[1]);
            }
        }
        return $data;
    }

}
