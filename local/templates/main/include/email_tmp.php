<?
    $email = COption::GetOptionString('sepro.helper','EMAIL');
?>
<? if($arParams['CLASS']=='strong'):?>
    <a href="mailto:<?=$email?>"><strong><?=$email?></strong></a>
<? else:?>
<a href="mailto:<?=$email?>" class="<?=$arParams['CLASS']?>">
    <?=$email?>
</a>
<? endif;?>