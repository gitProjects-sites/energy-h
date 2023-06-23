<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\Entity,
    \Bitrix\Main\UI;

class StandardElementsComponent extends CBitrixComponent
{
    protected $cacheKeys = array(); // кешируемые ключи arResult
    protected $cacheAddon = array(); // дополнительные параметры, от которых зависит кеш
    protected $nav = array(); // параметры постраничной навигации
    protected $sections = array(); // разделы инфоблоков
    protected $sku_pid = 0; // ID свойства привязки инфоблоков
    protected $iblock = array(); // Данные инфоблока
    protected $obQuery; // Сущность запросов
    protected $limit = 0;
    protected $offset = 0;

    protected $checkParams = array(
        'IBLOCK_TYPE' => array('type' => 'string'),
        'COUNT' => array('type' => 'int')
    );

    public $_return = false;

    // ПОДКЛЮЧАЕМ ЯЗЫКОВЫЕ ФАЙЛЫ
    public function onIncludeComponentLang()
    {
        Loc::setCurrentLang(LANGUAGE_ID);
        $this->includeComponentLang(basename(__FILE__), LANGUAGE_ID);
        Loc::loadMessages(__FILE__);
    }

    // ПРОВЕРЯЕТ PARAMS
    public function onPrepareComponentParams($params)
    {
        $params = array_replace_recursive(
            $params,
            array(
                'IBLOCK_TYPE' => trim($params['IBLOCK_TYPE']),
                'IBLOCK_ID' => intval($params['IBLOCK_ID']),
                'SECTION_ID' => !empty($params['SECTION_ID']) && is_array($params['SECTION_ID']) ? array_unique($params['SECTION_ID']) : array(),
                'ITEM_PROPERTIES' => !empty($params['ITEM_PROPERTIES']) && is_array($params['ITEM_PROPERTIES']) ? array_unique($params['ITEM_PROPERTIES']) : array(),
                'OFFER_PROPERTIES' => !empty($params['OFFER_PROPERTIES']) && is_array($params['OFFER_PROPERTIES']) ? array_unique($params['OFFER_PROPERTIES']) : array(),
                'RELATION_PROPS' => !empty($params['RELATION_PROPS']) && is_array($params['RELATION_PROPS']) ? array_unique($params['RELATION_PROPS']) : array(),
                'SORT_FIELD1' => mb_strlen($params['SORT_FIELD1']) ? $params['SORT_FIELD1'] : 'ID',
                'SORT_DIRECTION1' => $params['SORT_DIRECTION1'] == 'ASC' ? 'ASC' : 'DESC',
                'SORT_FIELD2' => mb_strlen($params['SORT_FIELD2']) ? $params['SORT_FIELD2'] : 'ID',
                'SORT_DIRECTION2' => $params['SORT_DIRECTION2'] == 'ASC' ? 'ASC' : 'DESC',
                'CHECK_MODULE_CATALOG' => $params['CHECK_MODULE_CATALOG'] == 'Y' ? 'Y' : 'N',
                'SHOW_NAV' => $params['SHOW_NAV'] == 'Y' ? 'Y' : 'N',
                'VARIABLE_NAV' => !empty($params['VARIABLE_NAV']) ? $params['VARIABLE_NAV'] : 'PAGEN',
                'CATALOG_GROUPS' => is_array($params['CATALOG_GROUPS']) ? array_unique($params['CATALOG_GROUPS']) : array(),
                'CACHE_TYPE' => $params['CACHE_TYPE'] == 'Y' ? 'Y' : 'N',
                'CACHE_TIME' => intval($params['CACHE_TIME']) > 0 ? intval($params['CACHE_TIME']) : 3600,
            )
        );

        $this->arResult['PARAMS'] = $params;
        return $params;
    }

    // ПРОВЕРЯЕТ ЧИТАТЬ ДАННЫЕ ИЗ КЕША
    protected function readDataFromCache()
    {
        if($this->arParams['CACHE_TYPE'] == 'N')
        {
            return true;
        }

        return $this->StartResultCache(false, $this->cacheAddon);
    }

    // КЭШИРУЕТ КЛЮЧИ МАССИВА
    protected function putDataToCache()
    {
        if (is_array($this->cacheKeys) && !empty($this->cacheKeys))
        {
            $this->arResult['CACHE_KEYS'] = $this->cacheKeys;
            $this->SetResultCacheKeys($this->cacheKeys);
        }
    }

    // ПРОВЕРЯЕТ ОБЯЗАТЕЛЬНЫЙ ВВОД ПАРАМЕТРОВ ИЗ $this->checkParams
    private function checkAutomaticParams()
    {
        foreach ($this->checkParams as $key => $params)
        {
            switch ($params['type'])
            {
                case 'int':

                    if (intval($this->arParams[$key]) <= 0)
                    {
                        throw new \Bitrix\Main\ArgumentTypeException($key, 'integer');
                    }
                    break;

                case 'string':

                    $this->arParams[$key] = htmlspecialchars(trim($this->arParams[$key]));
                    if (empty($this->arParams[$key]))
                    {
                        throw new \Bitrix\Main\ArgumentNullException($key);
                    }
                    break;

                case 'array':

                    if (!is_array($this->arParams[$key]))
                    {
                        throw new \Bitrix\Main\ArgumentTypeException($key, 'array');
                    }
                    break;

                default:
                    throw new \Bitrix\Main\ArgumentTypeException($key);
            }
        }
    }

