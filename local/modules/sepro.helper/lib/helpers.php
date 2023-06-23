<?
namespace Sepro;

class Helpers
{
    // Записываем окончания для численных слов
    public static function num2word($number, $after)
    {
        // Example: \Sepro\Helpers::num2word($number, array('элемент', 'элемента', 'элементов'));

        $arCases = array(2,0,1,1,1,2);
        return $after[ ($number%100 > 4 && $number%100 < 20) ? 2 : $arCases[min($number%10, 5)] ];
    }

    // Склоняем слова по падежам
    // Максимум 1000 запросов в сутки.
    // Очень медленный метод
    public static function decline($word, $case = false, $multi = false)
    {
        if(!function_exists('curl_init'))
            return false;

        $Xml = \Sepro\Helpers::dataCurl("http://api.morpher.ru/WebService.asmx/GetXml?s=".urlencode($word));
        $obData = \Sepro\Helpers::parseXml($Xml);
        $arCases = array('И', 'Р', 'Д', 'В', 'Т', 'П');
        $arErrors = array(
            1 => 'error function decline_morpher - '.$word.': Превышен лимит на количество запросов в сутки.',
            2 => 'error function decline_morpher - '.$word.': Превышен лимит на количество одинаковых запросов в сутки.',
            3 => 'error function decline_morpher - '.$word.': IP заблокирован',
            4 => 'error function decline_morpher - '.$word.': Склонение числительных в GetXml не поддерживается.',
            5 => 'error function decline_morpher - '.$word.': Не найдено русских слов.',
            6 => 'error function decline_morpher - '.$word.': Не указан обязательный параметр s',
            7 => 'error function decline_morpher - '.$word.': Необходимо оплатить услугу',
            8 => 'error function decline_morpher - '.$word.': Пользователь с таким ID не зарегистрирован',
            9 => 'error function decline_morpher - '.$word.': Неправильное имя пользователя или пароль'
        );

        if(intval($obData->code) > 0)
        {
            \Sepro\Log::add2log($arErrors[intval($obData->code)]);
            return $word;
        }

        if($multi)
        {
            $obData = (array) $obData->{"множественное"};
        }
        else
        {
            unset($obData->{"множественное"});
        }

        if($case && in_array($case, $arCases))
        {
            if($case == 'И' && !$multi)
            {
                return $word;
            }

            $obData = $obData->{$case};

            return (string) $obData;
        }

        return (array) $obData;
    }

