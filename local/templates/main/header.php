<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use \Bitrix\Main\Page\Asset;
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']))
{
    $APPLICATION->IncludeComponent(
        "bitrix:main.include",
        "",
        array(
            "AREA_FILE_SHOW" => "file",
            "PATH" => SITE_TEMPLATE_PATH."/ajax.php"
        ),
        false,
        array('HIDE_ICONS'=>'Y')
    );
}
$IsAdmin = $GLOBALS['USER']->IsAdmin();
$IsMain = $GLOBALS["APPLICATION"]->GetCurPage() == "/";
$IsCatalog = (strpos($GLOBALS['APPLICATION']->GetCurDir(), '/catalog/')!==false);
$Is404 = defined('ERROR_404') && ERROR_404 == 'Y';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?$APPLICATION->ShowHead();?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <?/*
    $APPLICATION->ShowCSS();
    $APPLICATION->ShowMeta('keywords');
    $APPLICATION->ShowMeta('description');
    if($IsAdmin || $IsCatalog)
    {
        $APPLICATION->ShowHeadStrings();
        $APPLICATION->ShowHeadScripts();
    }*/
    ?>
    <link rel="shortcut icon" href="<?=SITE_TEMPLATE_PATH?>/img/favicon.ico" type="image/x-icon">
    <title><? $APPLICATION->ShowTitle() ?></title>
<?
Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/css/swiper-bundle.min.css");
Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/css/form-components.css");
Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/css/jquery.fancybox.min.css");
Asset::getInstance()->addCss("https://api.mapbox.com/mapbox-gl-js/v1.12.0/mapbox-gl.css");
Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/css/main.css");
Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/css/custom.css");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/js/jquery-3.6.1.min.js");
?>
</head>
<body<? if($IsMain):?> class="index"<? endif;?><? if($Is404):?> class="error"<? endif;?>>
<?
$APPLICATION->IncludeFile(
	SITE_TEMPLATE_PATH."/include/jsscript.php",
    //SITE_TEMPLATE_PATH."/include/sprite_svg.php",
	Array(),
	Array("MODE"=>"html", "NAME"=>"текст", "SHOW_BORDER"=>false)
);
?>
<? $APPLICATION->ShowPanel() ?>
<div class="site-wrapper">
    <header class="header">
        <div class="h-top">
            <div class="container">
                <div class="ht-flex">
                    <div class="ht-col-l">
                        <a href="mailto:<?=CONTACT_EMAIL?>" class="ht-link"><?=CONTACT_EMAIL?></a>
                        <?//=ERROR_404?>
                    </div>
                    <div class="ht-col-r">
                        <div class="h-search-wrp">
                            <form action="/search/" class="h-search validation" method="post">
                                <button type="submit" class="h-search-submit">
                                    <i class="icon-search"></i>
                                </button>
                                <input type="search" data-parsley-errors-messages-disabled required name="q" placeholder="Поиск">
                                <button type="button" class="h-search-close">
                                    &times;
                                </button>
                            </form>
                        </div>
                        <a href="#contact-us" data-modal class="btn">Связаться с нами</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-main">
            <div class="container">
                <div class="hm-flex">
                    <div class="hm-col-l">
                        <a href="/" class="logo">
                            <svg width="194" viewBox="0 0 194 75">
                                <use xlink:href="#logo" width="64" height="45" />
                                <use xlink:href="#logo-text" height="65" />
                            </svg>
                        </a>
                    </div>
                    <div class="hm-col-c">
                        <a href="#" class="nav-btn">
                            <span class="nav-ico">
                                <i></i>
                            </span>
                        </a>
                        <?
                        $APPLICATION->IncludeComponent("bitrix:menu", "top", Array(
                              "ROOT_MENU_TYPE" => "top",
                              "MAX_LEVEL" => "1",
                              "CHILD_MENU_TYPE" => "",
                              "USE_EXT" => "Y",
                              "DELAY" => "N",
                              "ALLOW_MULTI_SELECT" => "Y",
                              "MENU_CACHE_TYPE" => "A",
                              "MENU_CACHE_TIME" => "3600",
                              "MENU_CACHE_USE_GROUPS" => "N",
                              ),
                              false
                          );
                        ?>
                    </div>
                    <div class="hm-col-r">
                        <a href="tel:<?=CAkon::setPhoneNum(CONTACT_PHONE)?>" class="hm-tel">
                            <i class="icon-phone"></i><span><?=CONTACT_PHONE?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <script>
            let headerObj = document.getElementsByClassName('header');
            hHeader();
            window.addEventListener('resize', function() {
                hHeader();
            });
            function hHeader() {
                if (!headerObj.length > 0) return false;
                setTimeout(function() {
                    document.documentElement.style.setProperty('--js-header-height', headerObj[0].offsetHeight + 'px');
                }, 0);
            }
        </script>
    </header>
    <main class="content">
        <? /*if((strpos($APPLICATION->GetCurDir(), '/info/')!==false)):?>
        <div class="container">
        <? endif*/?>