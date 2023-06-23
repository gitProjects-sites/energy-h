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
$more_img = is_array($arResult['DISPLAY_PROPERTIES']['MORE_PHOTO']['VALUE']);
$arNotNeedProp = ['MORE_PHOTO','DOCS'];
//echo Sepro\Helpers::printPre($arResult);
?>
    <div class="brc min-top-offset"><?=$arResult['NAME']?></div>

    <div class="rt-box-wrp">
        <div class="rt-box">
            <div class="th-name"><?=$arResult['SECTION']['PATH'][0]['NAME']?></div>
            <time datetime="<?=$arResult['ACTIVE_FROM']?>"><?=$arResult['ACTIVE_FROM']?>, 2 минуты чтения</time>
        </div>
    </div>

    <h1 class="caption2 bigger text-center" style="max-width: 1660px; margin-left: auto; margin-right: auto">
        <?=$arResult['NAME']?>
    </h1>

    <div class="fs-img">
        <img src="<?=$arResult['DETAIL_PICTURE']['SRC']?>" alt="">
    </div>

    <div class="container-small m-df">
        <div class="social-wrp">
            <div class="sc-title">Поделиться:</div>
            <?
            $APPLICATION->IncludeComponent("bitrix:main.share", "", array(
                "HANDLERS" => $arParams["SHARE_HANDLERS"],
                "PAGE_URL" => $arResult["~DETAIL_PAGE_URL"],
                "PAGE_TITLE" => $arResult["~NAME"],
                "SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
                "SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
                "HIDE" => $arParams["SHARE_HIDE"],
            ),
                $component//,
                //array("HIDE_ICONS" => "Y")
            );
            ?>
            <ul class="social-list custom">
                <li>
                    <a href="#" class="sc-box">
                        <svg width="23" viewBox="0 0 175 157">
                            <use xlink:href="#tlg" />
                        </svg>
                    </a>
                </li>
                <li>
                    <a href="#" class="sc-box">
                        <svg width="24" viewBox="0 0 183 183">
                            <use xlink:href="#wt" />
                        </svg>
                    </a>
                </li>
                <li>
                    <a href="#" class="sc-box">
                        <svg width="28" viewBox="0 0 216 133">
                            <use xlink:href="#vk" />
                        </svg>
                    </a>
                </li>
                <li>
                    <a href="#" class="sc-box">
                        <svg width="22" viewBox="0 0 166 172">
                            <use xlink:href="#email" />
                        </svg>
                    </a>
                </li>
            </ul>
        </div>
        <? if(!empty($arResult['PROPERTIES']['HEAD1']['VALUE'])):?>
            <h3>
                <?=$arResult['PROPERTIES']['HEAD1']['VALUE']?>
            </h3>
        <? endif;?>
        <? if(!empty($arResult['PROPERTIES']['TXT1']['VALUE'])):?>
            <p class="m2" style="font-weight: 300;">
                <?=$arResult['PROPERTIES']['TXT1']['~VALUE']['TEXT']?>
            </p>
        <? endif;?>
        <? if(!empty($arResult['DISPLAY_PROPERTIES']['IMAGE1']['FILE_VALUE']['SRC'])):?>
            <div class="img-full m2">
                <img src="<?=$arResult['DISPLAY_PROPERTIES']['IMAGE1']['FILE_VALUE']['SRC']?>" alt="">
            </div>
        <? endif;?>
        <? if(!empty($arResult['PROPERTIES']['TXT2']['VALUE'])):?>
        <?=$arResult['PROPERTIES']['TXT2']['~VALUE']['TEXT']?>
        <? endif;?>
        <? if(!empty($arResult['PROPERTIES']['HEAD2']['VALUE'])):?>
            <h3>
                <?=$arResult['PROPERTIES']['HEAD2']['VALUE']?>
            </h3>
        <? endif;?>
        <? if(!empty($arResult['PROPERTIES']['TXT3']['VALUE'])):?>
        <?=$arResult['PROPERTIES']['TXT3']['~VALUE']['TEXT']?>
        <? endif;?>
        <? if(!empty($arResult['DISPLAY_PROPERTIES']['IMAGE2']['FILE_VALUE']['SRC'])):?>
            <div class="img-full m2">
                <img src="<?=$arResult['DISPLAY_PROPERTIES']['IMAGE2']['FILE_VALUE']['SRC']?>" alt="">
            </div>
        <? endif;?>
        <? if(!empty($arResult['PROPERTIES']['TXT4']['VALUE'])):?>
        <?=$arResult['PROPERTIES']['TXT4']['~VALUE']['TEXT']?>
        <? endif;?>
        <? if(!empty($arResult['PROPERTIES']['HEAD3']['VALUE'])):?>
            <h3>
                <?=$arResult['PROPERTIES']['HEAD3']['VALUE']?>
            </h3>
        <? endif;?>
        <? if(!empty($arResult['PROPERTIES']['TXT5']['VALUE'])):?>
        <?=$arResult['PROPERTIES']['TXT5']['~VALUE']['TEXT']?>
        <? endif;?>
        <? if(!empty($arResult['PROPERTIES']['CITATE1']['VALUE'])):?>
            <blockquote>
                <div class="bl-text">
                    <?=$arResult['PROPERTIES']['CITATE1']['VALUE']?>
                </div>
                <div class="bl-sign">
                    <?=$arResult['PROPERTIES']['AUTH1']['VALUE']?>
                </div>
            </blockquote>
        <? endif;?>
        <? if(!empty($arResult['PROPERTIES']['HEAD4']['VALUE'])):?>
            <h3>
                <?=$arResult['PROPERTIES']['HEAD4']['VALUE']?>
            </h3>
        <? endif;?>
        <? if(!empty($arResult['PROPERTIES']['HEAD5']['VALUE'])):?>
            <h3>
                <?=$arResult['PROPERTIES']['HEAD5']['VALUE']?>
            </h3>
        <? endif;?>
        <? if(!empty($arResult['PROPERTIES']['TXT6']['VALUE'])):?>
        <?=$arResult['PROPERTIES']['TXT6']['~VALUE']['TEXT']?>
        <? endif;?>
        <? if(!empty($arResult['PROPERTIES']['CITATE2']['VALUE'])):?>
            <blockquote>
                <div class="bl-text">
                    <?=$arResult['PROPERTIES']['CITATE2']['VALUE']?>
                </div>
                <div class="bl-sign">
                    <?=$arResult['PROPERTIES']['AUTH2']['VALUE']?>
                </div>
            </blockquote>
        <? endif;?>
    </div>
    <? if(!empty($arResult['DISPLAY_PROPERTIES']['DOCS']['FILE_VALUE'])):?>
    <div class="bg-box">
        <h2 class="small">Документы</h2>
        <?
        $APPLICATION->IncludeFile(
            SITE_TEMPLATE_PATH."/include/doc_list.php",
            Array("DOCS" => $arResult['DISPLAY_PROPERTIES']['DOCS']['FILE_VALUE']),
            Array("MODE"=>"html", "NAME"=>"текст", "SHOW_BORDER"=>false)
        );
        ?>
    </div>
    <? endif;?>
