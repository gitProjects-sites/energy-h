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
<div class="ed-txt-flex">
    <div class="ed-txt-main">
        <h2 class="small m10" style="font-weight: 700;"><?=$arResult['NAME']?></h2>
        <p>
            <?=$arResult['DESCRIPTION']?>
        </p>
    </div>
    <div class="ed-txt-counters">
        <ul class="ed-counters custom scroll-animate">
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
    <li id="<?=$this->GetEditAreaId($arItem['ID']);?>">
        <div class="ed-counter-box">
            <div class="ed-count">
                <b data-count-to="<?=$arItem['CODE']?>">0</b><? if(intval($arItem['CODE'] > 1000)):?>+<? endif;?>
            </div>
            <div class="ed-title">
                <?=$arItem['NAME']?>
            </div>
        </div>
    </li>
<?endforeach;?>
        </ul>
    </div>
</div>