    // ВЫПОЛНЯЕТ ДЕЙСТВИЯ НАД КЕШИРОВАНИЕМ
    protected function executeProlog()
    {
        $this->setFrameMode(true); // Голосуем за автокомпозит. Динамическая компонента
        if (intval($this->arParams['COUNT']) > 0)
        {
            $this->nav = new UI\PageNavigation($this->arParams['VARIABLE_NAV']);
            $this->nav->allowAllRecords(false)->setPageSize($this->arParams['COUNT'])->initFromUri();
            $this->limit = $this->nav->getLimit() + 1;
            $this->offset = $this->nav->getOffset();
        }

        if (!empty($this->arParams['CACHE_ADDON']))
        {
            $this->cacheAddon = $this->arParams['CACHE_ADDON'];
            $this->arResult['CACHE_ADDON'] = $this->cacheAddon;
        }
    }

    // ПОДГОТАВЛИВАЕМ SELECT
    protected function onPrepareSelect($check, $arSelect)
    {
        $arSelected = array();

        switch($check)
        {
            case 'ITEM':
            case 'OFFER':

                // НАДСТРОЙКА ДЛЯ ПОСТРАНИЧНОЙ НАВИГАЦИИ
                if($check === 'ITEM')
                {
                    $arSelected[] = new Entity\ExpressionField('FOUND_ROWS', 'SQL_CALC_FOUND_ROWS %s', 'ID');
                    $this->obQuery->setLimit($this->limit);
                    $this->obQuery->setOffset($this->offset);
                }

                // НАДСТРОЙКА ДЛЯ СВОЙСТВ
                if(!empty($this->arParams[$check.'_PROPERTIES']))
                {
                    $arProperties = array();
                    $arHLTables = array();
                    $rsProperties = \Bitrix\Iblock\PropertyTable::GetList(
                        array(
                            'order' => array('ID' => 'DESC'),
                            'filter' => array('=ID' => $this->arParams[$check.'_PROPERTIES']),
                            'select' => array(
                                'ID',
                                'NAME',
                                'SORT',
                                'CODE',
                                'HINT',
                                'PROPERTY_TYPE',
                                'LIST_TYPE',
                                'MULTIPLE',
                                'IS_REQUIRED',
                                'USER_TYPE',
                                'USER_TYPE_SETTINGS'
                            )
                        )
                    );

                    while($arProperty = $rsProperties->fetch())
                    {
                        if(!empty($arProperty['USER_TYPE_SETTINGS']))
                        {
                            $arProperty['USER_TYPE_SETTINGS'] = unserialize($arProperty['USER_TYPE_SETTINGS']);
                        }

                        switch($arProperty['USER_TYPE'])
                        {
                            case 'directory': // Если обнаружили своство типа справочник

                                if(strlen($arProperty['USER_TYPE_SETTINGS']['TABLE_NAME']) > 0)
                                {
                                    $arHLTables[$arProperty['USER_TYPE_SETTINGS']['TABLE_NAME']] = $arProperty['ID'];
                                }

                                break;
                        }

                        $arProperties[$arProperty['ID']] = $arProperty;
                    }

                    if(!empty($arHLTables))
                    {
                        $rsHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::GetList(array(
                            'filter' => array('=TABLE_NAME' => array_keys($arHLTables)),
                            'select' => array('*')
                        ));

                        while($arHLBlock = $rsHLBlock->fetch())
                        {
                            $arProperties[$arHLTables[$arHLBlock['TABLE_NAME']]]['HL_BLOCK'] = $arHLBlock;
                        }
                    }

                    $this->arResult['PROPERTIES'][$check] = $arProperties;
                }

                $arSelected = array_merge($arSelected, $arSelect);

                break;
        }

        $this->obQuery->setSelect($arSelected);
    }

