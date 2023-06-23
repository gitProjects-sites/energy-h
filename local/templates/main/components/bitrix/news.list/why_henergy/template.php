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
    <h2 class="small"><?=$arResult['NAME']?></h2>
    <p class="m8">
        <?=$arResult['DESCRIPTION']?>
    </p>

    <div class="ou-slider ed-slider swiper">
        <div class="swiper-wrapper">
            <?
            $counter = 0;
            foreach($arResult["ITEMS"] as $arItem):?>
            <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
            $counter++;
            ?>
            <div class="swiper-slide" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                <div class="ed-box">
                    <div class="edb-main">
                        <?=$arItem['NAME']?>
                    </div>
                    <div class="edb-count">
                        0<?=$counter?>
                    </div>
                </div>
            </div>
<?endforeach;?>
        </div>
    </div>
    <div class="ou-btn-wrp">
        <button class="ou-btn sw-button-prev"><i class="icon-arr-md-l"></i></button>
        <button class="ou-btn sw-button-next"><i class="icon-arr-md-r"></i></button>
    </div>
</section>

