<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
echo \Sepro\Helpers::printPre($arResult);
if(empty($arResult['ITEMS']))
{
    return false;
}?>
