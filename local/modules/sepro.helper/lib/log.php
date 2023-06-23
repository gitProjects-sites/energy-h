<?
namespace Sepro;

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Log
{
    const logSize = 157864;

    private static $logDir = false;

    public static function init()
    {
        if(!static::$logDir)
        {
            static::$logDir = self::SetDir('logs', DOCUMENT_ROOT.'/upload/');
        }

        $logFilePath = false;
        $arFiles = scandir(static::$logDir);

        if(empty($arFiles))
        {
            throw new \InvalidArgumentException(Loc::getMessage('SEPRO_HELPER_LOG_UPLOAD_NOT_AVAILABLE'));
        }

        sort($arFiles);

        foreach($arFiles as $file)
        {
            if(in_array($file, array('.', '..')))
                continue;

            if(self::checkFile($file))
                $logFilePath = static::$logDir.'/'.$file;
        }

        if(!$logFilePath)
        {
            $logFilePath = static::$logDir.'/log_'.date('Ymd_His').'.txt';
        }

        return $logFilePath;
    }

    private static function SetDir($dirName, $dirPath)
    {
        if(!is_dir($dirPath))
        {
            throw new \InvalidArgumentException(Loc::getMessage('SEPRO_HELPER_LOG_UPLOAD_NOT_AVAILABLE'));
        }

        $logDir = $dirPath.'/'.$dirName;

        if(!is_dir($logDir) && !mkdir($logDir, 0755))
        {
            throw new \LogicException(Loc::getMessage('SEPRO_HELPER_LOG_DIR_NOT_ADD'));
        }

        return $logDir;
    }

    private function checkFile($file){

        $logFilePath = static::$logDir.'/'.$file;

        if(!is_file($logFilePath))
        {
            throw new \LogicException(Loc::getMessage('SEPRO_HELPER_LOG_DIR_HAVE_BAD_FILE'));
        }

        if(!\Sepro\Helpers::mstripos($file, array('log_', '.txt')))
        {
            if(!unlink($logFilePath))
            {
                throw new \LogicException(Loc::getMessage('SEPRO_HELPER_LOG_DIR_HAVE_BAD_FILE'));
            }

            return false;
        }

        if(filesize($logFilePath) > self::logSize)
        {
            return false;
        }

        return true;
    }

    private static function getContent($content)
    {
        $text = '';

        switch(gettype($content))
        {
            case 'object':
            case 'array':
                $content = (array) $content;
                foreach($content as $key => $value)
                {
                    $text .= $key." => ".self::getContent($value)."\r";
                }
                break;
            case 'boolean':
                $text .= $content ? 'true' : 'false';
                break;
            case 'NULL':
                $text .= 'NULL';
                break;
            default:
                $text .= $content;
        }

        if(empty($text) && $text != '0')
        {
            $text .= "Value are empty";
        }

        return $text;
    }

    public static function add2log($content, $isSystemLog = false)
    {
        $logFilePath = self::init();

        if($isSystemLog)
        {
            $logFilePath = SYSTEM_LOG;
        }

        if(!$logFilePath)
        {
            throw new \InvalidArgumentException(Loc::getMessage('SEPRO_HELPER_LOG_PATH_NOT_AVAILABLE'));
        }

        $content = self::getContent($content);

        if(file_put_contents($logFilePath, date('Y-m-d H:i:s')."\r".$content."\r--------------------\r", FILE_APPEND | LOCK_EX) === false)
        {
            throw new \LogicException(Loc::getMessage('SEPRO_HELPER_LOG_CONTENT_NOT_SET'));
        }

        return true;
    }
}