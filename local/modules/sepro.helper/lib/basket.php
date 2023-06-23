<?
namespace Sepro;

class Basket
{
    private static $basket = false;

    public static function getBasket($bChange = false)
    {
        if(!static::$basket || $bChange)
        {
            $basket = array();

            $rsBasket = \Bitrix\Sale\Internals\BasketTable::GetList(array(
                'filter' => array(
                    '=FUSER_ID' => \CSaleBasket::GetBasketUserID(),
                    '=ORDER_ID' => NULL,
                    '=CAN_BUY' => 'Y',
                    '=DELAY' => 'N'
                ),
                'select' => array(
                    'ID',
                    'PRODUCT_ID',
                    'QUANTITY'
                )
            ));

            while($arProduct = $rsBasket->fetch())
            {
                $basket[$arProduct['PRODUCT_ID']] = $arProduct;
            }

            static::$basket = $basket;
        }

        return static::$basket;
    }
}