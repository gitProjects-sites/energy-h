<?
use Bitrix\Main\Localization\Loc,
    Bitrix\Main\IO\Directory;

Loc::loadMessages(__FILE__);

class sepro_helper extends CModule
{
    var $MODULE_ID = 'sepro.helper';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $PARTNER_NAME;
    var $PARTNER_URI;

    function __construct()
    {
        /** @var array $arModuleVersion */
        include(__DIR__ . '/version.php');
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage("SEPRO_HELPER_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("SEPRO_HELPER_MODULE_DESC");
        $this->PARTNER_NAME = Loc::getMessage("SEPRO_HELPER_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("SEPRO_HELPER_PARTNER_URI");
    }

    public function DoInstall()
    {
        RegisterModule($this->MODULE_ID);
        if(!is_dir($_SERVER["DOCUMENT_ROOT"]."/local/php_interface"))
        {
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/php_interface", $_SERVER["DOCUMENT_ROOT"]."/local/php_interface", true, true);
        }

        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/components", $_SERVER["DOCUMENT_ROOT"]."/local/components", true, true);

        return true;
    }

    public function DoUninstall()
    {
        UnRegisterModule($this->MODULE_ID);
        Directory::deleteDirectory($_SERVER["DOCUMENT_ROOT"]."/local/components/sepro");

        return true;
    }
}
?>