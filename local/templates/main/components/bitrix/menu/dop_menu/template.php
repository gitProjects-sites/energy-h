<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>
<?
foreach($arResult as $arItem):
	if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
		continue;
?>
<a href="<?=$arItem["LINK"]?>" class="link-tr">
    <span><?=$arItem["TEXT"]?></span>
    <i class="icon-arr-min-t-r"></i>
</a>
<?endforeach?>
<?endif?>