    // ПОДГОТАВЛИВАЕМ FILTER
    protected function onPrepareFilter($check, $arFilter)
    {
        switch($check)
        {
            case 'ITEM':
            case 'OFFER':

                if($check == 'ITEM')
                {
                    if(!empty($GLOBALS[$this->arParams['FILTER_NAME']]))
                    {
                        switch($this->arParams['FILTER_NAME'])
                        {
                            case 'arrFilter':

                                $rsElements = \CIBlockElement::GetList(
                                    array(),
                                    array_merge($arFilter, $GLOBALS[$this->arParams['FILTER_NAME']]),
                                    false,
                                    false,
                                    array('ID')
                                );

                                while($arElement = $rsElements->fetch())
                                {
                                    $arFilter['=ID'][] = $arElement['ID'];
                                }

                                break;

                            default:

                                $arFilter = array_merge($arFilter, $GLOBALS[$this->arParams['FILTER_NAME']]);

                                break;
                        }
                    }

                    if($this->arParams['IBLOCK_ID'] > 0)
                    {
                        $arFilter['=IBLOCK_ID'] = $this->arParams['IBLOCK_ID'];

                        if($this->arParams['CHECK_MODULE_CATALOG'] == 'Y')
                        {
                            $arFilter['CATALOG.AVAILABLE'] = 'Y';
                        }

                        if(!empty($this->sections[$this->arParams['IBLOCK_ID']]) && !empty($this->arParams['SECTION_ID']))
                        {
                            $arFilter['=IBLOCK_SECTION_ID'] = array();

                            foreach($this->arParams['SECTION_ID'] as $NID)
                            {
                                if(!empty($this->sections[$this->arParams['IBLOCK_ID']]['SECTIONS'][$NID]['CHILDRENS']))
                                {
                                    $arFilter['=IBLOCK_SECTION_ID'] = array_merge(
                                        explode(';', $this->sections[$this->arParams['IBLOCK_ID']]['SECTIONS'][$NID]['CHILDRENS']),
                                        $arFilter['=IBLOCK_SECTION_ID']
                                    );

                                    continue;
                                }

                                $arFilter['=IBLOCK_SECTION_ID'][] = $NID;
                            }
                        }
                    }
                }
                // Учитываем начало и окончание активности
                $arFilter[] = array(
                    'LOGIC' => 'AND',
                    array(
                        'LOGIC' => 'OR',
                        '>=ACTIVE_TO' => new \Bitrix\Main\Type\DateTime(),
                        'ACTIVE_TO' => null,
                    ),
                    array(
                        'LOGIC' => 'OR',
                        '<=ACTIVE_FROM' => new \Bitrix\Main\Type\DateTime(),
                        'ACTIVE_FROM' => null,
                    )
                );
                break;
        }
        $this->obQuery->setFilter($arFilter);
    }

    // КАСТОМИЗИРУЕМ GETLIST
    protected function GetListElements($check, $arSort, $arFilter, $arSelect)
    {
        switch($check)
        {
            case 'ITEM':
            case 'OFFER':

                $this->obQuery = new Entity\Query(Sepro\ElementTable::getEntity());

                if($check == 'ITEM')
                {
                    $this->obQuery->countTotal(true);

                    if(!empty($GLOBALS[$this->arParams['RUNTIME_FIELDS']]))
                    {
                        foreach($GLOBALS[$this->arParams['RUNTIME_FIELDS']] as $field => $arRuntime)
                        {
                            $this->obQuery->registerRuntimeField($field, $arRuntime);
                            $this->arResult['RUNTIME'][$check][$field] = $arRuntime;
                        }
                    }
                }

                break;
        }

        $this->obQuery->setOrder($arSort);
        $this->onPrepareFilter($check, $arFilter);
        $this->onPrepareSelect($check, $arSelect);

        $this->arResult['ORDER'][$check] = $this->obQuery->getOrder();
        $this->arResult['FILTER'][$check] = $this->obQuery->getFilter();
        $this->arResult['SELECT'][$check] = $this->obQuery->getSelect();

        return $this->obQuery->exec();
    }

