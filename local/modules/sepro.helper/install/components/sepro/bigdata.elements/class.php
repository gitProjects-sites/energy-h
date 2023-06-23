<?php
use Bitrix\Main,
	Bitrix\Main\Application,
	Bitrix\Main\Localization\Loc as Loc,
	Bitrix\Main\SystemException;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

CBitrixComponent::includeComponentClass("bitrix:catalog.bigdata.products");

class BigdataElementsComponent extends CatalogBigdataProductsComponent
{
	public function executeComponent()
	{
		$context = Main\Context::getCurrent();
		$lastUsage = Main\Config\Option::get('main', 'rcm_component_usage', 0);

		if ($lastUsage == 0 || (time() - $lastUsage) > 3600)
		{
			Main\Config\Option::set('main', 'rcm_component_usage', time());
		}

		try
		{
			$this->checkModules();
		}
		catch (SystemException $e)
		{
			ShowError($e->getMessage());
			return;
		}

		$this->processRequest();
		$this->rcmParams = $this->getServiceRequestParamsByType($this->arParams['RCM_TYPE']);
		$showByIds = ($context->getServer()->getRequestMethod() == 'POST' && $context->getRequest()->getPost('rcm') == 'yes');

		if (!$showByIds)
		{
			try
			{
				if (!$this->extractDataFromCache())
				{
					$this->arResult['REQUEST_ITEMS'] = true;
					$this->arResult['RCM_PARAMS'] = $this->rcmParams;
					$this->arResult['RCM_TEMPLATE'] = $this->getTemplateName();
					$this->abortDataCache();

					if (Main\Context::getCurrent()->getRequest()->get('clear_cache') == 'Y')
					{
						$this->clearResultCache($this->getAdditionalCacheId(), '/'.$this->getSiteId().'/bitrix/catalog.bigdata.products/common');
					}

					$this->setResultCacheKeys(array());
					$this->includeComponentTemplate();
				}

				return null;
			}
			catch (SystemException $e)
			{
				$this->abortDataCache();

				if ($this->isAjax())
				{
					\Sepro\App::getInstance()->restartBuffer();
					echo CUtil::PhpToJSObject(array('STATUS' => 'ERROR', 'MESSAGE' => $e->getMessage()));
					die();
				}

				ShowError($e->getMessage());
			}
		}

		if ($showByIds)
		{
			$ajaxItemIds = $context->getRequest()->get('AJAX_ITEMS');
			if (!empty($ajaxItemIds) && is_array($ajaxItemIds))
			{
				$this->ajaxItemsIds = $ajaxItemIds;
			}
			else
			{
				$this->ajaxItemsIds = null;
			}

			$this->getProductIds();
			$this->formatResult();
		}

		if (!$this->extractDataFromCache())
		{
			$this->setResultCacheKeys(array());
			$this->includeComponentTemplate();
		}
	}

	protected function getProductIds()
	{
		$ids = parent::getProductIds();

		if(!empty($ids))
		{
			$this->arResult['IDS'] = $ids;
		}
	}
}