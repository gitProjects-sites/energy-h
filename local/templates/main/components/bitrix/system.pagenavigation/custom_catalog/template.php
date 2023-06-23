<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");
?>
<div class="pagination pg-wrp">
    <? if ($arResult["NavLastRecordShow"] !== $arResult["NavRecordCount"]) { ?>
        <?
        if (strpos($arResult['sUrlPath'], '/search/') !== false) { ?>
            <a href="<?= $arResult["sUrlPath"] ?>?<?= $arResult['NavQueryString'] ?>&PAGEN_<?= $arResult["NavNum"] ?>=<?= ($arResult["NavPageNomer"] + 1) ?>&AJAX=Y"
               class="btn btn-grey pg-more show_more_js">Показать еще</a>
        <? } else { ?>
            <a href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= ($arResult["NavPageNomer"] + 1) ?>&AJAX=Y"
               class="btn btn-grey pg-more show_more_js">Показать еще</a>
        <? } ?>
    <? } ?>
</div>