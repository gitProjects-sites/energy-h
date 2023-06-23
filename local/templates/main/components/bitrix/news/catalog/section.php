<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
<?
//echo Sepro\Helpers::printPre($arResult);
$rsSections = CIBlockSection::GetList(
    array(),
    array(
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        '=ID' => $arResult['VARIABLES']['SECTION_ID']
    ),
    false,
    array('UF_*')
);
if ($arSection = $rsSections->Fetch())
{
    foreach ($arSection['UF_DOCS'] as $doc_id)
    {
        $res = CFile::GetByID($doc_id);
        if($arRes = $res->fetch())
        {
            $arSection['ARR_DOCS'][] = $arRes;
        }
    }
    $arSection['DETAIL_PICTURE_SRC'] = CFile::GetPath($arSection['DETAIL_PICTURE']);
    $arResult['CURR_SECTION'] = $arSection;
    //echo Sepro\Helpers::printPre($arSection);
}
?>
<section class="top-section no-top-offset">
    <? if(!empty($arResult['CURR_SECTION']['DETAIL_PICTURE_SRC'])):?>
    <img src="<?=$arResult['CURR_SECTION']['DETAIL_PICTURE_SRC']?>" alt="">
    <? endif;?>
    <div class="container">
        <div class="ts-main">
            <div class="ts-vertical">
                <div class="brc"><?=$arResult['CURR_SECTION']['NAME']?></div>
                <h1 class="caption m2"><?=$arResult['CURR_SECTION']['NAME']?></h1>
                <p>
                    <?=$arResult['CURR_SECTION']['UF_BUNN_TXT']?>
                </p>
            </div>
        </div>
        <div class="ts-bottom">
            <i class="mouse-ico"></i>
        </div>
    </div>
</section>
<div class="container">
    <section class="tt-section m-df">
        <h2 class="m3"><?=$arResult['CURR_SECTION']['UF_HEAD1']?></h2>
        <p>
            <?=$arResult['CURR_SECTION']['UF_BLOCK1_TXT1']?>
        </p>
        <p class="m4">
            <?=$arResult['CURR_SECTION']['UF_BLOCK1_TXT2']?>
        </p>
        <? if(!empty($arResult['CURR_SECTION']['UF_YOUTUBE'])):?>
        <a href="<?=$arResult['CURR_SECTION']['UF_YOUTUBE']?>" data-fancybox class="play-link">
            <i class="icon-arr-tr-r"></i>
            <span>
                Посмотреть видео
            </span>
        </a>
        <? endif;?>
    </section>
    <? if(!empty($arResult['CURR_SECTION']['UF_HEAD2']) || !empty($arResult['CURR_SECTION']['UF_BLOCK2_TXT1'])):?>
    <section class="ar-prop-section m-df">
        <h2 class="small"><?=$arResult['CURR_SECTION']['UF_HEAD2']?></h2>
        <p class="m7">
            <?=$arResult['CURR_SECTION']['UF_BLOCK2_TXT1']?>
        </p>
        <? if(is_array($arResult['CURR_SECTION']['UF_LIST']) && count($arResult['CURR_SECTION']['UF_LIST'])):?>
        <h3 class="caption4 m3" style="font-weight: 500;">Рынки, которые мы обслуживаем:</h3>
        <ul class="col-3">
            <? foreach ($arResult['CURR_SECTION']['UF_LIST'] as $arEl):?>
                <li><?=$arEl?></li>
            <? endforeach;?>
        </ul>
        <? endif;?>
    </section>
    <? endif;?>
<?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"",
	Array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"NEWS_COUNT" => $arParams["NEWS_COUNT"],
		"SORT_BY1" => $arParams["SORT_BY1"],
		"SORT_ORDER1" => $arParams["SORT_ORDER1"],
		"SORT_BY2" => $arParams["SORT_BY2"],
		"SORT_ORDER2" => $arParams["SORT_ORDER2"],
		"FIELD_CODE" => $arParams["LIST_FIELD_CODE"],
		"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
		"MESSAGE_404" => $arParams["MESSAGE_404"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"SHOW_404" => $arParams["SHOW_404"],
		"FILE_404" => $arParams["FILE_404"],
		"INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
		"ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_FILTER" => $arParams["CACHE_FILTER"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE" => $arParams["PAGER_TITLE"],
		"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
		"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
		"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
		"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
		"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
		"PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
		"PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
		"PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
		"DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
		"DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
		"PREVIEW_TRUNCATE_LEN" => $arParams["PREVIEW_TRUNCATE_LEN"],
		"ACTIVE_DATE_FORMAT" => $arParams["LIST_ACTIVE_DATE_FORMAT"],
		"USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
		"GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
		"FILTER_NAME" => $arParams["FILTER_NAME"],
		"HIDE_LINK_WHEN_NO_DETAIL" => $arParams["HIDE_LINK_WHEN_NO_DETAIL"],
		"CHECK_DATES" => $arParams["CHECK_DATES"],
		"STRICT_SECTION_CHECK" => $arParams["STRICT_SECTION_CHECK"],

		"PARENT_SECTION" => $arResult["VARIABLES"]["SECTION_ID"],
		"PARENT_SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"IBLOCK_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],

	),
	$component
);?>
    <? if(is_array($arResult['CURR_SECTION']['ARR_DOCS']) && count($arResult['CURR_SECTION']['ARR_DOCS'])):?>
        <div class="bg-box">
            <h2 class="small">Документы</h2>
            <div class="ou-slider swiper">
                <div class="swiper-wrapper">
                    <? foreach ($arResult['CURR_SECTION']['ARR_DOCS'] as $arDoc):
                        $arFileData = explode('.', $arDoc['ORIGINAL_NAME']);
                        ?>
                        <div class="swiper-slide">
                            <a href="<?=$arDoc['SRC']?>" class="dw-box" target="_blank">
                    <span class="dw-name">
                        <?=$arFileData[0]?>
                    </span>
                                <span class="dw-type">
                        Скачать файл <?=$arFileData[1]?>
                    </span>
                            </a>
                        </div>
                    <? endforeach?>
                </div>
            </div>
        </div>
    <? endif;?>
    <?
    $APPLICATION->IncludeComponent(
    	"bitrix:catalog.section.list",
    	"news",
    	Array(
    		"ADD_SECTIONS_CHAIN" => "Y",
    		"CACHE_FILTER" => "N",
    		"CACHE_GROUPS" => "Y",
    		"CACHE_TIME" => "36000000",
    		"CACHE_TYPE" => "A",
    		"COUNT_ELEMENTS" => "Y",
    		"COUNT_ELEMENTS_FILTER" => "CNT_ACTIVE",
    		"FILTER_NAME" => "",
    		"HIDE_SECTION_NAME" => "N",
    		"IBLOCK_ID" => IBLOCK_NEWS,
    		"IBLOCK_TYPE" => "products",
    		"SECTION_CODE" => "",
    		"SECTION_FIELDS" => array("",""),
    		"SECTION_ID" => $_REQUEST["SECTION_ID"],
    		"SECTION_URL" => "",
    		"SECTION_USER_FIELDS" => array("",""),
    		"SHOW_PARENT_NAME" => "Y",
    		"TOP_DEPTH" => "2",
    		"VIEW_MODE" => "TILE"
    	)
    );
    ?>
    <?
    $APPLICATION->IncludeFile(
        SITE_TEMPLATE_PATH."/include/contact_form.php",
        Array(),
        Array("MODE"=>"html", "NAME"=>"текст", "SHOW_BORDER"=>false)
    );
    ?>
</div>