    // See more http://php.net/manual/ru/function.array-merge-recursive.php#92195
    public static function array_merge_recursive_distinct(array &$array1, array &$array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value)
        {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key]))
            {
                $merged[$key] = self::array_merge_recursive_distinct($merged[$key], $value);
            }
            else
            {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    // ANALOG CSite::InDir
    public static function checkDirectory($findme)
    {
        return substr(\Sepro\App::getInstance()->GetCurPage(true), 0, strlen($findme)) == $findme;
    }

    // recursing htmlspecialchars
    public static function rhtmlspecialchars($var)
    {
        if(is_array($var))
        {
            foreach($var as $key => &$value)
            {
                $value = self::rhtmlspecialchars($value);
            }

            return $var;
        }

        return htmlspecialchars($var);
    }

    // ПОИСК ИСКОМОГО ЗНАЧЕНИЯ ПО СОВПАДЕНИЮ ИЗ НАЗВАНИЯ ФУНКЦИЕЙ stripos
    public static function mstripos($haystack, $arNeedles, $offset = 0)
    {
        if (is_array($haystack))
        {
            foreach ($haystack as $stack)
            {
                return self::mstripos($stack, $arNeedles, $offset);
            }
        }

        foreach ($arNeedles as $needle)
        {
            if (stripos($haystack, (string) $needle, $offset) !== false)
            {
                return $needle;
            }
        }

        return false;
    }

    // ПАРСИМ XML
    public static function parseXml($content)
    {
        if(empty($content)) return false;

        libxml_use_internal_errors(true);

        $obXml = is_file($content) ? simplexml_load_file($content) : simplexml_load_string($content);

        if($obXml === false)
        {
            \Sepro\Log::add2log("Ошибка чтения XML\r".$content);

            foreach(libxml_get_errors() as $error)
            {
                \Sepro\Log::add2log($error->message);
            }

            return false;
        }

        return $obXml;
    }

    // МЕТОД ИСПОЛЬЗУЕТСЯ РЕДКО
    public static function xml2array($obXml)
    {
        $result = array();
        foreach($obXml as $field => $value)
        {
            if(count($value) > 0)
            {
                $arValue = (array) $value;
                unset($arValue['@attributes']);

                if(count($arValue) > 1)
                {
                    $result[] = self::xml2array($value);
                }
                else
                {
                    $result[trim($field)] = self::xml2array($value);
                }
            }
            else
            {
                $result[trim((string) $field)] = trim((string) $value);
            }
        }

        return $result;
    }

    // ЧТЕНИЕ ПО АДРЕСУ ЧЕРЕЗ CURL
    public static function dataCurl($path)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $xml = curl_exec($ch);

        $errno = curl_errno($ch);

        curl_close($ch);

        if ($errno)
            return false;

        return $xml;
    }

    // ARRAY FOR ITEMS RANDOM
    public static function shuffle_assoc($array)
    {
        $keys = array_keys($array);
        $new = array();

        shuffle($keys);

        foreach($keys as $key) {
            $new[$key] = $array[$key];
        }

        return $new;
    }

    public static function price_format($money, $isInt = false)
    {
        return number_format($money, (($isInt) ? 0 : 2), '.', '&nbsp;');
    }

    public static function isMobile()
    {
        return (boolean) preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }

    public static function SendSms($phone, $text)
    {
        $obSms = new \CIWebSMS;
        $obSms->Send(
            $phone,
            $text,
            array(
                'GATE' => 'sms-sending.ru',
                'LOGIN' => 'power96',
                'PASSWORD' => '254102',
                'ORIGINATOR' => 'sportline',
            )
        );

        $resultSend = (array) $obSms->return_mess;

        return intval($resultSend["code"]) == 1 ? true : false;
    }

    // Отправляем смс и записываем пользователю сгенирированый код
    public static function sendGenericCode($id, $phone)
    {
        $obUser = new \CUser;
        $userGenericCode = rand(100000, 999999);
        $GLOBALS['_REQUEST']['ACCOUNT_CONFIRMED'] = 'Y';
        self::SendSms($phone, 'Ваш код подтверждения на сайте: '.$userGenericCode);

        if($obUser->Update($id, array('UF_GENERIC_CODE' => $userGenericCode)))
            return true;

        return false;
    }

    public static function printPre($var, $UserID = false, $IP = false, $title = false){

        $html = '';

        if(!empty($UserID) && $UserID !== (integer) \Sepro\User::getInstance()->GetID())
        {
            return false;
        }

        if(!empty($IP) && $IP != \Sepro\User::getUserIP())
        {
            return false;
        }

        $arBacktrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        $bIsFirstCall = $arBacktrace[1]['function'] != __FUNCTION__;

        if($bIsFirstCall)
        {
            $html .= '<div class="printPre">';
            $html .= "
            <style>
                .printPre {position:relative;z-index:1001;}
                .printPre * {cursor:default;font-family: Arial, Helvetica, sans-serif;font-size:10px;color:#000;}
                .printPre .headlink{}
                .printPre .headlink:before{content: '+ '}
                .printPre .headlink .showlink{cursor:pointer;text-decoration:none;color:#33567f;}
                .printPre .headlink ul{display:none;list-style:none;padding: 8px 0 8px 30px;margin:0}
                .printPre .headlink.active > ul{display:block}
                .printPre .headlink.active:before{content: '- '}
                .printPre .key{color:#999}
                .printPre .type{color:#999}
                .printPre .type.string{color:#74ae81}
                .printPre .type.integer{color:#668eae}
                .printPre .type.boolean{color:#ae5750}
            </style>";
        }

        if(is_array($var) || is_object($var))
        {
            if($bIsFirstCall)
            {
                $html .= '<p class="firstString">'.($title !== false ? $title : $arBacktrace[0]["file"]).', on line '.$arBacktrace[0]["line"].": ".'</p>';
            }

            $html .= '<span class="headlink">';
            $html .= '<span class="showlink" onclick="var parent=this.parentElement,pClass=parent.className,arMatches=pClass.match(/active/g)||[];parent.className=arMatches.length?pClass.replace(/ active/g,\'\'):pClass+\' active\';">'.(is_array($var) ? 'array ' : 'object '.get_class($var)).' ('.count((array)$var).')'.'</span> (';
            $html .= '<ul>';

            foreach ($var as $key => $value)
            {
                $html .= '<li><span class="key">['.$key.'] => </span>'.self::printPre($value)."</li>";
            }

            $html .= "</ul> )</span>";

        }
        else
        {
            $html .= '<span>';

            if ($bIsFirstCall)
            {
                $html .= $title !== false ? $title.": " : $arBacktrace[0]["file"].', line '.$arBacktrace[0]["line"].": ";
            }

            $type = gettype($var);

            switch($type)
            {
                case 'boolean':
                    $html .= $var ? 'true' : 'false';
                    break;
                case 'string':
                    $html .= htmlspecialchars($var);
                    break;
                default:
                    $html .= $var;
                    break;
            }

            $html .= '</span> <span class="type '.$type.'">('.($type == 'string' ? 'string '.strlen($var) : $type).')</span>';
        }

        if ($bIsFirstCall)
        {
            $html .= '</div>';
        }

        return $html;
    }
}