    // СБОР ДАННЫХ
    protected function getResult()
    {
        $this->sections = \Sepro\IBlock::getSections();
        $ORMProperties = \Sepro\ORMFactory::compile('b_iblock_element_property');
        $nItemCount = 0;

        if($this->arParams['IBLOCK_ID'] > 0)
        {
            $this->iblock = \Sepro\IBlock::getIBlock($this->arParams['IBLOCK_ID']);

            if($this->iblock['SKU_PROPERTY_ID'] > 0)
            {
                $this->sku_pid = $this->iblock['SKU_PROPERTY_ID'];
            }

            $this->arResult['IBLOCK'] = $this->iblock;
        }

        // СОБИРАЕМ ЭЛЕМЕНТЫ
        $rsElements = $this->GetListElements(
            'ITEM',
            array(
                $this->arParams['SORT_FIELD1'] => $this->arParams['SORT_DIRECTION1'],
                $this->arParams['SORT_FIELD2'] => $this->arParams['SORT_DIRECTION2']
            ),
            array(
                'ACTIVE' => 'Y'
            ),
            array(
                'ID',
                'IBLOCK_ID',
                'NAME',
                'CODE',
                'PREVIEW_PICTURE',
                'DETAIL_PICTURE',
                'DATE_CREATE',
                'ACTIVE_FROM',
                'ACTIVE_TO',
                'TIMESTAMP_X',
                'PREVIEW_TEXT',
                'PREVIEW_TEXT_TYPE',
                'DETAIL_TEXT',
                'DETAIL_TEXT_TYPE'
            )
        );

        $this->arResult['TOTAL_ELEMENTS'] = $rsElements->getCount();

        if($this->arParams['SHOW_NAV'] == 'Y' && $this->arParams['COUNT'] > 0 && $this->arResult['TOTAL_ELEMENTS'] > 0)
        {
            $this->nav->setRecordCount($this->arResult['TOTAL_ELEMENTS']);
        }

        while ($arElement = $rsElements->fetch())
        {
            $nItemCount++;
            if($this->arParams['COUNT'] > 0 && $nItemCount > $this->arParams['COUNT'])
            {
                break;
            }

            $this->arResult['ITEMS'][$arElement['ID']] = $arElement;
            $this->arResult['NID_ELEMENTS'][$arElement['ID']] = $arElement['ID'];
        }

        // ЗАПИСЫВАЕМ КОРРЕКТНЫЕ РАЗДЕЛЫ ДЛЯ ЭЛЕМЕНТОВ
        if(!empty($this->arResult['NID_ELEMENTS']))
        {
            $rsElementSections = \Bitrix\Iblock\SectionElementTable::GetList(
                array(
                    'order' => array('IBLOCK_SECTION_ID' => 'DESC'),
                    'filter' => array('IBLOCK_ELEMENT_ID' => $this->arResult['NID_ELEMENTS']),
                    'select' => array(
                        'SID' => 'IBLOCK_SECTION_ID',
                        'EID' => 'IBLOCK_ELEMENT_ID'
                    )
                )
            );

            while($arElementSection = $rsElementSections->fetch())
            {
                $this->arResult['ITEMS'][$arElementSection['EID']]['IBLOCK_SECTION_ID'][$arElementSection['SID']] = $arElementSection['SID'];
                $this->arResult['NID_SECTIONS'][$arElementSection['SID']] = $arElementSection['SID'];
            }
        }

        // УСТАНАВЛИВАЕМ ЗНАЧЕНИЯ СВОЙСТВ ЭЛЕМЕНТОВ
        if(!empty($this->arResult['NID_ELEMENTS']) && !empty($this->arResult['PROPERTIES']['ITEM']))
        {
            $rsElementProperties = $ORMProperties::GetList(
                array(
                    'order' => array('IBLOCK_ELEMENT_ID' => 'ASC'),
                    'filter' => array(
                        '=IBLOCK_ELEMENT_ID' => $this->arResult['NID_ELEMENTS'],
                        '=IBLOCK_PROPERTY_ID' => array_keys($this->arResult['PROPERTIES']['ITEM'])
                    ),
                    'select' => array(
                        'ID',
                        'IBLOCK_ELEMENT_ID',
                        'IBLOCK_PROPERTY_ID',
                        'DESCRIPTION',
                        'VALUE'
                    )
                )
            );

            while($arElementProperty = $rsElementProperties->fetch())
            {
                $value = $arElementProperty['VALUE'];

                if(strlen($arElementProperty['DESCRIPTION']) > 0)
                {
                    $value = array(
                        'VALUE' => $arElementProperty['VALUE'],
                        'DESCRIPTION' => $arElementProperty['DESCRIPTION'],
                    );
                }

                $this->arResult['ITEMS'][$arElementProperty['IBLOCK_ELEMENT_ID']]['PROPERTIES'][$arElementProperty['IBLOCK_PROPERTY_ID']][$arElementProperty['ID']] = $value;
                $this->arResult['PROPERTIES']['ITEM'][$arElementProperty['IBLOCK_PROPERTY_ID']]['VALUES'][$arElementProperty['ID']] = $arElementProperty['VALUE'];

                if(in_array($this->arResult['PROPERTIES']['ITEM'][$arElementProperty['IBLOCK_PROPERTY_ID']]['PROPERTY_TYPE'], array('E')))
                {
                    $this->arResult['PROPERTIES']['ITEM'][$arElementProperty['IBLOCK_PROPERTY_ID']]['ELEMENTS'][$arElementProperty['VALUE']][$arElementProperty['IBLOCK_ELEMENT_ID']] = $arElementProperty['IBLOCK_ELEMENT_ID'];
                }
            }
        }

        // ЗАПИСЫВАЕМ ОСТАТКИ НА СКЛАДАХ ЕСЛИ ИНФОБЛОК ЯВЛЯЕТСЯ ТОРГОВЫМ КАТАЛОГОМ И УСТАНАВЛИВАЕМ ЦЕНЫ
        if($this->arParams['CHECK_MODULE_CATALOG'] == 'Y')
        {
            // УСТАНАВЛИВАЕМ ЦЕНЫ
            if(!empty($this->arParams['CATALOG_GROUPS']))
            {
                $rsElementPrices = \Bitrix\Catalog\PriceTable::GetList(
                    array(
                        'order' => array('PRICE' => 'ASC'),
                        'filter' => array(
                            '=PRODUCT_ID' => $this->arResult['NID_ELEMENTS'],
                            '=CATALOG_GROUP_ID' => $this->arParams['CATALOG_GROUPS']
                        ),
                        'select' => array(
                            'ID',
                            'PRODUCT_ID',
                            'CATALOG_GROUP_ID',
                            'PRICE',
                            'CURRENCY',
                            'TIMESTAMP_X'
                        )
                    )
                );

                while($arElementPrices = $rsElementPrices->fetch())
                {
                    $this->arResult['ITEMS'][$arElementPrices['PRODUCT_ID']]['PRICES'][$arElementPrices['CATALOG_GROUP_ID']][$arElementPrices['ID']] = array(
                        'ID' => intval($arElementPrices['ID']),
                        'TIMESTAMP_X' => $arElementPrices['TIMESTAMP_X'],
                        'PRODUCT_ID' => intval($arElementPrices['PRODUCT_ID']),
                        'CATALOG_GROUP_ID' => intval($arElementPrices['CATALOG_GROUP_ID']),
                        'PRICE' => $arElementPrices['PRICE'],
                        'CURRENCY' => $arElementPrices['CURRENCY']
                    );

                    $this->arResult['ITEMS'][$arElementPrices['PRODUCT_ID']]['CURRENT_PRICES'][$arElementPrices['PRODUCT_ID']] = floatval($arElementPrices['PRICE']);
                }
            }

            // ЗАПИСЫВАЕМ ОСТАТКИ
            $rsElementStores = \Bitrix\Catalog\StoreProductTable::GetList(
                array(
                    'runtime' => array(
                        'PRICE' => array(
                            'data_type' => '\Bitrix\Catalog\PriceTable',
                            'reference' => array(
                                '=ref.PRODUCT_ID' => 'this.PRODUCT_ID',
                            )
                        )
                    ),
                    'order' => array(
                        'PRICE.PRICE' => 'ASC',
                        'PRODUCT_ID' => "ASC"
                    ),
                    'filter' => array(
                        '=PRODUCT_ID' => $this->arResult['NID_ELEMENTS'],
                        '=PRICE.CATALOG_GROUP_ID' => $this->arParams['CATALOG_GROUPS'],
                        '>AMOUNT' => 0
                    ),
                    'select' => array(
                        'ID',
                        'PRODUCT_ID',
                        'STORE_ID',
                        'AMOUNT'
                    )
                )
            );

            while($arElementStore = $rsElementStores->fetch())
            {
                $this->arResult['ITEMS'][$arElementStore['PRODUCT_ID']]['STORES'][$arElementStore['STORE_ID']][$arElementStore['ID']] = array(
                    'ID' => intval($arElementStore['ID']),
                    'STORE_ID' => intval($arElementStore['STORE_ID']),
                    'AMOUNT' => intval($arElementStore['AMOUNT'])
                );

                $this->arResult['ITEMS'][$arElementStore['PRODUCT_ID']]['AMOUNTS'][$arElementStore['STORE_ID']] += $arElementStore['AMOUNT'];
                $this->arResult['ITEMS'][$arElementStore['PRODUCT_ID']]['TOTAL'] += $arElementStore['AMOUNT'];
            }
        }

        // ПРОДОЛЖАЕМ РАБОТУ С ТОРГОВЫМИ ПРЕДЛОЖЕНИЯМИ
        if($this->sku_pid > 0 && $this->arParams['CHECK_MODULE_CATALOG'] == 'Y')
        {
            $arProducts = array();
            $rsElementProperties = $ORMProperties::GetList(
                array(
                    'order' => array('VALUE' => 'ASC'),
                    'filter' => array(
                        '=IBLOCK_PROPERTY_ID' => $this->sku_pid,
                        '=VALUE' => $this->arResult['NID_ELEMENTS']
                    ),
                    'select' => array('VALUE', 'IBLOCK_ELEMENT_ID')
                )
            );

            while($arElementPropertySKU = $rsElementProperties->fetch())
            {
                $this->arResult['NID_OFFERS'][$arElementPropertySKU['IBLOCK_ELEMENT_ID']] = $arElementPropertySKU['IBLOCK_ELEMENT_ID'];
                $arProducts[$arElementPropertySKU['IBLOCK_ELEMENT_ID']] = $arElementPropertySKU['VALUE'];
            }

            // ДОСТАЕМ ТОРГОВЫЕ ПРЕДЛОЖЕНИЯ
            if(!empty($this->arResult['NID_OFFERS']))
            {
                $rsOffers = $this->GetListElements(
                    'OFFER',
                    array('PRICE.PRICE' => 'ASC', 'ID' => 'ASC'),
                    array(
                        'ACTIVE' => 'Y',
                        '=ID' => $this->arResult['NID_OFFERS']
                    ),
                    array(
                        'ID',
                        'IBLOCK_ID',
                        'NAME',
                        'DETAIL_PICTURE',
                        'PREVIEW_PICTURE'
                    )
                );

                while($arOffer = $rsOffers->fetch())
                {
                    $NIDPicture = intval($arOffer['PREVIEW_PICTURE']) > 0 ? $arOffer['PREVIEW_PICTURE'] : $arOffer['DETAIL_PICTURE'];

                    if(intval($NIDPicture) > 0)
                    {
                        $arOffer['PICTURE'] = array(
                            'ID' => $NIDPicture,
                            'ORIGINAL' => CFile::GetPath($NIDPicture)
                        );

                        if(is_file(DOCUMENT_ROOT.$arOffer['PICTURE']['ORIGINAL']))
                        {
                            $arOffer['PICTURE']['RESIZE'] = CFile::ResizeImageGet(
                                $NIDPicture,
                                array(
                                    'width' => 300,
                                    'height' => 300
                                ),
                                BX_RESIZE_IMAGE_PROPORTIONAL,
                                false
                            );
                        }
                        else
                        {
                            unset($arOffer['PICTURE']);
                        }
                    }

                    $this->arResult['ITEMS'][$arProducts[$arOffer['ID']]]['OFFERS'][$arOffer['ID']] = $arOffer;
                }

                // УСТАНАВЛИВАЕМ ЗНАЧЕНИЯ СВОЙСТВ ТОРГОВЫХ ПРЕДЛОЖЕНИЙ
                if(!empty($this->arResult['PROPERTIES']['OFFER']))
                {
                    $rsElementProperties = $ORMProperties::GetList(
                        array(
                            'order' => array('IBLOCK_ELEMENT_ID' => 'ASC'),
                            'filter' => array(
                                '=IBLOCK_ELEMENT_ID' => $this->arResult['NID_OFFERS'],
                                '=IBLOCK_PROPERTY_ID' => array_keys($this->arResult['PROPERTIES']['OFFER'])
                            ),
                            'select' => array(
                                'ID',
                                'NID' => 'IBLOCK_ELEMENT_ID',
                                'PID' => 'IBLOCK_PROPERTY_ID',
                                'VALUE'
                            )
                        )
                    );

                    while($arElementProperty = $rsElementProperties->fetch())
                    {
                        // ТОРГОВОЕ ПРЕДЛОЖЕНИЕ НЕ АКТИВНО
                        $PNID = $arProducts[$arElementProperty['NID']];
                        if(empty($this->arResult['ITEMS'][$PNID]['OFFERS'][$arElementProperty['NID']]))
                        {
                            continue;
                        }

                        $this->arResult['ITEMS'][$PNID]['OFFERS'][$arElementProperty['NID']]['PROPERTIES'][$arElementProperty['PID']][$arElementProperty['ID']] = $arElementProperty['VALUE'];
                        $this->arResult['PROPERTIES']['OFFER'][$arElementProperty['PID']]['VALUES'][$arElementProperty['ID']] = $arElementProperty['VALUE'];

                        // ЯВЛЯЕТСЯ ЛИ ЦЕНА ЗАВИСИМОЙ ОТ СВОЙСТВА
                        if(in_array($arElementProperty['PID'], $this->arParams['RELATION_PROPS']))
                        {
                            $value = $this->arResult['ITEMS'][$PNID]['OFFER_PROPERTIES'][$arElementProperty['PID']][$arElementProperty['NID']];

                            if(!empty($value)) // ЕСЛИ СВОЙСТВО МНОЖЕСТВЕННОЕ
                            {
                                $arElementProperty['VALUE'] = $value.';'.$arElementProperty['VALUE'];
                            }

                            // УСТАНАВЛИВАЕМ СВОЙСТВА ТОРГОВЫХ ПРЕДЛОЖЕНИЙ ДЛЯ СВЯЗКИ С ТОВАРАМИ
                            $this->arResult['ITEMS'][$PNID]['OFFER_PROPERTIES'][$arElementProperty['PID']][$arElementProperty['NID']] = $arElementProperty['VALUE'];

                            // УСТАНАВЛИВАЕМ СВОЙСТВА ТОРГОВЫХ ПРЕДЛОЖЕНИЙ ДЛЯ ОПРЕДЕЛЕНИЯ В JS
                            if(
                                $this->arResult['PROPERTIES']['OFFER'][$arElementProperty['PID']]['PROPERTY_TYPE'] == 'L' ||
                                $this->arResult['PROPERTIES']['OFFER'][$arElementProperty['PID']]['USER_TYPE'] == 'directory'
                            )
                            {
                                $this->arResult['ITEMS'][$PNID]['OFFERS_TO_JS'][$arElementProperty['NID']][$arElementProperty['PID']] = $arElementProperty['VALUE'];
                            }
                        }
                    }
                }

                // УСТАНАВЛИВАЕМ ЦЕНЫ ДЛЯ ТОРГОВЫХ ПРЕДЛОЖЕНИЙ
                if(!empty($this->arParams['CATALOG_GROUPS']))
                {
                    $rsElementPrices = \Bitrix\Catalog\PriceTable::GetList(
                        array(
                            'order' => array('PRICE' => 'ASC'),
                            'filter' => array(
                                '=PRODUCT_ID' => $this->arResult['NID_OFFERS'],
                                '=CATALOG_GROUP_ID' => $this->arParams['CATALOG_GROUPS']
                            ),
                            'select' => array('ID', 'PRODUCT_ID', 'CATALOG_GROUP_ID', 'PRICE', 'CURRENCY', 'TIMESTAMP_X')
                        )
                    );

                    while($arElementPrices = $rsElementPrices->fetch())
                    {
                        // ТОРГОВОЕ ПРЕДЛОЖЕНИЕ НЕ АКТИВНО
                        if(empty($this->arResult['ITEMS'][$arProducts[$arElementPrices['PRODUCT_ID']]]['OFFERS'][$arElementPrices['PRODUCT_ID']]))
                        {
                            continue;
                        }

                        $arPrice = array(
                            'ID' => intval($arElementPrices['ID']),
                            'TIMESTAMP_X' => $arElementPrices['TIMESTAMP_X'],
                            'PRODUCT_ID' => intval($arElementPrices['PRODUCT_ID']),
                            'CATALOG_GROUP_ID' => intval($arElementPrices['CATALOG_GROUP_ID']),
                            'PRICE' => $arElementPrices['PRICE'],
                            'CURRENCY' => $arElementPrices['CURRENCY']
                        );

                        $this->arResult['ITEMS'][$arProducts[$arPrice['PRODUCT_ID']]]['OFFERS'][$arPrice['PRODUCT_ID']]["PRICES"][$arPrice['CATALOG_GROUP_ID']][$arPrice['ID']] = $arPrice;
                        $this->arResult['ITEMS'][$arProducts[$arPrice['PRODUCT_ID']]]['OFFERS'][$arPrice['PRODUCT_ID']]["CURRENT_PRICES"][$arPrice['PRODUCT_ID']] = floatval($arPrice['PRICE']);

                        $this->arResult['ITEMS'][$arProducts[$arPrice['PRODUCT_ID']]]['PRICES'][$arPrice['CATALOG_GROUP_ID']][$arPrice['ID']] = $arPrice;
                        $this->arResult['ITEMS'][$arProducts[$arPrice['PRODUCT_ID']]]['CURRENT_PRICES'][$arPrice['PRODUCT_ID']] = floatval($arPrice['PRICE']);
                    }
                }

                // ЗАПИСЫВАЕМ ОСТАТКИ
                $rsElementStores = \Bitrix\Catalog\StoreProductTable::GetList(
                    array(
                        'runtime' => array(
                            'PRICE' => array(
                                'data_type' => '\Bitrix\Catalog\PriceTable',
                                'reference' => array(
                                    '=ref.PRODUCT_ID' => 'this.PRODUCT_ID',
                                )
                            )
                        ),
                        'order' => array(
                            'PRICE.PRICE' => 'ASC',
                            'PRODUCT_ID' => "ASC"
                        ),
                        'filter' => array(
                            '=PRODUCT_ID' => $this->arResult['NID_OFFERS'],
                            '=PRICE.CATALOG_GROUP_ID' => $this->arParams['CATALOG_GROUPS'],
                            '>AMOUNT' => 0
                        ),
                        'select' => array(
                            'ID',
                            'PRODUCT_ID',
                            'STORE_ID',
                            'AMOUNT'
                        )
                    )
                );

                while($arElementStore = $rsElementStores->fetch())
                {
                    // ТОРГОВОЕ ПРЕДЛОЖЕНИЕ НЕ АКТИВНО
                    if(empty($this->arResult['ITEMS'][$arProducts[$arElementStore['PRODUCT_ID']]]['OFFERS'][$arElementStore['PRODUCT_ID']]))
                    {
                        continue;
                    }

                    $this->arResult['ITEMS'][$arProducts[$arElementStore['PRODUCT_ID']]]['OFFERS'][$arElementStore['PRODUCT_ID']]['STORES'][$arElementStore['STORE_ID']][$arElementStore['ID']] = array(
                        'ID' => intval($arElementStore['ID']),
                        'STORE_ID' => intval($arElementStore['STORE_ID']),
                        'AMOUNT' => intval($arElementStore['AMOUNT'])
                    );

                    // ОСТАТКИ ТОРГОВЫЙ ПРЕДЛОЖЕНИЙ
                    $this->arResult['ITEMS'][$arProducts[$arElementStore['PRODUCT_ID']]]['OFFERS'][$arElementStore['PRODUCT_ID']]['AMOUNTS'][$arElementStore['STORE_ID']] += $arElementStore['AMOUNT'];
                    $this->arResult['ITEMS'][$arProducts[$arElementStore['PRODUCT_ID']]]['OFFERS'][$arElementStore['PRODUCT_ID']]['TOTAL'] += $arElementStore['AMOUNT'];

                    $this->arResult['ITEMS'][$arProducts[$arElementStore['PRODUCT_ID']]]['CURRENT_STORES'][$arElementStore['STORE_ID']][$arElementStore['PRODUCT_ID']] += $arElementStore['AMOUNT'];

                    // ОБЩИЕ ОСТАТКИ ТОВАРА
                    $this->arResult['ITEMS'][$arProducts[$arElementStore['PRODUCT_ID']]]['AMOUNTS'][$arElementStore['STORE_ID']] += $arElementStore['AMOUNT'];
                    $this->arResult['ITEMS'][$arProducts[$arElementStore['PRODUCT_ID']]]['TOTAL'] += $arElementStore['AMOUNT'];
                }
            }
        }

        // ПОЛУЧАЕМ ВСЕ ОПИСАНИЯ ЗНАЧЕНИЙ СВОЙСТВ ТОВАРОВ И ТОРГОВЫХ ПРЕДЛОЖЕНИЙ
        if (!empty($this->arResult['PROPERTIES']))
        {
            foreach ($this->arResult['PROPERTIES'] as $check => &$arProps)
            {
                switch($check)
                {
                    case 'ITEM':
                    case 'OFFER':

                        foreach($arProps as $NID => &$arProp)
                        {
                            if(empty($arProp['VALUES']) || !is_array($arProp['VALUES']))
                            {
                                continue;
                            }

                            $arProp = $this->setValueProperties($arProp);
                        }

                        break;
                }
            }
        }

        // ПОСТРАНИЧКА
        if ($this->arParams['SHOW_NAV'] == 'Y' && $this->arParams['COUNT'] > 0 && $this->arResult['TOTAL_ELEMENTS'] > 0)
        {
            ob_start();

            \Sepro\App::getInstance()->IncludeComponent(
                "bitrix:main.pagenavigation",
                "modern",
                array(
                    "NAV_OBJECT" => $this->nav,
                    "SEF_MODE" => "N",
                    "SHOW_COUNT" => "N",
                    "SHOW_ALL" => "N"
                ),
                false
            );

            $this->arResult['NAV_STRING'] = @ob_get_clean();
            $this->arResult['NAV_OBJECT'] = $this->nav;
        }
    }

