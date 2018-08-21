<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (!check_bitrix_sessid()) {

    return;
}

if ($errorException = $APPLICATION->GetException()) {

    echo CAdminMessage::ShowMessage(
        $errorException->GetString()
    );

} else {

    echo CAdminMessage::ShowNote(
        Loc::getMessage("INTELDEV_GEOIP_STEP_BEFORE") . " " . Loc::getMessage("INTELDEV_GEOIP_STEP_AFTER")
    );

}

?>

<form action="<? echo($APPLICATION->GetCurPage()); ?>">
    <input type="hidden" name="lang" value="<? echo(LANG); ?>" />
    <input type="hidden" name="id" value="<?=GetModuleID(__FILE__);?>" />
    <input type="hidden" name="install" value="Y" />

    <input type="submit" value="<? echo(Loc::getMessage("INTELDEV_GEOIP_STEP_SUBMIT_BACK")); ?>">
</form>