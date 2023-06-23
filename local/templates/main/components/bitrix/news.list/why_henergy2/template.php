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
<section class="ed-dection m-df">
    <h2><?=$arResult['NAME']?></h2>
    <p class="m8">
        <?=$arResult['DESCRIPTION']?>
    </p>

    <ul class="df-list ed-list custom">
            <?
            $counter = 0;
            foreach($arResult["ITEMS"] as $arItem):?>
            <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
            $counter++;
            ?>
            <li id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                <div class="ed-item">
                    <div class="ed-img">
                        <img src="<?=$arItem['DISPLAY_PROPERTIES']['SVG']['FILE_VALUE']['SRC']?>" alt="">
                    </div>
                    <div class="ed-text">
                        <?=$arItem['NAME']?>
                    </div>
                </div>
            </li>
<?endforeach;?>
    </ul>
</section>

