<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

$frame = $this->createFrame()->begin();
$injectId = 'bigdata_recommeded_products_'.rand();
?>

<?if (isset($arResult['REQUEST_ITEMS']))
{
    CJSCore::Init(array('ajax'));

    // component parameters
    $signer = new \Bitrix\Main\Security\Sign\Signer;
    $signedParameters = $signer->sign(
        base64_encode(serialize($arResult['_ORIGINAL_PARAMS'])),
        'bx.bd.products.recommendation'
    );
    $signedTemplate = $signer->sign($arResult['RCM_TEMPLATE'], 'bx.bd.products.recommendation');

    ?>

    <div id="<?=$injectId?>" class="bigdata_recommended_products_container"></div>

    <script type="text/javascript">
        BX.ready(function(){
            bx_rcm_get_from_cloud(
                '<?=CUtil::JSEscape($injectId)?>',
                <?=CUtil::PhpToJSObject($arResult['RCM_PARAMS'])?>,
                {
                    'parameters':'<?=CUtil::JSEscape($signedParameters)?>',
                    'template': '<?=CUtil::JSEscape($signedTemplate)?>',
                    'site_id': '<?=CUtil::JSEscape(SITE_ID)?>',
                    'rcm': 'yes'
                }
            );
        });
    </script>

    <?
    $frame->end();
    return;
}

if(intval($arResult['IDS']) && is_array($arResult['IDS']))
{
    $GLOBALS['RECOMMEND_FILTER'] = array('=ID' => $arResult['IDS']);


    /*\Sepro\App::getInstance()->IncludeComponent(
        "sepro:standard.elements",
        "recommend",
        array(
            "COMPONENT_TEMPLATE" => "",
            "IBLOCK_TYPE" => "1c_catalog", // Тип инфоблока
            "PAGER_TEMPLATE" => "modern",
            "IBLOCK_ID" => "11", // Инфоблок
            "SHOW_NAV" => "N", // Постраничная навигация
            "SHOW_CHAINS" => "N",
            "COUNT" => $arParams['PAGE_ELEMENT_COUNT'], // Количество элементов
            "SORT_FIELD1" => "ID",    // Поле первой сортировки
            "SORT_DIRECTION1" => "ASC",    // Направление первой сортировки
            "SORT_FIELD2" => "TIMESTAMP_X",    // Поле второй сортировки
            "SORT_DIRECTION2" => "DESC", // Направление второй сортировки
            "CACHE_TYPE" => "N", // Тип кеширования
            "CACHE_TIME" => "3600",    // Время кеширования (сек.)
            "CHECK_MODULE_CATALOG" => "Y",    // Инфоблок является торговым каталогом,
            "CATALOG_GROUPS" => array(3), // Тип цены
            "FILTER_NAME" => "RECOMMEND_FILTER", // Название переменной для фильтрации елементов
            "ITEM_PROPERTIES" => array(162),
            "OFFER_PROPERTIES" => array(
                149,
                // ОБЩАЯ ХАРАКТЕРИСТИКА - 141
                152,
                // ЦВЕТ
                153,
                154
            )
        ),
        false
    );*/
}
$frame->end();?>