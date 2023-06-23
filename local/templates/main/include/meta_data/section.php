<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

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

$this->setFrameMode(true);


?>
<?$intSectionID = $APPLICATION->IncludeComponent(
    "bitrix:catalog.section",
    //'universal',
    '',
    array()
);
?>
<?
$APPLICATION->IncludeFile(
    SITE_TEMPLATE_PATH."/include/meta_block.php",
    Array(
        //"sm_filter_path" => $sm_filter_path,
        //"curr_sect" => $arCurSection['ID'],
        "seo_id" => $currSeoElemID
    ),
    Array("MODE"=>"html", "NAME"=>"текст", "SHOW_BORDER"=>false)
);
//include_once 'meta_block.php';
//$APPLICATION->SetTitle($GLOBALS['h1Title']);
?>