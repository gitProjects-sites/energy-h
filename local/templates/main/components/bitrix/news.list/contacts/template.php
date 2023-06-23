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

$res = CIBlock::GetByID($arParams['IBLOCK_ID']);
if($ar_res = $res->GetNext())
{
    $arResult['BUNNER_SRC'] = CFile::GetPath($ar_res['PICTURE']);
    $arResult['NAME'] = $ar_res['NAME'];
    $arResult['DESCRIPTION'] = $ar_res['DESCRIPTION'];
}
?>
<section class="top-section no-top-offset">
    <img src="<?=$arResult['BUNNER_SRC']?>" alt="">

    <div class="container">
        <div class="ts-main">
            <div class="ts-vertical">
                <div class="brc">Контакты</div>

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
<div class="container-decore container">
    <svg class="ab-logo-decor" width="194" viewBox="0 0 64 45" preserveAspectRatio="xMaxYMin meet">
        <use xlink:href="#logo" />
    </svg>
</div>
<div class="container">
    <section class="ct-main m-df">
        <h2>
            <?
            $APPLICATION->IncludeFile(
                SITE_TEMPLATE_PATH."/include/contacts/head.php",
                Array(),
                Array("MODE"=>"html", "NAME"=>"заголовок")
            );
            ?></h2>
        <p style="max-width: 1580px;">
            <?
            $APPLICATION->IncludeFile(
                SITE_TEMPLATE_PATH."/include/contacts/text.php",
                Array(),
                Array("MODE"=>"html", "NAME"=>"описание")
            );
            ?>
        </p>
    </section>
    <section class="ct-tel-section m-df">
        <a href="tel:<?=CAkon::setPhoneNum(CONTACT_PHONE)?>" class="ct-tel"><?=CONTACT_PHONE?></a>
        <div class="ct-sub-tel">
            <?
            $APPLICATION->IncludeFile(
                SITE_TEMPLATE_PATH."/include/contacts/phon_desc.php",
                Array(),
                Array("MODE"=>"html", "NAME"=>"описание")
            );
            ?>
        </div>
    </section>
