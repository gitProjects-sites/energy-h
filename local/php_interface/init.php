<?use \Bitrix\Main\Loader;

Loader::includeModule('sepro.helper');
Loader::includeModule("iblock");
//Loader::includeModule("highloadblock");
//Loader::includeModule("catalog");
//Loader::includeModule("sale");

//CJSCore::Init(array('window'));

AddEventHandler('main', 'OnEpilog', '_Check404Error', 1);
//AddMessage2Log('init', 'init.php');
function _Check404Error(){
    if (defined('ERROR_404') && ERROR_404 == 'Y' || CHTTP::GetLastStatus() == "404 Not Found") {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        include $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/header.php';
        include $_SERVER['DOCUMENT_ROOT'] . '/404.php';
        include $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/footer.php';
    }
}

define('IBLOCK_NEWS', 1);
define('IBLOCK_CATALOG', 2);
define('IBLOCK_SITE_STORE', 5);
define('IBLOCK_WHU_ENERGY', 7);
define('IBLOCK_COORDS', 8);
define('IBLOCK_CONTACTS', 9);
define('ELEMENT_VIDEO', 33);

//define('COMPANY_NAME', COption::GetOptionString('sepro.helper','COMPANY'));
define('CONTACT_EMAIL', \Bitrix\Main\Config\Option::get("grain.customsettings","EMAIL"));
define('CONTACT_PHONE', \Bitrix\Main\Config\Option::get("grain.customsettings","PHONE"));
define('CONTACT_PHONE2', \Bitrix\Main\Config\Option::get("grain.customsettings","PHONE2"));
define('CONTACT_ADDRESS', \Bitrix\Main\Config\Option::get("grain.customsettings","ADDRESS"));

global $arServices, // все разделы каталога
       $ORMprops; // орм свойств


//$arServices = \Sepro\IBlock::getSections(array(),$_REQUEST['clear_cache']=='Y')[IBLOCK_CATALOG];
$ORMprops = \Sepro\ORMFactory::compile('b_iblock_element_property');

$checkType = '';
if (isset($_COOKIE['BITRIX_SM_siteType']))
{
    $checkType=$_COOKIE['BITRIX_SM_siteType'];
}
else
{
    //echo Sepro\Helpers::printPre($_SERVER['HTTP_USER_AGENT']);
    $iphone = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
    $android = strpos($_SERVER['HTTP_USER_AGENT'],"Android");
    $palmpre = strpos($_SERVER['HTTP_USER_AGENT'],"webOS");
    $berry = strpos($_SERVER['HTTP_USER_AGENT'],"BlackBerry");
    $ipod = strpos($_SERVER['HTTP_USER_AGENT'],"iPod");
    $mobile = strpos($_SERVER['HTTP_USER_AGENT'],"Mobile");
    $symb = strpos($_SERVER['HTTP_USER_AGENT'],"Symbian");
    $operam = strpos($_SERVER['HTTP_USER_AGENT'],"Opera M");
    $htc = strpos($_SERVER['HTTP_USER_AGENT'],"HTC_");
    $fennec = strpos($_SERVER['HTTP_USER_AGENT'],"Fennec/");
    $winphone = strpos($_SERVER['HTTP_USER_AGENT'],"WindowsPhone");
    $wp7 = strpos($_SERVER['HTTP_USER_AGENT'],"WP7");
    $wp8 = strpos($_SERVER['HTTP_USER_AGENT'],"WP8");

    if ($iphone || $android || $palmpre || $ipod || $berry || $mobile || $symb || $operam || $htc || $fennec || $winphone || $wp7 || $wp8 || !empty($_REQUEST['mob']))
    {
        $type = "mob";
        $APPLICATION->set_cookie("siteType", $type);
        $checkType = $type;
    }
}

define('SITE_VERSION',$checkType);

class CAkon
{

    public static $arTranslit = array(
        "max_len" => "100",
        "change_case" => "L",
        "replace_space" => "_",
        "replace_other" => "_",
        "delete_repeat_replace" => "true",
        "use_google" => "false",
    );

    public static function Translit($str)
    {
        return CUtil::translit($str, "ru", self::$arTranslit);
    }

    public static function setPhoneNum($phone)
    {
        return preg_replace("/[\D]/", "", $phone);
    }

