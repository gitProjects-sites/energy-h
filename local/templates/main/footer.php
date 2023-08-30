<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
    </main>
    <? if(!$Is404):?>
    <footer class="footer">
        <div class="fm">
            <div class="container">
                <div class="ft-logo">
                    <div class="f-logo">
                        <svg width="194" viewBox="0 0 194 65">
                            <use xlink:href="#logo" width="64" height="45" />
                            <use xlink:href="#logo-text" height="65" />
                        </svg>
                    </div>
                    <a href="/policy/" class="policy">Политика конфиденциальности</a>
                    <div class="copy"><? $APPLICATION->IncludeFile(
                            SITE_TEMPLATE_PATH."/include/copyr.php",
                            Array(),
                            Array("MODE"=>"html", "NAME"=>"текст")
                        );?> <?=date('Y')?></div>
                </div>
                <div class="ft-col-l">
                    <div class="ft-ct-box">
                        <div class="f-caption">КОНТАКТНЫЙ ТЕЛЕФОН</div>
                        <a href="tel:<?=CAkon::setPhoneNum(CONTACT_PHONE)?>" class="f-caption"><?=CONTACT_PHONE?></a>
                        <a href="tel:<?=CAkon::setPhoneNum(CONTACT_PHONE2)?>" class="f-caption"><?=CONTACT_PHONE2?></a>
                        <a href="mailto:<?=CONTACT_EMAIL?>" class="f-caption"><?=CONTACT_EMAIL?></a>
                    </div>
                    <div class="ft-ct-box">
                        <div class="f-caption">НАШ АДРЕС</div>
                        <div class="fm-content">
                            <?=CONTACT_ADDRESS?>
                        </div>
                    </div>
                </div>
                <div class="ft-col-c">
                    <?
                    $APPLICATION->IncludeComponent("bitrix:menu", "bottom", Array(
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
                <div class="ft-col-r">
                    <?
                    $APPLICATION->IncludeComponent(
                        "bitrix:news.list",
                        "soc_links",
                        Array(
                            "ACTIVE_DATE_FORMAT" => "d.m.Y",
                            "ADD_SECTIONS_CHAIN" => "N",
                            "AJAX_MODE" => "N",
                            "AJAX_OPTION_ADDITIONAL" => "",
                            "AJAX_OPTION_HISTORY" => "N",
                            "AJAX_OPTION_JUMP" => "N",
                            "AJAX_OPTION_STYLE" => "Y",
                            "CACHE_FILTER" => "N",
                            "CACHE_GROUPS" => "N",
                            "CACHE_TIME" => "36000000",
                            "CACHE_TYPE" => "A",
                            "CHECK_DATES" => "Y",
                            "DETAIL_URL" => "",
                            "DISPLAY_BOTTOM_PAGER" => "N",
                            "DISPLAY_DATE" => "N",
                            "DISPLAY_NAME" => "N",
                            "DISPLAY_PICTURE" => "Y",
                            "DISPLAY_PREVIEW_TEXT" => "Y",
                            "DISPLAY_TOP_PAGER" => "N",
                            "FIELD_CODE" => array("",""),
                            "FILTER_NAME" => "",
                            "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                            "IBLOCK_ID" => "10",
                            "IBLOCK_TYPE" => "services",
                            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                            "INCLUDE_SUBSECTIONS" => "Y",
                            "MESSAGE_404" => "",
                            "NEWS_COUNT" => "200",
                            "PAGER_BASE_LINK_ENABLE" => "N",
                            "PAGER_DESC_NUMBERING" => "N",
                            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                            "PAGER_SHOW_ALL" => "N",
                            "PAGER_SHOW_ALWAYS" => "N",
                            "PAGER_TEMPLATE" => ".default",
                            "PAGER_TITLE" => "Новости",
                            "PARENT_SECTION" => "",
                            "PARENT_SECTION_CODE" => "",
                            "PREVIEW_TRUNCATE_LEN" => "",
                            "PROPERTY_CODE" => array("PRICE","LINK","OLD_PRICE",""),
                            "SET_BROWSER_TITLE" => "N",
                            "SET_LAST_MODIFIED" => "N",
                            "SET_META_DESCRIPTION" => "N",
                            "SET_META_KEYWORDS" => "N",
                            "SET_STATUS_404" => "N",
                            "SET_TITLE" => "N",
                            "SHOW_404" => "N",
                            "SORT_BY1" => "SORT",
                            "SORT_BY2" => "ID",
                            "SORT_ORDER1" => "ASC",
                            "SORT_ORDER2" => "ASC"
                        )
                    );
                    ?>
                    <?
                    $APPLICATION->IncludeComponent("bitrix:menu", "dop_menu", Array(
                        "ROOT_MENU_TYPE" => "dop",
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
            </div>
        </div>
        <div class="fb">
            <div class="container">
                <div class="fb-flex">
                    <div class="fb-dev-copy">
                        <div class="copy">
                            <? $APPLICATION->IncludeFile(
                        	SITE_TEMPLATE_PATH."/include/copyr.php",
                        	Array(),
                        	Array("MODE"=>"html", "NAME"=>"текст")
                        );?> <?=date('Y')?></div>
                    </div>
                    <div class="fb-dev-policy">
                        <a href="/policy/" class="policy">Политика конфиденциальности</a>
                    </div>
                    <div class="fb-dev-item">
                        <!--a href="https://akona.ru/" target="_blank" class="dev">
                            Разработка: <span>akona.ru</span>
                        </a-->
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <? endif;?>
</div>
<script>
    setScrollbarSize();
    window.addEventListener('resize', function() {
        setScrollbarSize();
    });
    function setScrollbarSize() {
        setTimeout(function() {
            document.documentElement.style.setProperty('--js-scrollbar-size', window.innerWidth - document.documentElement.clientWidth + 'px');
        }, 0);
    }
</script>

<?
//if (!isset($_COOKIE['BITRIX_SM_cookie_use']))
//{
?>
<div class="cookie-bar" style="display: none">
    <div class="container">
        <div class="cb-flex">
            <div class="cb-text">
                <?
                $APPLICATION->IncludeFile(
                	SITE_TEMPLATE_PATH."/include/cook_txt.php",
                	Array(),
                	Array("MODE"=>"html", "NAME"=>"текст")
                );
                ?>
            </div>
            <a href="#" class="btn js-cookie-close">
                ПРИНИМАЮ
            </a>
        </div>
    </div>
</div>
<?// } ?>
<div id="contact-us" class="modal" style="display: none;">
    <div class="m-caption caption2 small m1">Связаться с нами</div>
    <p class="m3">
        <?
        $APPLICATION->IncludeFile(
        	SITE_TEMPLATE_PATH."/include/form_txt.php",
        	Array(),
        	Array("MODE"=>"html", "NAME"=>"текст", "SHOW_BORDER"=>false)
        );
        ?>
    </p>
    <form action="#" class="f-form validation js-form" method="post">
        <input type="hidden" name="AJAX" value="SEND"/>
        <div class="f-item full">
            <label class="fc-component fc-input fc-placeholder-shift err-target" data-fs-type="input-default">
                <span class="fc-in-paceholder"><span>Ваше имя</span></span>
                <input type="text" placeholder="" value="" name="name">
            </label>
        </div>
        <div class="f-item full">
            <label class="fc-component fc-input fc-placeholder-shift err-target" data-fs-type="input-default">
                <span class="fc-in-paceholder"><span>Ваш телефон<b>*</b></span></span>
                <input type="tel" placeholder="" data-parsley-pattern="\+[0-9]\s\([0-9]{3}\)\s[0-9]{3}-[0-9]{2}-[0-9]{2}" data-parsley-errors-messages-disabled required value="" name="phone">
            </label>
        </div>
        <div class="f-item full form-btn-wrp">
            <input type="submit" class="btn" value="СВЯЗАТЬСЯ С НАМИ" name="button">
            <label class="cr err-target">
                <input type="checkbox" checked data-parsley-required data-parsley-trigger="click" data-parsley-errors-messages-disabled name="checkbox">
                <span class="cr-ps">
						<i class="cr-icon"></i>
						<span class="cr-text">
							Нажимая на кнопку «Связаться с нами» Вы даете согласие на обработку <a href="/policy/" target="_blank">персональных данных</a>
						</span>
					</span>
            </label>
        </div>
    </form>
</div>

<div id="request" class="modal" style="display: none;">
    <div class="top-img-head">
        <img src="img/tmp/img20.jpg" alt="">
        <div class="top-img-inner">
            <div class="m-caption caption2 small">Оформить заявку</div>
            <div class="m-subcaption">Вводы BRIT</div>
            <p>
                Производство: город Хотького, Россия
            </p>
            <p>
                Применение: масло, воздух
            </p>
        </div>
    </div>
    <p class="m3">
        <?
        $APPLICATION->IncludeFile(
            SITE_TEMPLATE_PATH."/include/form_txt.php",
            Array(),
            Array("MODE"=>"html", "NAME"=>"текст", "SHOW_BORDER"=>false)
        );
        ?>
    </p>
    <form action="#" class="f-form validation js-form" method="post">
        <input type="hidden" name="AJAX" value="SEND"/>
        <div class="f-item full">
            <label class="fc-component fc-input fc-placeholder-shift err-target" data-fs-type="input-default">
                <span class="fc-in-paceholder"><span>Ваше имя</span></span>
                <input type="text" placeholder="" value="" name="name">
            </label>
        </div>
        <div class="f-item full">
            <label class="fc-component fc-input fc-placeholder-shift err-target" data-fs-type="input-default">
                <span class="fc-in-paceholder"><span>Ваш телефон<b>*</b></span></span>
                <input type="tel" placeholder="" data-parsley-pattern="\+[0-9]\s\([0-9]{3}\)\s[0-9]{3}-[0-9]{2}-[0-9]{2}" data-parsley-errors-messages-disabled required value="" name="phone">
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
</div>
<script src="<?=SITE_TEMPLATE_PATH?>/js/ofi.min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.fancybox.min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/tua-body-scroll-lock.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/swiper-bundle.min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/smooth-scrollbar.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/parsley.min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/mutation-summary.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.inputmask.bundle.min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/form-components.js"></script>
<script src="https://api.mapbox.com/mapbox-gl-js/v1.12.0/mapbox-gl.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/main.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/custom.js?v=1.1"></script>
</body>
</html>
