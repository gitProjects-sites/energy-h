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
//echo Sepro\Helpers::printPre($arResult);
?>
<section class="offers-section m-df">
    <h2 class="small">Наши предложения</h2>
    <div class="ou-slider offers-slider swiper">
        <div class="swiper-wrapper">
    <?foreach($arResult["ITEMS"] as $arItem):?>
        <?
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>
        <div class="swiper-slide" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
            <a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="offer-box">
                <span class="offer-img">
                    <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="">
                </span>
                <span class="offer-main">
                    <span class="offer-caption">
                        <?echo $arItem["NAME"]?>
                    </span>
                </span>
            </a>
        </div>
    <?endforeach;?>
        </div>
    </div>
    <div class="ou-btn-wrp">
        <button class="ou-btn sw-button-prev"><i class="icon-arr-md-l"></i></button>
        <button class="ou-btn sw-button-next"><i class="icon-arr-md-r"></i></button>
    </div>
</section>