    public static function unicSymCode($name)
    {
        $fcode = self::Translit($name);
        $sym_code = $fcode;
        $result = \Bitrix\Iblock\SectionTable::getList(array(
            'filter' => array(
                'CODE' => $fcode.'%',
                'IBLOCK_ID' => IBLOCK_CATALOG,
            ),
            'select' => array(
                'ID',
                'CODE',
            )
        ));
        $maxnum = 0;
        while($res = $result->fetch())
        {
            $arCode = explode('-', $res['CODE']);
            if($arCode[0] == $fcode && $maxnum <= intval($arCode[1]))
            {
                $maxnum = intval($arCode[1]) + 1;
            }
            echo Helpers::printPre($arCode);
        }
        if (intval($maxnum))
        {
            $sym_code .= '-'.$maxnum;
        }
        return $sym_code;
    }

    public static function getPropsIblock($iblock_id) {
        $arResult = [];
        $result = \Bitrix\Iblock\ElementTable::getList(array(
            'filter' => array(
                'ACTIVE' => 'Y',
                'IBLOCK_ID' => $iblock_id,
            ),
            'select' => array(
                'ID',
                'NAME',
            )
        ));
        while($res = $result->fetch())
        {
            $arResult[$res['ID']] = $res;
        }
        if(count($arResult))
        {
            $result = $GLOBALS['ORMprops']::getList(array(
                'filter' => array(
                    'IBLOCK_ELEMENT_ID' => array_keys($arResult),
                ),
                'select' => array(
                    'IBLOCK_ELEMENT_ID',
                    'IBLOCK_PROPERTY_ID',
                    'VALUE',
                )
            ));
            while($res = $result->fetch())
            {
                $arResult[$res['IBLOCK_ELEMENT_ID']][$res['IBLOCK_PROPERTY_ID']][] = $res['VALUE'];
            }
        }

        return $arResult;
    }

    public static function getUserField($id, $field) {
        //$arUser = array();
        $UserField = false;
        $result = Bitrix\Main\UserTable::getList(array(
            'select' => array(
                $field
            ),
            'filter' => array('ID' => $id)
        ));
        if($row = $result->fetch())
        {
            $UserField = $row[$field];
            if($field=='PERSONAL_PHOTO')
            {
                $arUserPhoto = CFile::ResizeImageGet(
                    $UserField,
                    array('width' => 60, 'height' => 60),
                    BX_RESIZE_IMAGE_PROPORTIONAL,
                    false,
                    false,
                    false,
                    90
                );
                $UserField = $arUserPhoto['src'];
            }
        }
        return $UserField;
    }

    public static function getUser($id) { // все поля пользователя
        $arUser = array();
        $result = Bitrix\Main\UserTable::getList(array(
            'select' => array(
                'ID',
                'NAME',
                'LAST_NAME',
                'LOGIN',
                'EMAIL',
                'PERSONAL_PHOTO',
                'PERSONAL_CITY',
                'PERSONAL_NOTES',
                'WORK_NOTES',
                'DATE_REGISTER',
                'LAST_LOGIN',
                'UF_*',
            ),
            'filter' => array('ID' => $id)
        ));
        if($row = $result->fetch())
        {
            /*$arUserPhoto = CFile::ResizeImageGet(
                $row['PERSONAL_PHOTO'],
                array('width' => 200, 'height' => 200),
                BX_RESIZE_IMAGE_PROPORTIONAL,
                false, // возвращать ли размеры в массиве
                false, // фильтры (пока один sharpe)
                false, // Отложенное масштабирование
                90 // качество жпг
            );*/
            $row['USER_PHOTO'] = self::getPhoto($row['PERSONAL_PHOTO'],200,200);
            $arUser = $row;
        }
        return $arUser;
    }

    public static function getReviewsCount($usid)
    {
        $result = count($GLOBALS['ORMprops']::GetList(array(
            'select' => array('IBLOCK_ELEMENT_ID'),
            'filter' => array(
                '=IBLOCK_PROPERTY_ID' => '25', // свойство "к кому прикреплён отзыв"
                '=VALUE' => $usid
            )
        ))->fetchAll());
        return $result;
    }


