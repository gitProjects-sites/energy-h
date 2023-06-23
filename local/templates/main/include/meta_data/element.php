<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

$this->setFrameMode(true);


?>
здесь подключение компонента детальной страницы элемента
<?



$result = \Bitrix\Iblock\ElementTable::getList(array(
    'filter' => array(
        'ID' => $elementId,
    ),
    'select' => array(
        'NAME',
        //'CODE',
    )
));
$el_name = $result->fetch()['NAME'];
$meta_title = $meta_keywords = $el_name.' – купить в '.SITE_CITY;
$meta_descr = 'В компании '.COMPANY_NAME.' вы можете купить '.$el_name.' по выгодной цене. Оформить заказ можно по телефону или через форму заявки.';
$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues(
    $arParams["IBLOCK_ID"], // ID инфоблока
    $elementId // ID элемента
);
$arElMetaProp = $ipropValues->getValues();

if(!empty($arElMetaProp['ELEMENT_META_TITLE']))
{
    $meta_title = $arElMetaProp['ELEMENT_META_TITLE'];
}
if(!empty($arElMetaProp['ELEMENT_META_DESCRIPTION']))
{
    $meta_descr = $arElMetaProp['ELEMENT_META_DESCRIPTION'];
}
if(!empty($arElMetaProp['ELEMENT_META_KEYWORDS']))
{
    $meta_keywords = $arElMetaProp['ELEMENT_META_KEYWORDS'];
}
$APPLICATION->SetPageProperty('title', $meta_title);
$APPLICATION->SetPageProperty('description', $meta_descr);
$APPLICATION->SetPageProperty('keywords', $meta_keywords);
?>