<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;

/**
* Подключаем языковые константы
*/
Loc::loadMessages(__FILE__);

/**
* Инсталляция модуля inteldev_geoip
*/
class inteldev_geoip extends CModule
{
    public $errors;
    public $MODULE_ID = 'inteldev.geoip';
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;
    public $PARTNER_URI;
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;

    /**
    * Инициализация модуля для страницы «Управление модулями»
    */
    public function __construct()
    {

        $this->MODULE_ID           = str_replace('_', '.', get_class($this));
        $this->MODULE_NAME         = Loc::getMessage('INTELDEV_GEOIP_NAME');
        $this->MODULE_DESCRIPTION  = Loc::getMessage('INTELDEV_GEOIP_DESCRIPTION');
        $this->PARTNER_NAME        = Loc::getMessage('INTELDEV_GEOIP_PARTNER_NAME');
        $this->PARTNER_URI         = Loc::getMessage('INTELDEV_GEOIP_PARTNER_URI');

        if (file_exists(__DIR__ . '/version.php')) {

            $arModuleVersion = array();

            include_once(__DIR__ . '/version.php');

            $this->MODULE_VERSION      = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
            
        }

    }

    /**
    * Устанавливаем модуль
    *
    * @return bool
    */ 
    public function DoInstall()
    {

        global $APPLICATION;

        $version = defined('SM_VERSION') ? SM_VERSION : ModuleManager::getVersion('main');

        if (CheckVersion($version, '14.00.00')) {

            $this->InstallFiles();
            $this->InstallDB();
            $this->InstallEvents();

            ModuleManager::registerModule($this->MODULE_ID);

        } else {

            $APPLICATION->ThrowException(
                Loc::getMessage('INTELDEV_GEOIP_INSTALL_ERROR_VERSION')
            );

        }

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage('INTELDEV_GEOIP_INSTALL_TITLE') . ' "' . Loc::getMessage('INTELDEV_GEOIP_NAME') . '"',
            __DIR__ . '/step.php'
        );
        
        return true;
    }

    /**
    * Удаляем модуль
    *
    * @return bool
    */ 
    public function DoUninstall()
    {

        global $APPLICATION;

        $this->UnInstallFiles();
        $this->UnInstallDB();
        $this->UnInstallEvents();

        ModuleManager::unRegisterModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage('INTELDEV_GEOIP_UNINSTALL_TITLE') . ' "' . Loc::getMessage('INTELDEV_GEOIP_NAME') . '"',
            __DIR__ . '/unstep.php'
        );
        
        return true;
    }

    /**
    * Добавляем почтовые события
    *
    * @return bool
    */ 
    public function InstallEvents() 
    { 
        return true;
    }

    /**
    * Удаляем почтовые события
    *
    * @return bool
    */ 
    public function UnInstallEvents() 
    {
        return true;
    }

    /**
    * Копируем файлы административной части
    *
    * @return bool
    */ 
    public function InstallFiles() 
    { 
        return true;
    }

    /**
    * Удаляем файлы административной части
    *
    * @return bool
    */ 
    public function UnInstallFiles() 
    { 
        return true;
    }

    /**
    * Добавляем таблицы в БД
    *
    * @return bool
    */ 
    public function InstallDB() 
    { 
        global $DB;
        $this->errors = false;
        $this->errors = $DB->RunSQLBatch(__DIR__ . '/db/install.sql');
        if (!$this->errors) {
            return true;
        } else
            return $this->errors;
    }

    /**
    * Удаляем таблицы из БД
    *
    * @return bool
    */ 
    public function UnInstallDB() 
    { 
        global $DB;
        $this->errors = false;
        $this->errors = $DB->RunSQLBatch(__DIR__ . '/db/uninstall.sql');
        Option::delete($this->MODULE_ID);
        if (!$this->errors) {
            return true;
        } else
            return $this->errors;
    }

}