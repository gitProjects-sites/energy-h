<?
namespace Sepro;

use \Bitrix\Main\DB;

class ElementTable extends \Bitrix\Iblock\ElementTable
{
    public static function getMap()
    {
        $arMap = parent::getMap();
        $ORMProperties = \Sepro\ORMFactory::compile('b_iblock_element_property');

        if(class_exists('\Bitrix\Catalog\ProductTable'))
        {
            $arMap['CATALOG'] = array(
                'data_type' => '\Bitrix\Catalog\ProductTable',
                'reference' => array(
                    '=ref.ID' => 'this.ID',
                )
            );
        }

        if(class_exists('\Bitrix\Catalog\PriceTable'))
        {
            $arMap['PRICE'] = array(
                'data_type' => '\Bitrix\Catalog\PriceTable',
                'reference' => array(
                    '=ref.PRODUCT_ID' => 'this.ID',
                )
            );
        }

        foreach(\Sepro\IBlock::getIBlocks() as $IBLOCK)
        {
            if($IBLOCK['SKU_PROPERTY_ID'] > 0)
            {
                $arMap['CML2_LINK_'.$IBLOCK['ID']] = array(
                    'data_type' => $ORMProperties::getClassName(),
                    'reference' => array(
                        '=ref.VALUE' => 'this.ID',
                        '=ref.IBLOCK_PROPERTY_ID' => new DB\SqlExpression('?i', $IBLOCK['SKU_PROPERTY_ID'])
                    )
                );
            }

            foreach($IBLOCK['PROPERTIES'] as $CODE => $arProperty)
            {
                $arMap['IBLOCK_'.$IBLOCK['ID'].'_PROPERTY_'.$CODE] = array(
                    'data_type' => $ORMProperties::getClassName(),
                    'reference' => array(
                        '=ref.IBLOCK_ELEMENT_ID' => 'this.ID',
                        '=ref.IBLOCK_PROPERTY_ID' => new DB\SqlExpression('?i', $arProperty['ID'])
                    )
                );
            }
        }

        return $arMap;
    }
}