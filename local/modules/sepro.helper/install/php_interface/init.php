<?use \Bitrix\Main\Loader;

Loader::includeModule('sepro.helper');
Loader::includeModule("iblock");
Loader::includeModule("highloadblock");
Loader::includeModule("catalog");
Loader::includeModule("sale");

CJSCore::Init(array('window'));