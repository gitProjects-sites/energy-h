<?
namespace Sepro;

class App
{
    private static $application = false;

    public static function GetInstance()
    {
        if(!static::$application)
        {
            static::$application = $GLOBALS['APPLICATION'];
        }

        return static::$application;
    }

    public static function GetParameter($property_id)
    {
        /*
         * $property_id {int}
         * $variants {array} two argument
         *
         * Example:
         * \Sepro\App::GetInstance()->AddBufferContent(Array("Sepro\App", "GetParameter"), 'PAGE_CLASS');
         * Property must have two parameters "Y" or "N"
         * */

        $return = '';

        $value = self::GetInstance()->GetPageProperty($property_id);

        if(empty($value))
        {
            return false;
        }

        if(in_array($property_id, array('PAGE_CLASS')))
        {
            $return = ' ';
        }

        $return .= $value;

        return $return;

    }

    public static function getTitle()
    {
        $title = '<h1 class="page-title">'.self::GetInstance()->GetTitle().'</h1>';

        return self::GetParameter('HIDE_TITLE') !== 'Y' ? $title : '';
    }

    public static function getSidebar($variants)
    {
        return self::GetParameter('SIDEBAR') == 'Y' ? $variants[0] : $variants[1];
    }

    public static function hideContainer($variants)
    {
        return self::GetParameter('HIDE_CONTAINER') == 'Y' ? $variants[0] : $variants[1];
    }
}