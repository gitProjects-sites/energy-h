<?
namespace Sepro;

class Payment
{
    private static $order;
    private static $paymentCollection;
    private static $payment;
    private static $paymentService;

    public static function init($ORDER_ID, $ACCOUNT_ID)
    {
        $order = \Bitrix\Sale\Order::load($ORDER_ID);
        $paymentCollection = $order->getPaymentCollection();

        // LAST PAYMENT OPERATION BY ACCOUNT_ID
        $payment = $paymentCollection->getItemById($ACCOUNT_ID);

        // PAYMENT_SERVICE
        $paymentService = \Bitrix\Sale\PaySystem\Manager::getObjectById($payment->getPaymentSystemId());

        static::$order = $order;
        static::$paymentCollection = $paymentCollection;
        static::$payment = $payment;
        static::$paymentService = $paymentService;

        return new Payment();
    }

    public static function getField($field)
    {
        $value = self::$paymentService->getField($field);

        if($value)
        {
            return $value;
        }

        return false;
    }

    public function getHtml()
    {
        // exmaple in order_confirm: 
        // \Sepro\Payment::init($arResult['ORDER']['ID'], $arResult['PAYMENT'][$arResult['ORDER']['PAYMENT_ID']]['ACCOUNT_NUMBER'])->getHtml();

        $data = '';
        $arPaySysAction = self::getPaymentSystemAction();

        //\Sepro\Log::add2log(static::$order->getFields());

        if(!empty($arPaySysAction) && is_array($arPaySysAction))
        {
            switch($arPaySysAction['ACTION_FILE'])
            {
                case 'yandex':

                    $YANDEX_SHOP_ID = \Bitrix\Sale\BusinessValue::getValueFromProvider(
                        static::$payment,
                        'YANDEX_SHOP_ID',
                        static::$paymentService->getConsumerName()
                    );

                    $YANDEX_SCID = \Bitrix\Sale\BusinessValue::getValueFromProvider(
                        static::$payment,
                        'YANDEX_SCID',
                        static::$paymentService->getConsumerName()
                    );

                    $data .= \Sepro\Form::start(
                        'https://money.yandex.ru/eshop.xml',
                        'post',
                        array(
                            'name' => 'ShopForm',
                            'target' => '_blank',
                            'class' => 'personal-order-payment-action'
                        )
                    );

                    $data .= \Sepro\Form::input('hidden', 'ShopID', $YANDEX_SHOP_ID);
                    $data .= \Sepro\Form::input('hidden', 'scid', $YANDEX_SCID);
                    $data .= \Sepro\Form::input('hidden', 'customerNumber', static::$order->getField('USER_ID'));
                    $data .= \Sepro\Form::input('hidden', 'orderNumber', static::$payment->getField('ORDER_ID'));
                    $data .= \Sepro\Form::input('hidden', 'Sum', static::$order->getField('PRICE'));
                    $data .= \Sepro\Form::input('hidden', 'paymentType', '');
                    $data .= \Sepro\Form::input('hidden', 'cms_name', '1C-Bitrix');
                    $data .= \Sepro\Form::input('hidden', 'BX_HANDLER', 'YANDEX');
                    $data .= \Sepro\Form::input('hidden', 'BX_PAYSYSTEM_CODE', static::$payment->getPaymentSystemId());
                    $data .= \Sepro\Form::input('submit', 'BuyButton', 'Оплатить', false, array('class' => 'data-btn danger'));
                    $data .= \Sepro\Form::end();

                    break;

            }
        }

        return $data;
    }

    protected static function getPaymentSystemAction()
    {
        $arPaySysAction = static::$paymentService->getFieldsValues();

        $map = \CSalePaySystemAction::getOldToNewHandlersMap();
        $map = array_flip($map);

        if(strlen($arPaySysAction["ACTION_FILE"]) > 0 && $arPaySysAction["NEW_WINDOW"] != "Y")
        {
            $oldHandler = $map[$arPaySysAction['ACTION_FILE']];
            if ($oldHandler !== false && !static::$paymentService->isCustom())
            {
                $arPaySysAction["PATH_TO_ACTION"] = $oldHandler;
            }

            $pathToAction = $_SERVER['DOCUMENT_ROOT'].$arPaySysAction["PATH_TO_ACTION"];

            $pathToAction = str_replace("\\", "/", $pathToAction);

            while (substr($pathToAction, strlen($pathToAction) - 1, 1) == "/")
                $pathToAction = substr($pathToAction, 0, strlen($pathToAction) - 1);

            if (file_exists($pathToAction))
            {
                if (is_dir($pathToAction) && file_exists($pathToAction."/payment.php"))
                    $pathToAction .= "/payment.php";

                $arPaySysAction["PATH_TO_ACTION"] = $pathToAction;
            }

            return $arPaySysAction;
        }

        return false;
    }
}