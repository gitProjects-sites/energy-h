<?
namespace Sepro;

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

class ECommerce
{
    private static $XML = array();
    private static $XMLDocuments = array();
    private static $XMLContragents = array();
    private static $XMLRests = array();

    public static function getObjectXml($filePath)
    {
        if(!is_file($filePath))
            throw new \LogicException(Loc::getMessage('SEPRO_HELPER_COMMERCE_FILE_NOT_AVAILABLE'));

        if(empty(static::$XML))
            static::$XML = \Sepro\Helpers::parseXml($filePath);

        return static::$XML;
    }

    public static function compile($filePath)
    {
        try
        {
            $Xml = self::getObjectXml($filePath);

            if(empty($Xml))
                throw new \InvalidArgumentException(Loc::getMessage('SEPRO_HELPER_COMMERCE_ARRAY_ARE_EMPTY'));

            switch(true)
            {
                case strpos($filePath, 'Documents') !== false:

                    return self::getXMLDocuments();

                    break;

                case strpos($filePath, 'Contragents') !== false:

                    return self::getXMLContragents();

                    break;

                case strpos($filePath, 'rests') !== false:

                    return self::getXMLRests();

                    break;
            }

            return $Xml;
        }
        catch(Exception $e)
        {
            \Sepro\Log::add2log($e->getMessage());

            return false;
        }
    }

    public static function getXMLDocuments()
    {
        if(empty(static::$XML)) return false;

        if(empty(static::$XMLDocuments))
        {
            $result = static::$XML;

            static::$XMLDocuments = $result;
        }

        return static::$XMLDocuments;
    }

    public static function getXMLContragents()
    {
        if(empty(static::$XML)) return false;

        if(empty(static::$XMLContragents))
        {
            $result = static::$XML;

            static::$XMLContragents = $result;
        }

        return static::$XMLContragents;
    }

    public static function getXMLRests()
    {
        if(empty(static::$XML)) return false;

        if(empty(static::$XMLRests))
        {
            $result = static::$XML;

            static::$XMLRests = $result;
        }

        return static::$XMLRests;
    }
}