    public static function getPhoto($id, $width = 0, $height = 0, $exact = false)
    {
        if (!$width) {
            $photo_src = CFile::GetPath($id);
        } else {
            if ($exact) {
                $arFile = CFile::ResizeImageGet($id, array('width' => $width, 'height' => $height), BX_RESIZE_IMAGE_EXACT, true);
            } else {
                $arFile = CFile::ResizeImageGet($id, array('width' => $width, 'height' => $height), BX_RESIZE_IMAGE_PROPORTIONAL, true);
            }
            $photo_src = $arFile['src'];
        }
        return $photo_src;
    }


    static public function getPropsENUM($iblock=IBLOCK_CATALOG)
    {// значения свойств типа список
        $clearCache = false;
        $arProps = array();
        $cache = \Bitrix\Main\Application::getInstance()->getManagedCache();
        if ($clearCache) {
            $cache->clean("PROPS_ENUM");
        }

        if ($cache->read(3600, "PROPS_ENUM")) {
            $arProps = $cache->get("PROPS_ENUM");
        }
        else {
            $ORMenum = \Sepro\ORMFactory::compile('b_iblock_property_enum');
            $rsProperties = \Bitrix\Iblock\PropertyTable::GetList(array(
                'filter' => array(
                    '=IBLOCK_ID' => $iblock
                ),
                'select' => array(
                    'ID',
                    'CODE',
                    'PROPERTY_TYPE'
                )
            ));
            $arPropEnumID = array();
            while($arProperty = $rsProperties->fetch())
            {
                if($arProperty['PROPERTY_TYPE']=='L')
                {
                    $arPropEnumID[] = $arProperty['ID'];
                }
            }
            $result = $ORMenum::getList(array(
                'filter' => array(
                    'PROPERTY_ID' => $arPropEnumID,
                ),
                'select' => array(
                    'ID',
                    'PROPERTY_ID',
                    'VALUE',
                )
            ));
            while ($res = $result->fetch())
            {
                $arProps[$res['PROPERTY_ID']][$res['ID']] = $res['VALUE'];
            }
            $cache->set("PROPS_ENUM", $arProps);
        }

        return $arProps;
    }

    static public function getOptionData($field)
    {// запрос значения выбранных свойств каталога на данный момент из базы
        $arSelectProps = array();
        if ($res = \Bitrix\Main\Application::getConnection()->query("SELECT `VALUE` FROM `b_option` WHERE `MODULE_ID` = 'sepro.helper' AND `NAME` = '$field'")->fetch())
        {
            $arSelectProps = unserialize($res['VALUE']);
        }
        return $arSelectProps;
    }

    static public function setOptionData($field, $arProperties = array())
    {
        \Bitrix\Main\Application::getConnection()->query("DELETE FROM `b_option` WHERE `MODULE_ID` = 'sepro.helper' AND `NAME` = '$field'");
        if (count($arProperties)) {
            //AddMessage2Log('$arProperties = '.print_r($arProperties, true), 'serv');
            $str_date = serialize($arProperties);
            \Bitrix\Main\Application::getConnection()->query("INSERT INTO `b_option`(`MODULE_ID`, `NAME`, `VALUE`, `DESCRIPTION`, `SITE_ID`) VALUES ('sepro.helper','$field','$str_date',NULL,NULL)");
        }
    }

