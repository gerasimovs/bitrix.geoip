<?php

namespace Inteldev\GeoIp\Base;

use Inteldev\GeoIp\Module;

class Ipgeobase extends Module {

    public function parse($text)
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
        $url = 'http://ipgeobase.ru:7020/geo/?ip={ip_address}';
        $url = str_replace('{ip_address}', $ip, $url);

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

        $text = iconv("windows-1251", SITE_CHARSET, $text);

        $arData = $this->parse($text);
        return ($arData);
    }

}
