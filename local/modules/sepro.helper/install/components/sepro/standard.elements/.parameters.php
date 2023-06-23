<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main,
    \Bitrix\Main\Localization\Loc;

Loc::setCurrentLang(LANGUAGE_ID);
Loc::loadMessages(__FILE__);

try
{
    $iblockTypes = \CIBlockParameters::GetIBlockTypes(Array("-" => " "));

    $arSort = array('SORT' => 'ASC', 'NAME' => 'ASC');
    $arIblocks = array(0 => " ");
    $arSKUIBlock = array();
    $arIBSections = array();
    $arSections = array();
    $arProperties = array();
    $arGroups = array();
    $arOfferProperties = array();

    if (!empty($arCurrentValues['IBLOCK_TYPE']))
    {
        foreach(\Sepro\IBlock::getIBlocksByType($arCurrentValues['IBLOCK_TYPE']) as $arIblock)
        {
            $arIblocks[$arIblock['ID']] = $arIblock['NAME'];
        }
    }

    if(!empty($arCurrentValues['IBLOCK_ID']))
    {
        $arIBSections = \Sepro\IBlock::getSections();
        $arIBSections = $arIBSections[$arCurrentValues['IBLOCK_ID']]['SECTIONS'];

        foreach($arIBSections as $NID => $arSection)
        {
            $arSections[$arSection['ID']] = str_repeat('. ', $arSection['DEPTH_LEVEL']).$arSection['NAME'];
        }

        $arIblock = \Sepro\IBlock::getIBlock($arCurrentValues['IBLOCK_ID']);

        foreach($arIblock['PROPERTIES'] as $arProperty)
        {
            $arProperties[$arProperty['ID']] = $arProperty['NAME'];
        }

        if($arCurrentValues['CHECK_MODULE_CATALOG'] == "Y")
        {
            $rsGroup = \CCatalogGroup::GetList(
                array("ID" => "ASC"),
                array(),
                false,
                false,
                array('ID', 'NAME_LANG')
            );

            while($arGroup = $rsGroup->fetch())
            {
                $arGroups[$arGroup['ID']] = $arGroup['NAME_LANG'];
            }

            if(intval($arIblock['SKU_IBLOCK_ID']) > 0)
            {
                $arSKUIBlock = \Sepro\IBlock::getIBlock($arIblock['SKU_IBLOCK_ID']);

                foreach($arSKUIBlock['PROPERTIES'] as $arProperty)
                {
                    if($arIblock['SKU_PROPERTY_ID'] == $arProperty['ID']) continue;

                    $arOfferProperties[$arProperty['ID']] = $arProperty['NAME'];
                }
            }
        }
    }

    $sortFields = array(
        'ID' => Loc::getMessage('STANDARD_ELEMENTS_LIST_PARAMETERS_SORT_ID'),
        'NAME' => Loc::getMessage('STANDARD_ELEMENTS_LIST_PARAMETERS_SORT_NAME'),
        'ACTIVE_FROM' => Loc::getMessage('STANDARD_ELEMENTS_LIST_PARAMETERS_SORT_ACTIVE_FROM'),
        'SORT' => Loc::getMessage('STANDARD_ELEMENTS_LIST_PARAMETERS_SORT_SORT'),
        'TIMESTAMP_X' => Loc::getMessage('STANDARD_ELEMENTS_LIST_PARAMETERS_SORT_TIMESTAMP_X')
    );

    $sortDirection = array(
        'ASC' => Loc::getMessage('STANDARD_ELEMENTS_LIST_PARAMETERS_SORT_ASC'),
        'DESC' => Loc::getMessage('STANDARD_ELEMENTS_LIST_PARAMETERS_SORT_DESC')
    );

    // НАЧАЛЬНЫЕ ПАРАМЕТРЫ
    $arIBParams = array(
        'IBLOCK_TYPE' => Array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('STANDARD_ELEMENTS_LIST_PARAMETERS_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $iblockTypes,
            'DEFAULT' => '',
            'REFRESH' => 'Y'
        ),
        'IBLOCK_ID' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('STANDARD_ELEMENTS_LIST_PARAMETERS_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIblocks,
            'REFRESH' => 'Y'
        )
    );

    // ДОБАВЛЯЕМ ПАРАМЕТРЫ ДЛЯ ВЫБОРА РАЗДЕЛОВ
    if(!empty($arSections))
    {
        $arIBParams['SECTION_ID'] = array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('STANDARD_ELEMENTS_LIST_PARAMETERS_SECTION_ID'),
            'TYPE' => 'LIST',
            'MULTIPLE' => 'Y',
            'VALUES' => $arSections,
            'SIZE' => '5'
        );

    }

    // ВЫВОДИМ СВОЙСТВА ДЛЯ ПОДКЛЮЧЕНИЯ
    if(!empty($arProperties))
    {
        $arIBParams['ITEM_PROPERTIES'] = array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('STANDARD_ELEMENTS_LIST_PARAMETERS_ITEM_PROPERTIES'),
            'TYPE' => 'LIST',
            'MULTIPLE' => 'Y',
            'VALUES' => $arProperties,
            'SIZE' => '5',
        );
    }

    // ИНФОБЛОК ЯВЛЯЕТСЯ ТОРГОВЫМ КАТАЛОГОМ

    $arIBParams['CHECK_MODULE_CATALOG'] = array(
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('STANDARD_ELEMENTS_LIST_PARAMETERS_CHECK_MODULE_CATALOG'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    );

    /*
     * ВЫБРАТЬ ТИП ЦЕН ДЛЯ ВЫБОРКИ
     * */

    if(!empty($arGroups)){

        $arIBParams['CATALOG_GROUPS'] = array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('STANDARD_ELEMENTS_LIST_PARAMETERS_CATALOG_GROUPS'),
            'TYPE' => 'LIST',
            'MULTIPLE' => 'Y',
            'VALUES' => $arGroups,
            'SIZE' => '3',
        );

    }

    /*
     * ВЫБРАТЬ СВОЙСТВА ТОРГОВЫХ ПРЕДЛОЖЕНИЙ
     * */

    if(!empty($arOfferProperties))
    {
        $arIBParams['OFFER_PROPERTIES'] = array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('STANDARD_ELEMENTS_LIST_PARAMETERS_OFFER_PROPERTIES'),
            'TYPE' => 'LIST',
            'MULTIPLE' => 'Y',
            'VALUES' => $arOfferProperties,
            'SIZE' => '5',
        );

        $arIBParams['RELATION_PROPS'] = array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('STANDARD_ELEMENTS_LIST_PARAMETERS_RELATION_PROPS'),
            'TYPE' => 'LIST',
            'MULTIPLE' => 'Y',
            'VALUES' => $arOfferProperties,
            'SIZE' => '5',
        );
    }

    $arADParams = array(
        'SORT_FIELD1' => array(
            'PARENT' => 'SORTING',
            'NAME' => Loc::getMessage('STANDARD_ELEMENTS_LIST_PARAMETERS_SORT_FIELD1'),
            'TYPE' => 'LIST',
            'VALUES' => $sortFields,
            'ADDITIONAL_VALUES' => 'Y'
        ),
        'SORT_DIRECTION1' => array(
            'PARENT' => 'SORTING',
            'NAME' => Loc::getMessage('STANDARD_ELEMENTS_LIST_PARAMETERS_SORT_DIRECTION1'),
            'TYPE' => 'LIST',
            'VALUES' => $sortDirection
        ),
        'SORT_FIELD2' => array(
            'PARENT' => 'SORTING',
            'NAME' => Loc::getMessage('STANDARD_ELEMENTS_LIST_PARAMETERS_SORT_FIELD2'),
            'TYPE' => 'LIST',
            'VALUES' => $sortFields,
            'ADDITIONAL_VALUES' => 'Y'
        ),
        'SORT_DIRECTION2' => array(
            'PARENT' => 'SORTING',
            'NAME' => Loc::getMessage('STANDARD_ELEMENTS_LIST_PARAMETERS_SORT_DIRECTION2'),
            'TYPE' => 'LIST',
            'VALUES' => $sortDirection
        ),
        'FILTER_NAME' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('STANDARD_ELEMENTS_LIST_PARAMETERS_FILTER_NAME'),
            'TYPE' => 'STRING',
            'DEFAULT' => ''
        ),
        'COUNT' =>  array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('STANDARD_ELEMENTS_LIST_PARAMETERS_COUNT'),
            'TYPE' => 'STRING',
            'DEFAULT' => '0'
        ),
        'SHOW_NAV' => array(
            'PARENT' => 'NAVIGATION',
            'NAME' => Loc::getMessage('STANDARD_ELEMENTS_LIST_PARAMETERS_SHOW_NAV'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ),
        'CACHE_TIME' => array(
            'DEFAULT' => 3600
        )
    );

    if($arCurrentValues['SHOW_NAV'] == "Y")
    {
        $arADParams['VARIABLE_NAV'] = array(
            'PARENT' => 'NAVIGATION',
            'NAME' => Loc::getMessage('STANDARD_ELEMENTS_LIST_PARAMETERS_VARIABLE_NAV'),
            'TYPE' => 'STRING',
            'DEFAULT' => 'PAGEN'
        );
    }

    $arComponentParameters = array(
        'GROUPS' => array(
            'SORTING' => array(
                "NAME" => Loc::getMessage('STANDARD_ELEMENTS_LIST_GROUPS_SORTING'),
                "SORT" => "150"
            ),
            'NAVIGATION' => array(
                "NAME" => Loc::getMessage('STANDARD_ELEMENTS_LIST_GROUPS_NAVIGATION'),
                "SORT" => "650"
            )
        ),
        'PARAMETERS' => array_merge($arIBParams, $arADParams)
    );

}
catch (Main\LoaderException $e)
{
    ShowError($e->getMessage());
}
?>