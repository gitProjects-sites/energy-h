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
<section class="offers-section m-df">
    <h2 class="small">Наши предложения</h2>
    <div class="ou-slider offers-slider swiper">
        <div class="swiper-wrapper">
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
            ?>

                <div class="swiper-slide" id="<? echo $this->GetEditAreaId($arSection['ID']); ?>">
                    <a href="<? echo $arSection['SECTION_PAGE_URL']; ?>" class="offer-box">
                        <span class="offer-img">
                            <img src="<? echo $arSection['PICTURE']['SRC']; ?>" alt="">
                        </span>
                        <span class="offer-main">
                            <span class="offer-caption">
                                <?echo $arSection["NAME"]?>
                            </span>
                        </span>
                    </a>
                </div>
            <?}
            unset($arSection);?>
        </div>
    </div>
    <div class="ou-btn-wrp">
        <button class="ou-btn sw-button-prev"><i class="icon-arr-md-l"></i></button>
        <button class="ou-btn sw-button-next"><i class="icon-arr-md-r"></i></button>
    </div>
</section>

