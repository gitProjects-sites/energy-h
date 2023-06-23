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
<section class="nw-section m-df">
    <div class="caption-wrp m10">
        <h2><?=$arResult['NAME']?></h2>
        <div class="caption-ot">
            <a href="/news/" class="color">Посмотреть все новости</a>
        </div>
    </div>
    <ul class="nw-list custom">
        <?foreach($arResult["ITEMS"] as $arItem):?>
        <?
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        /*$arImg = CFile::ResizeImageGet(
            $arItem["PREVIEW_PICTURE"]["ID"],
            array('width'=>200, 'height'=>200),
            BX_RESIZE_IMAGE_PROPORTIONAL,
            false,
            false,
            false,
            80
        );
        $arTime = explode(' ', $arItem['ACTIVE_FROM']);*/
        ?>
        <li id="<?=$this->GetEditAreaId($arItem['ID']);?>">
            <div class="nw-box">
                <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="nw-img">
                    <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="">
                </a>
                <div class="nw-main">
                    <time datetime="<?=$arItem['ACTIVE_FROM']?>"><?=$arItem['SECTION_NAME']?>, <?=$arItem['ACTIVE_FROM']?>, 2 минуты чтения</time>
                    <h3 class="caption3"><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a></h3>
                    <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="link-square">
                        <span>Узнать больше</span>
                        <i class="icon-arr-sm-r"></i>
                    </a>
                </div>
            </div>
        </li>
        <?endforeach;?>
    </ul>
</section>
