<?
namespace Sepro;

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\Service\GeoIp,
    \Bitrix\Main\Application,
    \Bitrix\Main\Web\Cookie;

Loc::loadMessages(__FILE__);

class User
{
    private static $user = false;
    private static $city = false;
    private static $cookies = array();
    private static $requests = array();
    private static $ip = false;
    private static $agent = false;
    private static $geoCode = '69MH6XT6';

    public static function getInstance()
    {
        if(!static::$user)
        {
            static::$user = $GLOBALS['USER'];
        }

        return static::$user;
    }

    public static function getRequest()
    {
        if(empty(static::$requests))
        {
            /* ПОДУМАТЬ */

            static::$requests = \Bitrix\Main\Context::getCurrent()->getRequest();
        }

        return static::$requests;
    }

    public static function getUserCity()
    {
        if(self::initBots())
            return false;

        if(!static::$city)
        {
            self::getUserIP();

            if(empty(static::$ip)) return false;

            if(class_exists('\Bitrix\Main\Service\GeoIp\Manager'))
            {
                $obData = GeoIp\Manager::getDataResult(static::$ip, LANGUAGE_ID);

                static::$city = $obData->getGeoData()->cityName;
            }

            if(!static::$city)
            {
                if(!function_exists('curl_init')) return false;

                $Xml = \Sepro\Helpers::dataCurl("http://ipgeobase.ru:7020/geo/?ip=".static::$ip);

                if (!$Xml)
                {
                    $Xml = \Sepro\Helpers::dataCurl("http://geoip.elib.ru/cgi-bin/getdata.pl?sid=".static::$geoCode."&ip=".static::$ip."&hex=3ffd");
                }

                $obData = \Sepro\Helpers::parseXml($Xml);

                if ($obData)
                {
                    foreach ($obData->ip as $obValue)
                    {
                        if (!empty($obValue->city))
                        {
                            static::$city = (string) $obValue->city;
                            break;
                        }

                        if (!empty($obValue->Town))
                        {
                            static::$city = (string) $obValue->Town;
                            break;
                        }
                    }
                }
            }
        }

        return static::$city;
    }

    // Searching Robots
    private static function initBots()
    {
        if(!static::$agent)
        {
            static::$agent = \Sepro\Helpers::mstripos(
                $_SERVER['HTTP_USER_AGENT'],
                array(
                    'rambler',
                    'googlebot',
                    'ia_archiver',
                    'Wget',
                    'WebAlta',
                    'MJ12bot',
                    'aport',
                    'yahoo',
                    'msnbot',
                    'mail.ru',
                    'alexa.com',
                    'Baiduspider',
                    'Speedy Spider',
                    'abot',
                    'Indy Library'
                )
            );
        }

        return static::$agent;
    }

    public static function getUserIP()
    {
        if(!static::$ip)
        {
            static::$ip = class_exists('\Bitrix\Main\Service\GeoIp\Manager') ? GeoIp\Manager::getRealIp() : self::getUserHostAddress('REMOTE_ADDR');
        }

        return static::$ip;
    }

    public static function getCookie($cookie = false)
    {
        if(!$cookie) return false;

        if(empty(static::$cookies[$cookie]))
        {
            if(!empty($_COOKIE[$cookie]))
            {
                static::$cookies[$cookie] = $_COOKIE[$cookie];
            }

            if(empty(static::$cookies[$cookie]))
            {
                static::$cookies[$cookie] = Application::getInstance()->getContext()->getRequest()->getCookie($cookie);
            }
        }

        return static::$cookies[$cookie];
    }

    public static function getUserHostAddress($ip_param_name = false)
    {
        $arTrustedIPS = array(
            'GEOIP_ADDR',
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR'
        );

        if (!empty($_SERVER[$ip_param_name]) && filter_var($_SERVER[$ip_param_name], FILTER_VALIDATE_IP))
            return $_SERVER[$ip_param_name];

        foreach($arTrustedIPS as $ipParamName)
        {
            if($ip_param_name === $ipParamName) continue;

            if(!empty($_SERVER[$ipParamName]) && filter_var($_SERVER[$ipParamName], FILTER_VALIDATE_IP))
                return $_SERVER[$ipParamName];
        }

        return false;
    }
}