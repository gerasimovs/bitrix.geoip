<?php

Bitrix\Main\Loader::registerAutoloadClasses(
    "inteldev.geoip",
    array(
        "Inteldev\\GeoIp\\Main" => "lib/main.php",
        //"Inteldev\\GeoIp\\Base\\Maxmind" => "lib/base/maxmind.php",
    )
);