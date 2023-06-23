<?
$phone = COption::GetOptionString('sepro.helper','PHONE');
$link_phone = preg_replace('/[^0-9]/', '', $phone);
?>
<? if($arParams['CLASS']=='strong'):?>
    <a href="tel:+<?=$link_phone?>"><strong><?=$phone?></strong></a>
<? else:?>
    <a href="tel:+<?=$link_phone?>" class="<?=$arParams['CLASS']?>">
        <? if(!empty($arParams['ICON'])):?>
            <i class="<?=$arParams['ICON']?>"></i>
            <span><?=$phone?></span>
        <? else:?>
            <?=$phone?>
        <? endif;?>
    </a>
<? endif;?>