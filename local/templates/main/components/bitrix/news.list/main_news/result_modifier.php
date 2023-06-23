<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arSects = array();
$result = \Bitrix\Iblock\SectionTable::getList(array(
    'filter' => array(
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => IBLOCK_NEWS,
    ),
    'select' => array(
        'ID',
        'NAME',
    )
));
while($res = $result->fetch())
{
    $arSects[$res['ID']] = $res;
}
foreach ($arResult["ITEMS"] as &$arItem)
{
    $arItem['SECTION_NAME'] = $arSects[$arItem['IBLOCK_SECTION_ID']]['NAME'];
}