<?
$arCoordProps = CAkon::getPropsIblock(IBLOCK_COORDS);
//echo Sepro\Helpers::printPre($arCoordProps);
$arSections = [];
foreach ($arResult['ITEMS'] as $arItem)
{
    //$arSections[$arItem['PROPERTIES']['SECTION']['VALUE_ENUM_ID']] = ['NAME' => $arItem['PROPERTIES']['SECTION']['VALUE'], 'ITEMS' => $arItem];
    $arSections[$arItem['PROPERTIES']['SECTION']['VALUE_ENUM_ID']][] = $arItem;
}
foreach ($arSections as $arSection)
{
    ?>
    <section class="m-df">
        <h2 class="small"><?=$arSection[0]['PROPERTIES']['SECTION']['VALUE']?></h2>
        <ul class="df-list custom">
            <?
            foreach ($arSection as $arElem):
                $coord_id = $arElem['PROPERTIES']['COORDS']['VALUE'];
                $arCoordProps[$coord_id]['WORKS'][] = $arElem['NAME'];
                ?>
            <li id="<?=$this->GetEditAreaId($arElem['ID']);?>">
                <div class="ctn-box">
                    <div class="ctn-img">
                        <a href="#" data-map-goTo-id="<?=$coord_id?>" class="ctn-img-inner">
                            <img src="<?=$arElem['PREVIEW_PICTURE']['SRC']?>" alt="">
                        </a>
                    </div>
                    <div class="ctn-main">
                        <div class="cnt-top">
                            <a href="#" data-map-goTo-id="<?=$coord_id?>" class="cnt-caption m1">
                                <?=$arCoordProps[$coord_id]['NAME']?>
                            </a>
                            <a href="#" data-map-goTo-id="<?=$coord_id?>" class="cnt-text fs18 m5" style="color: black">
                                <?=$arCoordProps[$coord_id][36][0]?>
                            </a>
                            <a href="#" data-map-goTo-id="<?=$coord_id?>" class="cnt-text fs18" style="color: black">
                                <?=$arElem['NAME']?>
                            </a>
                        </div>
                    </div>
                </div>
            </li>
            <?
                endforeach;
            ?>
        </ul>
    </section>
        <?
}
?>
    <section class="map-section m-df">
        <div id="map" class="map">
            <div class="map-scroll-overlay">
                Чтобы изменить масштаб, прокручивайте карту, удерживая клавишу Ctrl
            </div>
            <?

            ?>
            <script>
                let objects = [
                    //[0] id
                    //[1] lat
                    //[2] lng
                    //[3] popup
                    <? foreach ($arCoordProps as $id => $arCoordProp):
                    $html_works = '';
                    foreach ($arCoordProp['WORKS'] as $wname)
                    {
                        $html_works .= '<div class="map-type">'.$wname.'</div>';
                    }
                    ?>
                    [
                        <?=$id?>,
                        <?=$arCoordProp[33][0]?>,
                        <?=$arCoordProp[34][0]?>,
                        <? print '`<div class="map-popup">
                            <div class="map-caption">'.$arCoordProp['NAME'].'</div>
                            <div class="map-address">'.$arCoordProp[36][0].'</div>'
                            .$html_works.
                            '<a href="tel:'.CAkon::setPhoneNum($arCoordProp[38][0]).'" class="map-tel">'.$arCoordProp[38][0].'</a>
                            <div class="map-timework">'.$arCoordProp[37][0].'</div>
                            <div class="map-coordinates"><span>'.$arCoordProp[33][0].',</span> <span>'.$arCoordProp[34][0].'</span></div>
                        </div>`';?>
                    ],
                    <? endforeach;?>
                ];

            </script>
        </div>
    </section>
    <section class="contact-form">
        <h2 class="small">Связаться с нами</h2>
        <p class="m3" style="max-width: 1280px;">
            <?
            $APPLICATION->IncludeFile(
                SITE_TEMPLATE_PATH."/include/contacts/form_text.php",
                Array(),
                Array("MODE"=>"html", "NAME"=>"описание")
            );
            ?>
        </p>
        <form action="#" class="f-form validation js-form" method="post">
            <input type="hidden" name="AJAX" value="SEND" />
            <div class="f-item">
                <label class="fc-component fc-input fc-placeholder-shift err-target" data-fs-type="input-default">
                    <span class="fc-in-paceholder"><span>Ваше имя</span></span>
                    <input type="text" placeholder="" value="" name="name">
                </label>
            </div>
            <div class="f-item">
                <label class="fc-component fc-input fc-placeholder-shift err-target" data-fs-type="input-default">
                    <span class="fc-in-paceholder"><span>Ваша фамилия</span></span>
                    <input type="text" placeholder="" value="" name="famil">
                </label>
            </div>
            <div class="f-item">
                <label class="fc-component fc-input fc-placeholder-shift err-target" data-fs-type="input-default">
                    <span class="fc-in-paceholder"><span>Ваш e-mail</span></span>
                    <input type="email" placeholder="" value="" name="email">
                </label>
            </div>
            <div class="f-item">
                <label class="fc-component fc-input fc-placeholder-shift err-target" data-fs-type="input-default">
                    <span class="fc-in-paceholder"><span>Ваш телефон<b>*</b></span></span>
                    <input type="tel" placeholder="" data-parsley-pattern="\+[0-9]\s\([0-9]{3}\)\s[0-9]{3}-[0-9]{2}-[0-9]{2}" data-parsley-errors-messages-disabled required value="" name="phone">
                </label>
            </div>
            <div class="f-item full">
                <label class="fc-component fc-input fc-placeholder-shift err-target" data-fs-type="input-default">
                    <span class="fc-in-paceholder"><span>Ваша должность</span></span>
                    <input type="text" placeholder="" value="" name="position">
                </label>
            </div>
            <div class="f-item">
                <label class="fc-component fc-input fc-placeholder-shift err-target" data-fs-type="input-default">
                    <span class="fc-in-paceholder"><span>Наименование компании</span></span>
                    <input type="text" placeholder="" value="" name="company">
                </label>
            </div>
            <div class="f-item">
                <div class="fc-component fc-select fc-placeholder-shift fc-select-default-js" data-fs-type="select-default" data-fs-options='{"multiple": true}'>
                    <button class="fc-select-title fc-select-title-js">
                        <span class="fc-select-title-wrapper fc-select-title-wrapper-js">
                            <span class="fc-in-paceholder fc-select-title-placeholder-js">Тема</span>
                            <span class="fc-selected-text fc-selected-text-js"></span>
                        </span>
                    </button>
                    <div class="fc-select-drop-down fc-select-drop-down-js">
                        <div class="fc-select-scroll fc-select-scroll-js">
                            <ul class="fc-select-list fc-select-list-js custom">
                                <li><button data-val="Продукты"><span class="sl-ch"><i class="cr-icon"></i><span>Продукты</span></span></button></li>
                                <li><button data-val="Сервис"><span class="sl-ch"><i class="cr-icon"></i><span>Сервис</span></span></button></li>
                                <li><button data-val="Общие вопросы"><span class="sl-ch"><i class="cr-icon"></i><span>Общие вопросы</span></span></button></li>
                            </ul>
                        </div>
                    </div>
                    <!--								current value-->
                    <input class="fc-select-input-js" type="hidden" name="theme">
                </div>
            </div>
            <div class="f-item full">
                <label class="fc-component fc-textarea fc-input fc-placeholder-shift" data-fs-type="textarea-default">
                    <span class="fc-in-paceholder"><span>Ваш комментарий</span></span>
                    <textarea rows="1" name="comment" placeholder=""></textarea>
                </label>
            </div>
            <div class="f-item full form-btn-wrp">
                <input type="submit" class="btn" value="СВЯЗАТЬСЯ С НАМИ" name="button">
                <label class="cr err-target">
                    <input type="checkbox" checked data-parsley-required data-parsley-trigger="click" data-parsley-errors-messages-disabled name="checkbox">
                    <span class="cr-ps">
                        <i class="cr-icon"></i>
                        <span class="cr-text">
                            Нажимая на кнопку «Связаться с нами» Вы даете согласие на обработку <a href="/policy/">персональных данных</a>
                        </span>
                    </span>
                </label>
            </div>
        </form>
    </section>

</div>


