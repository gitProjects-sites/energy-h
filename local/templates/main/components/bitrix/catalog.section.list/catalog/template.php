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


$strSectionEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT");
$strSectionDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE");
$arSectionDeleteParams = array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM'));
//echo Sepro\Helpers::printPre($arResult);
?>
<section class="top-section no-top-offset">
    <img src="<?=$arResult['BUNNER_SRC']?>" alt="">
    <div class="container">
        <div class="ts-main">
            <div class="ts-vertical">
                <div class="brc">
                    <ul class="custom breadcrumb">
                        <li><a href="/">Главная</a></li>
                        <li><span><?=$arResult['NAME']?></span></li>
                    </ul>
                </div>

                <h1 class="caption m2"><?=$arResult['NAME']?></h1>
                <p>
                    <?=$arResult['DESCRIPTION']?>
                </p>
            </div>
        </div>
        <div class="ts-bottom">
            <i class="mouse-ico"></i>
        </div>
    </div>
</section>
<div class="container">
    <section class="pr-section m-df">
        <div class="m-df">
            <?
            $APPLICATION->IncludeFile(
            	SITE_TEMPLATE_PATH."/include/catalog_txt.php",
            	Array(),
            	Array("MODE"=>"html", "NAME"=>"текст")
            );
            ?>
        </div>
    <?
if (0 < $arResult["SECTIONS_COUNT"])
{
?>
    <ul class="df-list list-4 custom">
<?
			foreach ($arResult['SECTIONS'] as &$arSection)
			{
				$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
				$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);

				if (false === $arSection['PICTURE'])
					$arSection['PICTURE'] = array(
						'SRC' => $arCurView['EMPTY_IMG'],
						'ALT' => (
							'' != $arSection["IPROPERTY_VALUES"]["SECTION_PICTURE_FILE_ALT"]
							? $arSection["IPROPERTY_VALUES"]["SECTION_PICTURE_FILE_ALT"]
							: $arSection["NAME"]
						),
						'TITLE' => (
							'' != $arSection["IPROPERTY_VALUES"]["SECTION_PICTURE_FILE_TITLE"]
							? $arSection["IPROPERTY_VALUES"]["SECTION_PICTURE_FILE_TITLE"]
							: $arSection["NAME"]
						)
					);
				?><li id="<? echo $this->GetEditAreaId($arSection['ID']); ?>">
                    <a href="<? echo $arSection['SECTION_PAGE_URL']; ?>" class="offer-box">
                        <span class="offer-img">
                            <img src="<? echo $arSection['PICTURE']['SRC']; ?>" alt="">
                        </span>
                        <span class="offer-main">
                            <span class="offer-caption">
                                <? echo $arSection['NAME']; ?>
                            </span>
                            <span class="offer-prop">
                                <? echo $arSection['DESCRIPTION']; ?>
                            </span>
                        </span>
                    </a>
                </li><?
			}
			unset($arSection);
?>
</ul>
<?
}
    ?>
    </section>
    <?
    $APPLICATION->IncludeFile(
        SITE_TEMPLATE_PATH."/include/contact_form.php",
        Array(),
        Array("MODE"=>"html", "NAME"=>"текст", "SHOW_BORDER"=>false)
    );
    ?>
</div>