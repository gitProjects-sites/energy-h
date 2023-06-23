<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//=Sepro\Helpers::printPre($arResult)?>
<?if (!empty($arResult)):?>
    <ul class="nav-list custom">
<?
foreach($arResult as $arItem):
	if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
		continue;
    if($arItem['DEPTH_LEVEL'] < $current_depth)
    {
        echo '</ul>';
    }
?>
    <? $class = '';
	    if($arItem['IS_PARENT'] || $arItem["SELECTED"])
        {
          $class = ' class="';
            if($arItem['IS_PARENT']) $class .= 'sublist';
            if($arItem['SELECTED']) $class .= ' active';
            $class .= '"';
        }
        ?>
    <li<?=$class?>>
        <a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
        <? if($arItem['IS_PARENT']):?>
            <ul class="custom">
        <? endif;?>
    </li>
    <? $current_depth = $arItem['DEPTH_LEVEL'];?>
<?endforeach?>
</ul>
<?endif?>