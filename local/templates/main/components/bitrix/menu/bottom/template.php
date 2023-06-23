<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>
<ul class="ft-nav-list custom">
<?
foreach($arResult as $arItem):
	if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
		continue;
?>
		<li><a href="<?=$arItem["LINK"]?>" class="fmt-link f-caption"><?=$arItem["TEXT"]?></a></li>
<?endforeach?>
</ul>
<?endif?>