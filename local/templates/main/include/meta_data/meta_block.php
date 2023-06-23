<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $filter_detail_text,
       $h1Title;
//echo $h1Title.' ???????';
//echo Sepro\Helpers::printPre($arParams);
if(empty($arParams['curr_sect']) && !empty($h1Title))
{
    $title = $h1Title;
    if(defined('SITE_CITY'))
    {
        $title .=' – купить в '.SITE_CITY;
    }
    $APPLICATION->SetPageProperty('title', $title);
    if(defined('COMPANY_NAME'))
    {
        $APPLICATION->SetPageProperty('description', 'В компании ' . COMPANY_NAME . ' вы можете купить ' . $h1Title . ' по выгодной цене. Оформить заказ можно по телефону или через форму заявки.');
    }
    $APPLICATION->SetPageProperty('keywords', $h1Title);
}
$result = \Bitrix\Iblock\ElementTable::getList(array(
    'filter' => array(
        'IBLOCK_ID' => IBLOCK_SEO,
        'ID' => $arParams['seo_id']
        //'IBLOCK_SECTION_ID' => $seo_sect['ID'],
        //'PREVIEW_TEXT' => $sm_url
    ),
    'select' => array(
        'ID',
        'DETAIL_TEXT'
        //'NAME',
        //'CODE',
    )
));
if($res = $result->fetch())
{
    $ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues(
        IBLOCK_SEO, // ID инфоблока
        $arParams['seo_id'] // ID элемента
    );
    $arElMetaProp = $ipropValues->getValues();
    //echo Sepro\Helpers::printPre($arElMetaProp);

    if(!empty($arElMetaProp['ELEMENT_META_TITLE']))
    {
        $APPLICATION->SetPageProperty('title', $arElMetaProp['ELEMENT_META_TITLE']);
        //$APPLICATION->SetTitle($arElMetaProp['ELEMENT_META_TITLE']);
    }
    if(!empty($arElMetaProp['ELEMENT_PAGE_TITLE']))
    {
        $h1Title = $arElMetaProp['ELEMENT_PAGE_TITLE'];
    }
    if(!empty($arElMetaProp['ELEMENT_META_KEYWORDS']))
    {
        $APPLICATION->SetPageProperty('keywords', $arElMetaProp['ELEMENT_META_KEYWORDS']);
    }
    if(!empty($arElMetaProp['ELEMENT_META_DESCRIPTION']))
    {
        $APPLICATION->SetPageProperty('description', $arElMetaProp['ELEMENT_META_DESCRIPTION']);
    }
    $filter_detail_text = $res['DETAIL_TEXT'];
}
//echo Sepro\Helpers::printPre($arItems);
$APPLICATION->SetTitle($h1Title);
?>
    <div class="cs-invbl js-m_title" data-title="<?=$arElMetaProp['ELEMENT_META_TITLE']?>"></div>
<?
//}
?>