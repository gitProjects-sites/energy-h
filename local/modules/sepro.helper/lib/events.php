<?
namespace Sepro;

use \Bitrix\Main\Config\Option;

class Events
{
    private static $base_cg_id = false; // Базовый тип цены

    const TYPE_PRODUCT = 1;
    const TYPE_SET = 2;
    const TYPE_SKU = 3;
    const TYPE_OFFER = 4;
    const TYPE_FREE_OFFER = 5;
    const TYPE_EMPTY_SKU = 6;

    function _onProlog()
    {
        // PUSH AND PULL
        $USER_ID = $GLOBALS['USER']->GetID();
        define('PULL_USER_ID', $USER_ID > 0 ? $USER_ID : -1); // TEST
    }

    function _Check404Error()
    {
        if(defined('ERROR_404') || strpos(\CHTTP::GetLastStatus(), '404') !== false)
        {
            global $APPLICATION;
            $APPLICATION->RestartBuffer();

            $GLOBALS['_SERVER']["REAL_FILE_PATH"] = "/404.php";

            require($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/header.php');
            require($_SERVER['DOCUMENT_ROOT'].'/404.php');
            require($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/footer.php');
        }
    }

    public function OnBeforePrologHandler()
    {
        // CRONTAB EXIST $_SERVER is empty
        if(!\Sepro\User::getUserIP())
            return true;

        // TODO Установка города

        return true;
    }

    //после добавления элемента
    public function OnAfterIBlockElementAddHandler(&$arFields)
    {
        $arFields['PRODUCT_ID'] = $arFields['ID'];
        self::updateDependence($arFields, 'ADD');
    }

    //после изменения элемента
    public function OnAfterIBlockElementUpdateHandler(&$arFields)
    {
        $arFields['PRODUCT_ID'] = $arFields['ID'];
        self::updateDependence($arFields, 'UPDATE');
    }

    //после удаления элемента
    public function OnBeforeIBlockElementDeleteHandler($arFields)
    {
        self::updateDependence($arFields, 'DELETE');
    }

    public static function updateDependence($arFields, $action)
    {
        $arIBCatalog = \Sepro\IBlock::getIBCatalog();

        if(!in_array($arFields['IBLOCK_ID'], array($arIBCatalog['ID'], $arIBCatalog['SKU_IBLOCK_ID'])))
        {
            return true;
        }

        if(intval(static::$base_cg_id) <= 0)
        {
            $arGTable = \Bitrix\Catalog\GroupTable::getList(array(
                'filter' => array('=BASE' => 'Y'),
                'select' => array('ID')
            ))->fetch();

            static::$base_cg_id = intval($arGTable['ID']) > 0 ? $arGTable['ID'] : 1;
        }

        $ORMEPTable = \Sepro\ORMFactory::compile('b_iblock_element_property');
        $arEPrices = array();
        $arOLDPrices = array();
        $EID = $arFields['ID'];

        $arCElement = \Bitrix\Catalog\ProductTable::GetList(array(
            'filter' => array(
                'ID' => $EID,
                'TYPE' => array(
                    self::TYPE_PRODUCT,
                    self::TYPE_SET,
                    self::TYPE_SKU,
                    self::TYPE_OFFER
                )
            ),
            'select' => array(
                'TYPE'
            )
        ))->fetch();

        if(empty($arCElement) || ($action == 'DELETE' && in_array($arCElement['TYPE'], array(self::TYPE_PRODUCT, self::TYPE_SET, self::TYPE_SKU))))
        {
            return true;
        }

        // Собираем цены
        switch($arCElement['TYPE'])
        {
            // Простой товар или комплект
            case self::TYPE_PRODUCT:
            case self::TYPE_SET:

                $arEPrice = \Bitrix\Catalog\PriceTable::GetList(array(
                    'filter' => array(
                        '=PRODUCT_ID' => $EID,
                        'CATALOG_GROUP_ID' => self::$base_cg_id
                    ),
                    'select' => array(
                        'PRODUCT_ID',
                        'PRICE'
                    )
                ))->fetch();

                if(!empty($arEPrice))
                {
                    $arEPrices[$arEPrice['PRODUCT_ID']] = $arEPrice['PRICE'];
                }

                break;

            // Торговое предложение
            case self::TYPE_OFFER:

                $arEPTable = $ORMEPTable::GetList(array(
                    'filter' => array(
                        '=IBLOCK_ELEMENT_ID' => $EID,
                        '=IBLOCK_PROPERTY_ID' => $arIBCatalog['SKU_PROPERTY_ID']
                    ),
                    'select' => array('VALUE')
                ))->fetch();

                if(!empty($arEPTable['VALUE']))
                {
                    $EID = $arEPTable['VALUE'];
                }

                break;
        }

        if(empty($arEPrices))
        {
            $rsEPTable = $ORMEPTable::GetList(array(
                'runtime' => array(
                    'PRICE' => array(
                        'data_type' => '\Bitrix\Catalog\PriceTable',
                        'reference' => array(
                            '=ref.PRODUCT_ID' => 'this.IBLOCK_ELEMENT_ID',
                            '=ref.CATALOG_GROUP_ID' => new \Bitrix\Main\DB\SqlExpression('?i', self::$base_cg_id),
                        )
                    )
                ),
                'filter' => array(
                    '=VALUE' => $EID,
                    '=IBLOCK_PROPERTY_ID' => $arIBCatalog['SKU_PROPERTY_ID'],
                    '!PRICE.PRICE' => false
                ),
                'select' => array(
                    'IBLOCK_ELEMENT_ID',
                    'PRICE_VALUE' => 'PRICE.PRICE'
                )
            ));

            while($arEPTable = $rsEPTable->fetch())
            {

                $arEPrices[$arEPTable['IBLOCK_ELEMENT_ID']] = $arEPTable['PRICE_VALUE'];
            }
        }

        if(!empty($arEPrices))
        {
            /////////////////////////
            // GET CUR DATA MIN AND MAX
            /////////////////////////

            $rsEPTable = $ORMEPTable::GetList(array(
                'filter' => array(
                    '=IBLOCK_ELEMENT_ID' => $EID,
                    '=IBLOCK_PROPERTY_ID' => array(
                        $arIBCatalog['PROPERTIES']['MINIMUM_PRICE']['ID'],
                        $arIBCatalog['PROPERTIES']['MAXIMUM_PRICE']['ID']
                    )
                ),
                'select' => array(
                    'ID',
                    'IBLOCK_PROPERTY_ID'
                )
            ));

            while($arEPTable = $rsEPTable->fetch())
            {
                $arOLDPrices[$arEPTable['IBLOCK_PROPERTY_ID']] = $arEPTable['ID'];
            }

            /////////////////////////
            // ADD OR UPDATE MIN PRICE
            /////////////////////////

            if(!empty($arOLDPrices[$arIBCatalog['PROPERTIES']['MINIMUM_PRICE']['ID']]))
            {
                $obResult = $ORMEPTable::update($arOLDPrices[$arIBCatalog['PROPERTIES']['MINIMUM_PRICE']['ID']], array(
                    'VALUE' => min($arEPrices)
                ));
            }
            else
            {
                $obResult = $ORMEPTable::add(array(
                    'IBLOCK_PROPERTY_ID' => $arIBCatalog['PROPERTIES']['MINIMUM_PRICE']['ID'],
                    'IBLOCK_ELEMENT_ID' => $EID,
                    'VALUE' => min($arEPrices),
                    'VALUE_NUM' => floatval(min($arEPrices)),
                    'VALUE_TYPE' => 'text'
                ));
            }

            if(!$obResult->isSuccess())
            {
                \Sepro\Log::add2log(array_merge(
                    array(
                        'Ошибка записи минимальной стоимости товара. PRODUCT_ID: '.$EID.'; VALUE:'.min($arEPrices)
                    ),
                    $obResult->getErrorMessages()
                ));
            }

            /////////////////////////
            // ADD OR UPDATE MAX PRICE
            /////////////////////////

            if(!empty($arOLDPrices[$arIBCatalog['PROPERTIES']['MAXIMUM_PRICE']['ID']]))
            {
                $obResult = $ORMEPTable::update($arOLDPrices[$arIBCatalog['PROPERTIES']['MAXIMUM_PRICE']['ID']], array(
                    'VALUE' => max($arEPrices)
                ));
            }
            else
            {
                $obResult = $ORMEPTable::add(array(
                    'IBLOCK_PROPERTY_ID' => $arIBCatalog['PROPERTIES']['MAXIMUM_PRICE']['ID'],
                    'IBLOCK_ELEMENT_ID' => $EID,
                    'VALUE' => max($arEPrices),
                    'VALUE_NUM' => floatval(max($arEPrices)),
                    'VALUE_TYPE' => 'text'
                ));
            }

            if(!$obResult->isSuccess())
            {
                \Sepro\Log::add2log(array_merge(
                    array(
                        'Ошибка записи максимальной стоимости товара. PRODUCT_ID: '.$EID.'; VALUE:'.max($arEPrices)
                    ),
                    $obResult->getErrorMessages()
                ));
            }
        }
    }
}

