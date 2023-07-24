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
<?foreach($arResult["ITEMS"] as $arItem):
    $color = 'currentColor';
    if(!empty($arItem['PROPERTIES']['COLOR']['VALUE']))
    {
        $color = $arItem['PROPERTIES']['COLOR']['VALUE'];
    }
    ?>
    <symbol id="<?=$arItem['CODE']?>" viewBox="0 0 175 157" xmlns="http://www.w3.org/2000/svg">
        <g fill="<?=$color?>">
            <?=$arItem['PREVIEW_TEXT']?>
        </g>
    </symbol>
<?endforeach;?>