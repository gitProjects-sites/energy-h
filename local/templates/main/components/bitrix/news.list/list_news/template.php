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
<ul class="df-list js-prlist custom">
<?foreach($arResult["ITEMS"] as $arItem):?>
    <?
    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
    ?>
    <li id="<?=$this->GetEditAreaId($arItem['ID']);?>">
        <div class="ctn-box">
            <div class="ctn-img">
                <span class="tg-wrp">
                    <span class="tg-item"><?=$arResult['SECTION']['PATH'][0]['NAME']?></span>
                </span>
                <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="ctn-img-inner">
                    <img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="">
                </a>
            </div>
            <div class="ctn-main">
                <div class="cnt-top">
                    <time datetime="<?=$arItem['ACTIVE_FROM']?>"><?=$arResult['SECTION']['PATH'][0]['NAME']?>, <?=$arItem['ACTIVE_FROM']?>, 2 минуты чтения</time>
                    <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="cnt-caption">
                        <?=$arItem['NAME']?>
                    </a>
                    <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="cnt-text">
                        <?=$arItem['PREVIEW_TEXT']?>
                    </a>
                </div>
                <div class="cnt-bottom">
                    <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="link">
                        <span>Узнать больше</span>
                        <i class="icon-arr-lg-r"></i>
                    </a>
                </div>
            </div>
        </div>
    </li>
<?endforeach;?>
</ul>
<?=$arResult["NAV_STRING"]?>