    public function setValueProperties($arProp)
    {
        switch($arProp['PROPERTY_TYPE'])
        {
            case 'L':

                $rsElementEnumProperties = \Bitrix\Iblock\PropertyEnumerationTable::getList(
                    array(
                        'order' => array('VALUE' => 'ASC'),
                        'filter' => array(
                            '=ID' => array_unique($arProp['VALUES']),
                            '=PROPERTY_ID' => $arProp['ID']
                        )
                    )
                );

                while($arElementEnumProperty = $rsElementEnumProperties->fetch())
                {
                    $arProp['DISPLAY_VALUES'][$arElementEnumProperty['ID']] = $arElementEnumProperty['VALUE'];
                    $arProp['DISPLAY_VALUES_XML_ID'][$arElementEnumProperty['ID']] = $arElementEnumProperty['XML_ID'];
                }

                break;

            case 'S':

                switch($arProp['USER_TYPE'])
                {
                    case 'HTML':

                        foreach($arProp['VALUES'] as $NID => $value)
                        {
                            $arProp['DISPLAY_VALUES'][$NID] = unserialize($value);
                        }

                        break;

                    case 'directory':

                        if(!empty($arProp['HL_BLOCK']))
                        {
                            $HLDataClass = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arProp['HL_BLOCK'])->getDataClass();

                            if (class_exists($HLDataClass))
                            {
                                $rsHLValues = $HLDataClass::GetList(array(
                                    'order' => array('ID' => 'ASC'),
                                    'filter' => array(
                                        '=UF_XML_ID' => array_unique($arProp['VALUES'])
                                    ),
                                    'select' => array('*')
                                ));

                                while ($arHLValue = $rsHLValues->fetch())
                                {
                                    $arProp['DISPLAY_VALUES_ROW_ID'][$arHLValue['UF_XML_ID']] = $arHLValue['ID'];
                                    $arProp['DISPLAY_VALUES_XML_ID'][$arHLValue['UF_XML_ID']] = $arHLValue['UF_XML_ID'];
                                    $arProp['DISPLAY_VALUES'][$arHLValue['UF_XML_ID']] = $arHLValue;
                                }
                            }
                        }

                        break;

                    default:

                        $ORMEPTable = \Sepro\ORMFactory::compile('b_iblock_element_property');
                        $rsEPTable = $ORMEPTable::GetList(array(
                            'filter' => array(
                                '=ID' => array_keys($arProp['VALUES'])
                            ),
                            'select' => array(
                                'ID',
                                'IBLOCK_ELEMENT_ID',
                                'VALUE',
                                'DESCRIPTION'
                            )
                        ));

                        while($arEPTable = $rsEPTable->fetch())
                        {
                            $arProp['DISPLAY_VALUES'][$arEPTable['IBLOCK_ELEMENT_ID']][$arEPTable['ID']] = $arEPTable;

                            if($arProp['CODE'] == 'CML2_ATTRIBUTES' && !empty($arEPTable['DESCRIPTION']))
                            {
                                $arProp['ATTRIBUTES_VALUES'][$arEPTable['IBLOCK_ELEMENT_ID']][$arEPTable['DESCRIPTION']] = $arEPTable;
                            }
                        }

                        break;
                }

                break;

            case 'F':

                foreach($arProp['VALUES'] as $NID => $value)
                {
                    $NIDFile = intval($value);
                    $path = CFile::GetPath($NIDFile);

                    if(!is_file(DOCUMENT_ROOT.$path))
                        continue;

                    $arProp['DISPLAY_VALUES'][$NIDFile]['PROPERTY_VALUE_ID'] = $NID;
                    $arProp['DISPLAY_VALUES'][$NIDFile]['PATH'] = $path;
                }

                break;

            case 'E':

                $rsElements = \Sepro\ElementTable::GetList(
                    array(
                        'order' => array('ID' => 'ASC'),
                        'filter' => array('=ID' => $arProp['VALUES']),
                        'select' => array(
                            'ID',
                            'IBLOCK_ID',
                            'NAME',
                            'CODE',
                            'DETAIL_TEXT',
                            'PREVIEW_TEXT',
                            'IBLOCK_SECTION_ID',
                            'PREVIEW_PICTURE',
                            'DETAIL_PICTURE',
                        )
                    )
                );

                while($arElement = $rsElements->fetch())
                {
                    $arProp['DISPLAY_VALUES'][$arElement['ID']] = $arElement;
                }

                break;
        }

        return $arProp;
    }

    // ИНИЦИАЛИЗАЦИЯ КОМПОНЕНТЫ
    public function executeComponent()
    {
        try
        {
            $this->checkAutomaticParams();
            $this->executeProlog();

            if ($this->readDataFromCache())
            {
                $this->getResult();
                $this->putDataToCache();
                $this->includeComponentTemplate();
            }

            if($this->_return)
            {
                // in template set $this->__component->_return = $variable; and return false;
                return $this->_return;
            }
        }
        catch (\Exception $exception)
        {
            $this->AbortResultCache();
            \Sepro\Log::add2log($exception->getMessage());
            echo $exception->getMessage();
        }

        return true;
    }
}
