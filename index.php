<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("H-energy");
?>
<?
$APPLICATION->IncludeComponent(
    "bitrix:news.detail",
    "top_video",
    Array(
        "IBLOCK_TYPE" => 'catalog',
        "IBLOCK_ID" => IBLOCK_SITE_STORE,
        "FIELD_CODE" => array('DATE_CREATE'),
        "PROPERTY_CODE" => array('VIDEO'),
        "ACTIVE_DATE_FORMAT" => 'd.m.Y',
        "CACHE_TYPE" => 'A',
        "CACHE_TIME" => '36000000',
        "CACHE_GROUPS" => 'N',
        "USE_PERMISSIONS" => 'N',
        "DISPLAY_TOP_PAGER" => 'N',
        "DISPLAY_BOTTOM_PAGER" => 'N',
        "PAGER_SHOW_ALWAYS" => "N",
        "CHECK_DATES" => 'Y',
        "ELEMENT_ID" => ELEMENT_VIDEO,
        "IBLOCK_URL" => '',
        "USE_SHARE" => 'N',
        "SET_BROWSER_TITLE" => "N",
        "SET_TITLE" => "N",
    ),
    false
);
?>
<div class="container">
    <section class="ed-txt-section m-df">
    <?
    $APPLICATION->IncludeComponent(
        "bitrix:news.list",
        "main_about",
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
            "IBLOCK_ID" => "6",
            "IBLOCK_TYPE" => "services",
            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
            "INCLUDE_SUBSECTIONS" => "Y",
            "MESSAGE_404" => "",
            "NEWS_COUNT" => "20",
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
    </section>
    <div class="bg-box m-df">
        <?
        $APPLICATION->IncludeComponent(
        	"bitrix:catalog.section.list",
        	"main_catalog",
        	Array(
        		"ADD_SECTIONS_CHAIN" => "Y",
        		"CACHE_FILTER" => "N",
        		"CACHE_GROUPS" => "Y",
        		"CACHE_TIME" => "36000000",
        		"CACHE_TYPE" => "A",
        		"COUNT_ELEMENTS" => "Y",
        		"COUNT_ELEMENTS_FILTER" => "CNT_ACTIVE",
        		"FILTER_NAME" => "sectionsFilter",
        		"HIDE_SECTION_NAME" => "N",
        		"IBLOCK_ID" => IBLOCK_CATALOG,
        		"IBLOCK_TYPE" => "products",
        		"SECTION_CODE" => "",
        		"SECTION_FIELDS" => array("",""),
        		"SECTION_ID" => $_REQUEST["SECTION_ID"],
        		"SECTION_URL" => "",
        		"SECTION_USER_FIELDS" => array("",""),
        		"SHOW_PARENT_NAME" => "Y",
        		"TOP_DEPTH" => "2",
        		"VIEW_MODE" => "TILE"
        	)
        );
        ?>
    </div>
    <?
    $APPLICATION->IncludeComponent(
        "bitrix:news.list",
        "why_henergy",
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
            "IBLOCK_ID" => "7",
            "IBLOCK_TYPE" => "services",
            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
            "INCLUDE_SUBSECTIONS" => "Y",
            "MESSAGE_404" => "",
            "NEWS_COUNT" => "20",
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
    <blockquote class="type-2 m-df scroll-animate">
        <svg class="bl-logo-decor" width="194" viewBox="0 0 64 45" preserveAspectRatio="xMaxYMid meet">
            <use xlink:href="#logo" />
        </svg>
        <h2 class="small m9"><?
            $APPLICATION->IncludeFile(
            	SITE_TEMPLATE_PATH."/include/mission_head.php",
            	Array(),
            	Array("MODE"=>"html", "NAME"=>"заголовок")
            );
            ?></h2>
        <p>
            <?
            $APPLICATION->IncludeFile(
                SITE_TEMPLATE_PATH."/include/mission_txt.php",
                Array(),
                Array("MODE"=>"html", "NAME"=>"текст")
            );
            ?>
        </p>
        <div class="bl-wrapper">
            <div class="bl-text">
                <?
                $APPLICATION->IncludeFile(
                    SITE_TEMPLATE_PATH."/include/mission_citat.php",
                    Array(),
                    Array("MODE"=>"html", "NAME"=>"цитату")
                );
                ?>
            </div>
            <div class="bl-sign">
                <?
                $APPLICATION->IncludeFile(
                    SITE_TEMPLATE_PATH."/include/mission_auth.php",
                    Array(),
                    Array("MODE"=>"html", "NAME"=>"подпись")
                );
                ?>
            </div>
        </div>
    </blockquote>
    <?
    $APPLICATION->IncludeComponent(
        "bitrix:news.list",
        "main_news",
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
            "IBLOCK_ID" => IBLOCK_NEWS,
            "IBLOCK_TYPE" => "services",
            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
            "INCLUDE_SUBSECTIONS" => "Y",
            "MESSAGE_404" => "",
            "NEWS_COUNT" => "4",
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
            "SORT_ORDER2" => "DESC"
        )
    );
    ?>
    <?
    $APPLICATION->IncludeFile(
    	SITE_TEMPLATE_PATH."/include/contact_form.php",
    	Array(),
    	Array("MODE"=>"html", "NAME"=>"текст", "SHOW_BORDER"=>false)
    );
    ?>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>