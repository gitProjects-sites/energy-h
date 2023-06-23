<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc as Loc;

Loc::setCurrentLang(LANGUAGE_ID);
Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => Loc::getMessage('BIGDATA_ELEMENTS_DESCRIPTION_NAME'),
	"DESCRIPTION" => Loc::getMessage('BIGDATA_ELEMENTS_DESCRIPTION_DESCRIPTION'),
	"ICON" => '/images/icon.gif',
	"SORT" => 20,
	"PATH" => array(
		"ID" => 'sepro',
		"NAME" => Loc::getMessage('BIGDATA_ELEMENTS_DESCRIPTION_GROUP'),
		"SORT" => 10
	),
);

?>