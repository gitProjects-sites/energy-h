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
$more_img = is_array($arResult['DISPLAY_PROPERTIES']['MORE_PHOTO']['VALUE']);
$arNotNeedProp = ['MORE_PHOTO','DOCS']
?>
<section class="ar-top-section no-top-offset m-df">
    <div class="container">
        <div class="ar-flex<?if (!$more_img):?> no-image<? endif;?>">
            <div class="ar-main">
                <div class="brc"><?=$arResult['NAME']?></div>

                <h1 class="caption m2"><?=$arResult['NAME']?></h1>
                <? foreach ($arResult['DISPLAY_PROPERTIES'] as $code => $arProp):
                    if(in_array($code, $arNotNeedProp) || empty($arProp['VALUE']))
                    {
                        continue;
                    }
                    ?>
                <p class="m6">
                    <strong class="color"><?=$arProp['NAME']?>:</strong> <?=$arProp['VALUE']?>
                </p>
                <? endforeach;?>
                <?=$arResult['PREVIEW_TEXT']?>
                <a href="#request" data-modal class="btn m4">ОФОРМИТЬ ЗАЯВКУ</a>
                <i class="mouse-ico"></i>
            </div>
            <? if($more_img):?>
            <div class="ar-images">
                <div class="ar-images-inner">
                    <div class="ar-img-slider swiper">
                        <div class="swiper-wrapper">
                            <? foreach ($arResult['DISPLAY_PROPERTIES']['MORE_PHOTO']['FILE_VALUE'] as $arPhoto):?>
                            <div class="swiper-slide">
                                <a href="<?=$arPhoto['SRC']?>" data-gallery="gallery1" class="ar-img-item">
                                    <img src="<?=$arPhoto['SRC']?>" alt="">
                                </a>
                            </div>
                            <? endforeach;?>
                        </div>
                        <div class="swiper-pagination-custom"></div>
                    </div>
                </div>
            </div>
            <? endif;?>
        </div>
        <p>
            <?=$arResult['DETAIL_TEXT']?>
        </p>
    </div>
</section>

<div class="container">
    <? if(!empty($arResult['PROPERTIES']['PARAMS']['VALUE']) || !empty($arResult['PROPERTIES']['U_AREAS']['VALUE'])):?>
    <section class="ar-prop-section m-df">
        <? if(!empty($arResult['PROPERTIES']['PARAMS']['VALUE'])):?>
        <h2 class="small">Основные параметры оборудования</h2>
        <ul class="m7">
            <? foreach ($arResult['PROPERTIES']['PARAMS']['VALUE'] as $val):?>
            <li><?=$val?></li>
            <? endforeach;?>
        </ul>
        <? endif;?>
        <? if(!empty($arResult['PROPERTIES']['U_AREAS']['VALUE'])):?>
        <h3 class="caption4 m3" style="font-weight: 500;">Области применения:</h3>
        <ul class="col-3">
            <? foreach ($arResult['PROPERTIES']['U_AREAS']['VALUE'] as $val):?>
            <li><?=$val?></li>
            <? endforeach;?>
        </ul>
        <? endif;?>
    </section>
    <? endif;?>
    <? if(!empty($arResult['PROPERTIES']['CHARAKTERS']['VALUE'])):?>
        <section class="tech-prop-section m-df">
            <h2>Технические характеристики</h2>
            <div class="t-scroll">
                <?=$arResult['PROPERTIES']['CHARAKTERS']['~VALUE']['TEXT']?>
            </div>
        </section>
    <? endif;?>
    <? if(!empty($arResult['DISPLAY_PROPERTIES']['DOCS']['VALUE'])):
        //echo Sepro\Helpers::printPre($arResult['PROPERTIES']['DOCS']['VALUE']);
        ?>
    <div class="bg-box">
        <h2 class="small">Полезные ссылки и документы</h2>
        <?
        $APPLICATION->IncludeFile(
        	SITE_TEMPLATE_PATH."/include/doc_list.php",
        	Array("DOCS" => $arResult['DISPLAY_PROPERTIES']['DOCS']['FILE_VALUE']),
        	Array("MODE"=>"html", "NAME"=>"текст", "SHOW_BORDER"=>false)
        );
        ?>
    </div>
    <? endif;?>
    <?
    $APPLICATION->IncludeComponent(
        "bitrix:news.list",
        "why_henergy2",
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
            "IBLOCK_ID" => IBLOCK_WHU_ENERGY,
            "IBLOCK_TYPE" => "services",
            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
            "INCLUDE_SUBSECTIONS" => "Y",
            "MESSAGE_404" => "",
            "NEWS_COUNT" => "20",
            "PAGER_BASE_LINK_ENABLE" => "N",
            "PAGER_DESC_NUMBERING" => "N",
            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
            "PAGER_SHOW_ALL" => "N",
            "PAGER_SHOW_ALWAYS" => "N",
            "PAGER_TEMPLATE" => ".default",
            "PAGER_TITLE" => "Новости",
            "PARENT_SECTION" => "",
            "PARENT_SECTION_CODE" => "",
            "PREVIEW_TRUNCATE_LEN" => "",
            "PROPERTY_CODE" => array("SVG","LINK","OLD_PRICE",""),
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
    <?
    $APPLICATION->IncludeFile(
        SITE_TEMPLATE_PATH."/include/contact_form.php",
        Array(),
        Array("MODE"=>"html", "NAME"=>"текст", "SHOW_BORDER"=>false)
    );
    ?>
</div>