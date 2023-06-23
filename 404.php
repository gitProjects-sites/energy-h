<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");

//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("404 Not Found");
?>
    <svg class="logo-decor" width="194" viewBox="0 0 64 45" preserveAspectRatio="xMaxYMin meet">
        <use xlink:href="#logo" />
    </svg>
    <div class="container">
        <div class="centered">
            <div class="err-code-flex">
                <div class="err-code">
                    4<span>0</span>4
                </div>
                <div class="err-title">
                    <div class="err-title-name">
                        error
                    </div>
                </div>
            </div>
            <div class="err-bottom-text">
                Этой страницы не существует или она была удалена
            </div>
        </div>
    </div>
<?

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>