    static public function getWordData($DATA, $full = true) // конвертирует формат даты с 04.11.2008 в 04 Ноября, 2008 или 04 ноября
    {
        $MES = array(
            "01" => "января",
            "02" => "февраля",
            "03" => "марта",
            "04" => "апреля",
            "05" => "мая",
            "06" => "июня",
            "07" => "июля",
            "08" => "августа",
            "09" => "сентября",
            "10" => "октября",
            "11" => "ноября",
            "12" => "декабря"
        );
        $arData = explode(".", $DATA);
        //echo count($arData);
        if(count($arData) != 3 || !intval($arData[0]) || !intval($arData[1]) || !intval($arData[2]))
        {
            $newData = 'надо date("d.m.Y")';
        }
        else
        {
            $d = ($arData[0] < 10) ? substr($arData[0], 1) : $arData[0];

            $newData = $d." ".$MES[$arData[1]];
            if($full)
            {
                $newData .= ", ".$arData[2];
            }
        }
        return $newData;
    }

    
    public static function setRybaText($iblock, $length=3, $sects=0, $pics=true) {
        /*
         *      array(
                    $iblock 'IBLOCK' => 'Номер инфоблока',
                    $length 'LENGTH' => 'количество элементов',
                    $sects 'SECTIONS' => 'количество разделов"
                    $pics 'IMAGES' => 'ставить ли картинки' (true/false)
                );
         *
         */
        //if($arGlob)

        $img_url = 'https://www.dreamstime.com/free-images_pg1';
        $modifier = $GLOBALS['USER']->GetID();
        $el = new CIBlockElement;
        $arTranslitParams = array("replace_space"=>"_", "replace_other"=>"_");
        $http = 'https://fish-text.ru/get?format=json&';
        $arRequests = array(
            'NAME' => 'type=title',
            'PREVIEW_TEXT' => 'number=5',
            'DETAIL_TEXT' => 'type=paragraph&number=5',
        );

        //echo Sepro\Helpers::printPre($arIMG);
        //return;
        $arSections = array();
        $pic_counter = 0;
        if(intval($sects) > 0)
        {
            $bs = new CIBlockSection;
            for ($i=0; $i<$sects; $i++)
            {
                $resp = json_decode(file_get_contents($http.$arRequests['NAME']), true);
                if($resp['status']=='success')
                {
                    $arFields = Array(
                        "ACTIVE" => "Y",
                        "IBLOCK_ID" => $iblock,
                        "NAME" => $resp['text'],
                    );
                    if($pics)
                    {
                        $arIMG = self::get_img_in_dir($img_url);
                        $arFile = CFile::MakeFileArray($arIMG[1][$pic_counter]);
                        $arFields["PICTURE"] = $arFile;
                        $pic_counter++;
                    }
                    $ID = $bs->Add($arFields);
                    if(intval($ID) > 0)
                    {
                        $arSections[] = $ID;
                    }
                }
            }
        }
        if(!count($arSections))
        {
            $arSections[] = 0;
        }
        foreach ($arSections as $sect_id)
        {
            for ($i=0; $i<$length; $i++)
            {
                foreach ($arRequests as $key => $request)
                {
                    $resp = json_decode(file_get_contents($http.$request), true);
                    if($resp['status']=='success')
                    {
                        $result_text = str_replace('\n\n','
', $resp['text']);
                        $arElem[$key] = $result_text;
                        if($key=='NAME')
                        {
                            $arElem['CODE'] = Cutil::translit($resp['text'], "ru", $arTranslitParams);
                        }
                    }
                }
                $PROP = array();
                if(!empty($arElem['NAME']))
                {
                    $arLoadProductArray = Array(
                        "MODIFIED_BY"    => $modifier,
                        "IBLOCK_ID"      => $iblock,
                        "IBLOCK_SECTION_ID"=> $sect_id,
                        "PROPERTY_VALUES"=> $PROP,
                        "NAME"=> $arElem['NAME'],
                        "CODE"=> $arElem['CODE'],
                        "ACTIVE"         => "Y",
                        "PREVIEW_TEXT"   => $arElem['PREVIEW_TEXT'],
                        "DETAIL_TEXT"    => $arElem['DETAIL_TEXT'],
                    );
                    if($pics)
                    {
                        $arIMG = self::get_img_in_dir($img_url);
                        $arFile = CFile::MakeFileArray($arIMG[1][$pic_counter]);
                        $arLoadProductArray["PREVIEW_PICTURE"] = $arFile;
                        $arLoadProductArray["DETAIL_PICTURE"] = $arFile;
                        $pic_counter++;
                    }
                    if(!$el->Add($arLoadProductArray))
                    {
                        echo "Error: ".$el->LAST_ERROR;
                    }
                }


                // echo Sepro\Helpers::printPre($resp);

            }

        }
    }


    public static function get_img_in_dir($url) {

        $host = parse_url($url, PHP_URL_HOST); // Нахожу хост в урле

        /* Для начала скачиваю код страницы... */
        $curl = curl_init(); // Инициализирую CURL
        curl_setopt($curl, CURLOPT_HEADER, 0); // Отключаю в выводе header-ы
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //Возвратить данные а не показать в браузере
        curl_setopt($curl, CURLOPT_URL, $url); // Указываю URL
        $code = curl_exec($curl); // Получаю данные
        curl_close($curl); // Закрываю CURL сессию

        //echo $code;
        $arrayImg = array(); // Массив для ссылок изображений
        $regex = '/<\s*img[^>]*src=[\"|\'](.*?)[\"|\'][^>]*\/*>/i';
        preg_match_all($regex, $code, $arrayImg);
        return $arrayImg;
    }


}