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
<ul class="doc-list custom js-prodlist">
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
    $arImg = CFile::ResizeImageGet(
        $arItem["PREVIEW_PICTURE"]["ID"],
		array('width'=>200, 'height'=>200),
		BX_RESIZE_IMAGE_PROPORTIONAL,
		false,
		false,
		false,
		80
		);
    $arTime = explode(' ', $arItem['ACTIVE_FROM']);
	?>
    <li id="<?=$this->GetEditAreaId($arItem['ID']);?>">
        <div class="doc-box js-doc_block" data-name="<?=$arItem['NAME']?>" data-src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>">
            <a href="#fdbk-modal" data-modal-open class="doc-img">
                <img src="<?=$arImg['src']?>" alt="">
            </a>
            <div class="doc-main">
                <a href="#fdbk-modal" data-modal-open class="doc-caption">
                    <?=$arItem['NAME']?>
                    <time datetime="<?=$arTime[0]?>"><?=$arTime[0]?></time>
                </a>
                <p>
                    <?=$arItem['PREVIEW_TEXT']?>
                </p>
            </div>
        </div>
    </li>
<?endforeach;?>
</ul>
<div class="js-navigation">
        <div class="pagination js-pages">
            <? //if($navParams['NavPageSize'] < $navParams['NavRecordCount']):?>
                <a href="#" class="btn btn-border js-more" style="min-width: 220px">Загрузить еще</a>
            <? //endif;?>
        </div>
    <div class="cs-invblll js-nav_string"><?=$arResult['NAV_STRING']?></div>
</div>

