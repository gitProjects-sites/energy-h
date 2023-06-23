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
$arItemsDATE = array();
$result = \Bitrix\Iblock\ElementTable::getList(array(
    'filter' => array(
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
    ),
    'select' => array(
        'ID',
        'IBLOCK_SECTION_ID',
        'ACTIVE_FROM',
    )
));
while($res = $result->fetch())
{
    $year = $res['ACTIVE_FROM']->format('Y');
    $arItemsDATE[$res['IBLOCK_SECTION_ID']][$year] = $year;
}
//echo Sepro\Helpers::printPre($arItemsDATE);
$strSectionEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT");
$strSectionDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE");
$arSectionDeleteParams = array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM'));
//echo Sepro\Helpers::printPre($arResult);
?>
<div class="ls-ctrl-col-l">
    <div class="ribbon">
        <div class="ribbon-wrp">
            <div class="ribbon-slider swiper">
                <div class="swiper-wrapper">
                    <?
                    $first = false;
                    $curr_sect = $arParams['CURR_SECTION'];
                    if(!$curr_sect)
                    {
                        $first = true;
                    }
                    foreach ($arResult['SECTIONS'] as &$arSection)
                    {
                        $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
                        $this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
                        ?>
                        <div class="swiper-slide<? if($first || $curr_sect == $arSection['ID']): $first = false; $curr_sect = $arSection['ID']?> active<? endif;?>" id="<? echo $this->GetEditAreaId($arSection['ID']); ?>">
                            <a href="<? echo $arSection['SECTION_PAGE_URL']; ?>" class="th-name">
                                <? echo $arSection['NAME']; ?>
                            </a>
                        </div>
                    <?
                    }
                    unset($arSection);
                    ?>
                </div>
            </div>
            <div class="ribbon-arr">
                <button class="sw-button-prev"><i class="icon-arr-md-l"></i></button>
                <button class="sw-button-next"><i class="icon-arr-md-r"></i></button>
            </div>
        </div>
    </div>
</div>
<div class="ls-ctrl-col-r">
    <div class="fc-component fc-select fc-placeholder-shift fc-select-default-js" data-fs-type="select-default">
        <button class="fc-select-title fc-select-title-js">
                <span class="fc-select-title-wrapper fc-select-title-wrapper-js">
                    <span class="fc-in-paceholder fc-select-title-placeholder-js">Год публикации</span>
                    <span class="fc-selected-text fc-selected-text-js"></span>
                </span>
        </button>
        <div class="fc-select-drop-down fc-select-drop-down-js">
            <div class="fc-select-scroll fc-select-scroll-js">
                <ul class="fc-select-list fc-select-list-js custom js-sel_year">
                    <li><button data-val="">Нет</button></li>
                    <? foreach ($arItemsDATE[$curr_sect] as $year):?>
                        <li><button data-val="<?=$year?>"><?=$year?></button></li>
                    <? endforeach;?>
                </ul>
            </div>
        </div>
        <!--current value-->
        <input class="fc-select-input-js" type="hidden" name="textfield">
    </div>
</div>