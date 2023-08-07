<div class="container">
	<div class="brc">
		<ul class="custom breadcrumb">
			<li><a href="/">Главная</a></li>
			<li><span>Правила использования cookie-файлов</span></li>
		</ul>
	</div>

	<h1><?$APPLICATION->ShowTitle(false)?></h1>

	<?$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		Array(
			"AREA_FILE_SHOW" => "",
			"AREA_FILE_SUFFIX" => "content",
			"EDIT_TEMPLATE" => ""
		)
	);?>
</div>