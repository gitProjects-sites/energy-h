<?
namespace Sepro;

use \Bitrix\Main\Application;

class IBlock
{
    static $catalog = array(); // ID каталога
    static $sku = array(); // ID торговых предложений

    static $iblocks;
    static $iblocksByType;
    static $categories;

    public static function getIBCatalog($iblock_id = false)
    {
        if(!class_exists('\Bitrix\Catalog\CatalogIblockTable')) return false;

        if(empty(static::$catalog))
        {
            if(intval($iblock_id) > 0)
            {
                static::$catalog = self::getIBlock($iblock_id);
            }

            if(empty(static::$catalog))
            {
                $rsCatalog = \Bitrix\Catalog\CatalogIblockTable::GetList(array(
                    'select' => array('IBLOCK_ID', 'PRODUCT_IBLOCK_ID')
                ));

                while($arCatalog = $rsCatalog->fetch())
                {
                    if(!empty($arCatalog['PRODUCT_IBLOCK_ID']))
                    {
                        static::$catalog = self::getIBlock($arCatalog['PRODUCT_IBLOCK_ID']);

                        break;
                    }

                    static::$catalog = self::getIBlock($arCatalog['IBLOCK_ID']);

                    break;
                }
            }
        }

        return !empty(static::$catalog) ? static::$catalog : false;
    }

    public static function getIBOffers($iblock_id = false)
    {
        if(empty(static::$sku))
        {
            if(intval($iblock_id) > 0)
            {
                static::$sku = self::getIBlock($iblock_id);
            }

            if(empty(static::$sku))
            {
                self::getIBCatalog();

                if(intval(static::$catalog['SKU_IBLOCK_ID']) > 0)
                {
                    static::$sku = self::getIBlock(static::$catalog['SKU_IBLOCK_ID']);
                }
            }
        }

        return !empty(static::$sku) ? static::$sku : false;
    }

    public static function getIBlocksByType($iblock_type = false)
    {
        if(!$iblock_type) return false;

        if(empty(static::$iblocksByType[$iblock_type]))
        {
            self::getIBlocks();
        }

        return !empty(static::$iblocksByType[$iblock_type]) ? static::$iblocksByType[$iblock_type] : false;
    }

    public static function getIBlock($id = false)
    {
        if(!$id) return false;

        if(empty(static::$iblocks[$id]))
        {
            self::getIBlocks();
        }

        return !empty(static::$iblocks[$id]) ? static::$iblocks[$id] : false;
    }

    public static function getIblockSections($IBLOCK_ID = 0, $arFilter = array(), $UFExist = false, $clearCache = false)
    {
        if(intval($IBLOCK_ID) <= 0) return false;

        if(empty(static::$categories[$IBLOCK_ID]))
        {
            self::getSections($arFilter, $clearCache);

            if($UFExist && empty(static::$categories[$IBLOCK_ID]['UF_FIELDS']))
            {
                $ORMUFSections = \Sepro\ORMFactory::compile('b_uts_iblock_'.$IBLOCK_ID.'_section');
                $rsUFSections = $ORMUFSections::GetList(array(
                    'select' => array('*')
                ));

                while($arUFSection = $rsUFSections->fetch())
                {
                    $SECTION_ID = array_shift($arUFSection);

                    foreach(array_keys($arUFSection) as $UFCode)
                    {
                        if(empty($arUFSection[$UFCode])) continue;

                        static::$categories[$IBLOCK_ID]['UF_FIELDS'][$UFCode][$SECTION_ID] = $arUFSection[$UFCode];
                    }
                }
            }
        }

        return !empty(static::$categories[$IBLOCK_ID]) ? static::$categories[$IBLOCK_ID] : false;
    }

    public static function getIBlocks()
    {
        if(empty(static::$iblocks))
        {
            $rsIblocks = \Bitrix\Iblock\IblockTable::GetList(
                array(
                    'order' => array('ID' => 'ASC'),
                    'select' => array('*')
                )
            );

            while($arIblock = $rsIblocks->fetch())
            {
                static::$iblocks[$arIblock['ID']] = $arIblock;
                static::$iblocksByType[$arIblock['IBLOCK_TYPE_ID']][$arIblock['ID']] = $arIblock;
            }

            $rsProperties = \Bitrix\Iblock\PropertyTable::GetList(array(
                'filter' => array(
                    '=IBLOCK_ID' => array_keys(static::$iblocks)
                ),
                'select' => array(
                    'ID',
                    'IBLOCK_ID',
                    'NAME',
                    'CODE',
                    'LINK_IBLOCK_ID',
                    'PROPERTY_TYPE'
                )
            ));

            while($arProperty = $rsProperties->fetch())
            {
                $arIblock = static::$iblocks[$arProperty['IBLOCK_ID']];

                if(empty($arIblock)) continue;

                $arIblock['PROPERTIES'][$arProperty['CODE']] = $arProperty;

                if(in_array($arProperty['CODE'], array('CML2_LINK')))
                {
                    // Записываем в инфоблок предложений связку с каталогом
                    $arIblock = array_merge($arIblock, array(
                        'IS_SKU' => true,
                        'CATALOG_IBLOCK_ID' => $arProperty['LINK_IBLOCK_ID']
                    ));

                    // Записываем в кататог связку с инфоблоком предложений
                    static::$iblocks[$arProperty['LINK_IBLOCK_ID']] = array_merge(static::$iblocks[$arProperty['LINK_IBLOCK_ID']], array(
                        'SKU_IBLOCK_ID' => $arProperty['IBLOCK_ID'],
                        'SKU_PROPERTY_ID' => $arProperty['ID']
                    ));
                }

                static::$iblocks[$arProperty['IBLOCK_ID']] = $arIblock;
                static::$iblocksByType[$arIblock['IBLOCK_TYPE_ID']][$arProperty['IBLOCK_ID']] = $arIblock;
            }
        }

        return static::$iblocks;
    }

