<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Правила использования cookie-файлов");
$APPLICATION->SetTitle("Правила использования cookie-файлов");
?><?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "page",
		"EDIT_TEMPLATE" => ""
	),
	false,
	array('HIDE_ICONS' => 'Y')
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>