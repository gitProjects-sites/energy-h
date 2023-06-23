<?
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
$SERVER_NAME = $_SERVER['SERVER_NAME'];
$RESULT_LANG = 'ru';

if(empty($DOCUMENT_ROOT))
{
    $DOCUMENT_ROOT = realpath(dirname(__FILE__).'/../../..');
    $_SERVER['DOCUMENT_ROOT'] = $DOCUMENT_ROOT;
}

if(empty($SERVER_NAME))
{
    $SERVER_NAME = \Bitrix\Main\Config\Option::get('main', 'server_name', gethostname(), 's1');
}

define('DOCUMENT_ROOT', $DOCUMENT_ROOT);
define('LOG_FILENAME', DOCUMENT_ROOT.'/upload/logs/log_'.date('d_m__H-i').'.txt');
define("SYSTEM_LOG", LOG_FILENAME);
define('SITE_NAME', $SERVER_NAME);
define('LANGUAGE_ID', $RESULT_LANG);
define('EMAIL_ADMIN', 'kollaps6@yandex.ru');

\Bitrix\Main\Loader::registerAutoLoadClasses(
    'sepro.helper',
    array(
        '\Sepro\App' => 'lib/app.php',
        '\Sepro\ECommerce' => 'lib/commerce.php',
        '\Sepro\Payment' => 'lib/payment.php',
        '\Sepro\User' => 'lib/user.php',
        '\Sepro\IBlock' => 'lib/iblock.php',
        '\Sepro\Basket' => 'lib/basket.php',
        '\Sepro\Log' => 'lib/log.php',
        '\Sepro\Events' => 'lib/events.php',
        '\Sepro\Helpers' => 'lib/helpers.php',
        '\Sepro\Form' => 'lib/form.php',

        '\Sepro\ORMFactory' => 'lib/orm.php',
        '\Sepro\ElementTable' => 'lib/element.php',

        'CModuleOptions' => 'classes/options.php'
    )
);

?>