    private static function setSectionRecursive($array)
    {
        if(!is_array($array) || empty($array))
        {
            return false;
        }

        $arSections = array_reverse($array, true);

        foreach(array_keys($arSections) as $NID)
        {
            $curSection = $arSections[$NID];

            $arSections[$NID]['SECTION_PATH'] = $array[$NID]['SECTION_PATH'] =  '/'.$curSection['CODE'];
            $arSections[$NID]['SUBKEYS'] = $curSection['ID'];
            $arSections[$NID]['AR_SUBKEYS'] = array($curSection['ID']);

            if(intval($curSection['IBLOCK_SECTION_ID']) > 0)
            {
                $arSections[$NID]['SECTION_PATH'] = $array[$NID]['SECTION_PATH'] = $arSections[$curSection['IBLOCK_SECTION_ID']]['SECTION_PATH'].'/'.$curSection['CODE'];
                $arSections[$NID]['SUBKEYS'] = $arSections[$curSection['IBLOCK_SECTION_ID']]['SUBKEYS'].';'.$curSection['ID'];
                if(is_array($arSections[$curSection['IBLOCK_SECTION_ID']]['AR_SUBKEYS']))
                {
                    $arSections[$NID]['AR_SUBKEYS'] = array_merge($arSections[$curSection['IBLOCK_SECTION_ID']]['AR_SUBKEYS'], array($curSection['ID']));
                }
                else
                {
                    $arSections[$NID]['AR_SUBKEYS'] = [];
                }

            }
        }

        foreach(array_keys($array) as $NID)
        {
            $curSection = $arSections[$NID];

            if(intval($curSection['IBLOCK_SECTION_ID']) > 0)
            {
                $array[$curSection['IBLOCK_SECTION_ID']]['CHILD'][$NID] = $array[$NID];

                if(!empty($curSection['CHILDRENS']))
                {
                    $arSections[$curSection['IBLOCK_SECTION_ID']]['CHILDRENS'] .= $curSection['CHILDRENS'];
                }

                if(!empty($curSection['ELEMENTS']))
                {
                    if(!empty($arSections[$curSection['IBLOCK_SECTION_ID']]['ELEMENTS']))
                    {
                        $curSection['ELEMENTS'] = array_merge($curSection['ELEMENTS'], $arSections[$curSection['IBLOCK_SECTION_ID']]['ELEMENTS']);
                    }

                    $arSections[$curSection['IBLOCK_SECTION_ID']]['ELEMENTS'] = $curSection['ELEMENTS'];
                }

                $arSections[$curSection['IBLOCK_SECTION_ID']]['CHILDRENS'] .= $NID.';';

                unset($array[$NID]);
            }
            else
            {
                $arSections[$NID]['CHILDRENS'] .= $NID;
            }
        }

        return array(
            'TREE' => array_reverse($array, true),
            'SECTIONS' => $arSections
        );
    }

    public static function getSections($arFilter = array(), $clearCache = false)
    {
        if(empty(static::$categories))
        {
            $cache = Application::getInstance()->getManagedCache();
            $arIBSections = array();
            $arSElements = array();

            if($clearCache)
            {
                $cache->clean("IBLOCK_SECTION_ELEMENTS");
            }

            if($cache->read(86400, "IBLOCK_SECTION_ELEMENTS"))
            {
                $arIBSections = $cache->get("IBLOCK_SECTION_ELEMENTS");
            }
            else
            {
                if(empty($arFilter))
                {
                    $arFilter = array(
                        'ACTIVE' => 'Y'
                    );
                }

                $rsSElements = \Sepro\ElementTable::GetList(array(
                    'filter' => $arFilter,
                    'select' => array(
                        'ID',
                        'IBLOCK_SECTION_ID'
                    )
                ));

                while($arSElement = $rsSElements->fetch())
                {
                    $arSElements[$arSElement['IBLOCK_SECTION_ID']][$arSElement['ID']] = $arSElement['ID'];
                }

                $rsSections = \Bitrix\Iblock\SectionTable::GetList(
                    array(
                        'order' => array(
                            //'LEFT_MARGIN' => 'DESC',
                            'SORT' => 'ASC',
                            'ID' => 'DESC'
                        ),
                        'filter' => array(
                            'ACTIVE' => 'Y'
                        ),
                        'select' => array(
                            'ID',
                            'IBLOCK_ID',
                            'NAME',
                            'CODE',
                            'PICTURE',
                            'DEPTH_LEVEL',
                            'IBLOCK_SECTION_ID',
                            'DESCRIPTION'
                        )
                    )
                );

                while($arSection = $rsSections->fetch())
                {
                    if(!empty($arSElements[$arSection['ID']]))
                    {
                        $arSection['ELEMENTS'] = $arSElements[$arSection['ID']];
                    }

                    $arIBSections[$arSection['IBLOCK_ID']][$arSection['ID']] = $arSection;
                }

                foreach($arIBSections as &$arSections)
                {
                    $arSections = self::setSectionRecursive($arSections);
                }

                $cache->set("IBLOCK_SECTION_ELEMENTS", $arIBSections);
            }

            static::$categories = $arIBSections;
        }

        return static::$categories;
    }
}