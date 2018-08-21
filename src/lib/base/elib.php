<?php

namespace Inteldev\GeoIp\Base;

use Inteldev\GeoIp\Module;

class Elib extends Module {

    public function parse($json)
    {
        if (strlen($text) > 0)
        {
            require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/xml.php");
            $objXML = new \CDataXML();
            $res = $objXML->LoadString($text);
            if($res !== false)
            {
                $arRes = $objXML->GetArray();
            }
        }

        $arRes = current($arRes);
        $arRes = $arRes["#"];
        $arRes = current($arRes);

        $ar = Array();

        foreach($arRes as $key => $arVal)
        {
            foreach($arVal["#"] as $title => $Tval)
            {
                $ar[$key][$title] = $Tval["0"]["#"];
            }
        }
        return ($ar[0]);
    }

    public function getGeoDataByIp($ip)
    {
        $url = 'http://geoip.elib.ru/cgi-bin/getdata.pl?sid={site_code}&ip={ip_address}';
        $siteCode = \COption::GetOptionString($this->moduleId, 'elib_key');
        
        $url = str_replace(
            array('{ip_address}', '{site_code}'), 
            array($ip, $siteCode),
            $url
        );

        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_VERBOSE, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $text = curl_exec($ch);

        $errno = curl_errno($ch);
        $errstr = curl_error($ch);
        curl_close($ch);

        if ($errno)
           return false;

        $text = iconv("UTF-8", SITE_CHARSET, $text);

        $arData_ = $this->parse($text);
        if(isset($arData_["Error"]))
          return false;

        $arData = Array(
            "country" => $arData_["Country"],
            "city" => $arData_["Town"],
            "region" => $arData_["Region"],
            "district" => "",
            "lat" => $arData_["Lat"],
            "lng" => $arData_["Lon"]
        );

        return ($arData);
    }

}