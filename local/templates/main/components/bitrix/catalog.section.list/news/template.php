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


$strSectionEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT");
$strSectionDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE");
$arSectionDeleteParams = array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM'));
//echo Sepro\Helpers::printPre($arResult);
?>

<section class="m-df">
    <h2 class="small"><?=$arResult['NAME']?></h2>
    <div class="ls-ctrl">
        <div class="ls-ctrl-col-l" style="padding-right: 0;">
            <div class="ribbon">
                <div class="ribbon-wrp">
                    <div class="ribbon-slider swiper">
                        <div class="swiper-wrapper">
                            <?
                            $first = true;
                            $first_section = 0;
                            foreach ($arResult['SECTIONS'] as &$arSection)
                            {
                            $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
                            $this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
                            ?>
                            <div id="<? echo $this->GetEditAreaId($arSection['ID']); ?>" class="swiper-slide<? if($first): $first = false; $first_section = $arSection['ID'];?> active<? endif;?>">
                                <a href="/news/<?=$arSection['CODE']?>/" class="th-name">
                                    <? echo $arSection['NAME']; ?>
                                </a>
                            </div><?
                            }
                            unset($arSection);
                            ?>
                        </div>
                    </div>
                    <div class="ribbon-arr">
                        <button class="sw-button-prev"><i class="icon-arr-md-l"></i></button>
                        <button class="sw-button-next"><i class="icon-arr-md-r"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?
    $APPLICATION->IncludeComponent(
    	"bitrix:news.list",
    	"news_in_catalog",
    	Array(
    		"ACTIVE_DATE_FORMAT" => "d.m.Y",
    		"ADD_SECTIONS_CHAIN" => "N",
    		"AJAX_MODE" => "N",
    		"AJAX_OPTION_ADDITIONAL" => "",
    		"AJAX_OPTION_HISTORY" => "N",
    		"AJAX_OPTION_JUMP" => "N",
    		"AJAX_OPTION_STYLE" => "Y",
    		"CACHE_FILTER" => "N",
    		"CACHE_GROUPS" => "N",
    		"CACHE_TIME" => "36000000",
    		"CACHE_TYPE" => "A",
    		"CHECK_DATES" => "Y",
    		"DETAIL_URL" => "",
    		"DISPLAY_BOTTOM_PAGER" => "N",
    		"DISPLAY_DATE" => "N",
    		"DISPLAY_NAME" => "N",
    		"DISPLAY_PICTURE" => "Y",
    		"DISPLAY_PREVIEW_TEXT" => "Y",
    		"DISPLAY_TOP_PAGER" => "N",
    		"FIELD_CODE" => array("",""),
    		"FILTER_NAME" => "",
    		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
    		"IBLOCK_ID" => $arParams['IBLOCK_ID'],
    		"IBLOCK_TYPE" => "services",
    		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
    		"INCLUDE_SUBSECTIONS" => "Y",
    		"MESSAGE_404" => "",
    		"NEWS_COUNT" => "3",
    		"PAGER_BASE_LINK_ENABLE" => "N",
    		"PAGER_DESC_NUMBERING" => "N",
    		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
    		"PAGER_SHOW_ALL" => "N",
    		"PAGER_SHOW_ALWAYS" => "N",
    		"PAGER_TEMPLATE" => ".default",
    		"PAGER_TITLE" => "Новости",
    		"PARENT_SECTION" => $first_section,
    		"PARENT_SECTION_CODE" => "",
    		"PREVIEW_TRUNCATE_LEN" => "",
    		"PROPERTY_CODE" => array("PRICE","LINK","OLD_PRICE",""),
    		"SET_BROWSER_TITLE" => "N",
    		"SET_LAST_MODIFIED" => "N",
    		"SET_META_DESCRIPTION" => "N",
    		"SET_META_KEYWORDS" => "N",
    		"SET_STATUS_404" => "N",
    		"SET_TITLE" => "N",
    		"SHOW_404" => "N",
    		"SORT_BY1" => "SORT",
    		"SORT_BY2" => "ID",
    		"SORT_ORDER1" => "ASC",
    		"SORT_ORDER2" => "ASC"
    	)
    );
    ?>
</section>