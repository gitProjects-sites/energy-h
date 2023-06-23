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
<section class="top-section main-top-screen">
    <h1 class="hidden"><?=$arResult["PREVIEW_TEXT"]?></h1>
    <video poster="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" playsinline autoplay loop muted>
        <source src="<?=$arResult["DISPLAY_PROPERTIES"]['VIDEO']['FILE_VALUE']['SRC']?>" type="video/mp4">
    </video>
    <div class="container">
        <div class="ts-main">

        </div>
        <div class="ts-bottom">
            <i class="mouse-ico"></i>
        </div>
    </div>
</section>