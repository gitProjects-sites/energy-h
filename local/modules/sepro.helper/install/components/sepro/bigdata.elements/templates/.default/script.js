function bx_rcm_get_from_cloud(injectId, rcmParameters, localAjaxData)
{
	var url = 'https://analytics.bitrix.info/crecoms/v1_0/recoms.php';
	var data = BX.ajax.prepareData(rcmParameters);

	if (data)
	{
		url += (url.indexOf('?') !== -1 ? "&" : "?") + data;
		data = '';
	}

	var onready = function(response) {

		if (!response.items)
		{
			response.items = [];
		}
		BX.ajax({
			url: '/local/components/sepro/bigdata.elements/ajax.php?'+BX.ajax.prepareData({'AJAX_ITEMS': response.items, 'RID': response.id}),
			method: 'POST',
			data: localAjaxData,
			dataType: 'html',
			processData: false,
			start: true,
			onsuccess: function (html) {
				var ob = BX.processHTML(html);

				// inject
				BX(injectId).innerHTML = ob.HTML;

				BX.ajax.processScripts(ob.SCRIPT);
			}
		});
	};

	BX.ajax({
		'method': 'GET',
		'dataType': 'json',
		'url': url,
		'timeout': 3,
		'onsuccess': onready,
		'onfailure': onready
	});
}