<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();

$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);

Loader::includeModule($module_id);

$aTabs = array(
    array(
        "DIV"     => "edit",
        "TAB"     => Loc::getMessage("INTELDEV_GEOIP_OPTIONS_TAB_NAME"),
        "TITLE"   => Loc::getMessage("INTELDEV_GEOIP_OPTIONS_TAB_NAME"),
        "OPTIONS" => array(
            Loc::getMessage("INTELDEV_GEOIP_OPTIONS_TAB_COMMON"),
            array('geobase',
                Loc::getMessage('INTELDEV_GEOIP_OPTIONS_TAB_GEOBASE'),
                null,
                array('selectbox', array(
                        'ipgeobase' => 'IpGeoBase',
                        'geoiplookup' => 'geoiplookup',
                        'maxmind' => 'MaxMind',
                        'elib' => 'Elib.ru',
                    )
                ),
            ),
            array('bots',
                Loc::getMessage('FIELDS_BOTS'),
                null,
                array('textarea',
                    10, 50
                ),
            ),
            array('note' => Loc::getMessage('NOTE_BOTS')),
            Loc::getMessage("MAXMIND_ACCESS"),
            array(
                "maxmind_user",
                Loc::getMessage("MAXMIND_USER"),
                "",
                array("text", 15)
            ),
            array(
                "maxmind_key",
                Loc::getMessage("MAXMIND_KEY"),
                "",
                array("text", 15)
            ),
            Loc::getMessage("ELIB_ACCESS"),
            array(
                "elib_key",
                Loc::getMessage("ELIB_KEY"),
                "",
                array("text", 15)
            ),
        )
    )
);

if($request->isPost() && check_bitrix_sessid()){

    foreach($aTabs as $aTab){

        foreach($aTab["OPTIONS"] as $arOption){

            if(!is_array($arOption)){

                continue;
            }

            if($arOption["note"]){

                continue;
            }

            if($request["apply"]){

                $optionValue = $request->getPost($arOption[0]);

                if($arOption[0] == "switch_on"){

                    if($optionValue == ""){

                        $optionValue = "N";
                    }
                }

                Option::set($module_id, $arOption[0], is_array($optionValue) ? implode(",", $optionValue) : $optionValue);
            }elseif($request["default"]){

                Option::set($module_id, $arOption[0], $arOption[2]);
            }
        }
    }

    LocalRedirect($APPLICATION->GetCurPage()."?mid=".$module_id."&lang=".LANG);
}

$tabControl = new CAdminTabControl(
    "tabControl",
    $aTabs
);

$tabControl->Begin();

?>

<form action="<? echo($APPLICATION->GetCurPage()); ?>?mid=<? echo($module_id); ?>&lang=<? echo(LANG); ?>" method="post">

    <?
    foreach($aTabs as $aTab){

        if($aTab["OPTIONS"]){

            $tabControl->BeginNextTab();

            __AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
        }
    }

    $tabControl->Buttons();
    ?>

    <input type="submit" name="apply" value="<? echo(Loc::GetMessage("INTELDEV_GEOIP_OPTIONS_INPUT_APPLY")); ?>" class="adm-btn-save" />
    <input type="submit" name="default" value="<? echo(Loc::GetMessage("INTELDEV_GEOIP_OPTIONS_INPUT_DEFAULT")); ?>" />

    <?
    echo(bitrix_sessid_post());
    ?>

</form>

<?php
$tabControl->End();
?>