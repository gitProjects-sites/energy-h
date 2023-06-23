<?
use \Bitrix\Main\Config\Option,
    \Bitrix\Main\Application,
    \Bitrix\Main\Context;

class CModuleOptions
{
    private $mid; // SEPRO.HELPER
    private $arCUROptions = array();
    private $arOptions = array();
    private $arFields = array();
    private $arGroups = array();
    private $js = '';

    private static $requests = array();

    public final function __construct($mid, $arFields, $js = false)
    {
        $this->mid = htmlspecialchars($mid);
        $this->js = $js;

        $ORMOption = \Sepro\ORMFactory::compile('b_option');

        $rsOptions = $ORMOption::GetList(array(
            'filter' => array(
                '=MODULE_ID' => $this->mid
            ),
            'select' => array(
                'NAME',
                'VALUE'
            )
        ));

        while($arOption = $rsOptions->fetch())
        {
            $this->arCUROptions[$arOption['NAME']] = $arOption['VALUE'];
        }

        foreach($arFields as $arTab)
        {
            $this->arFields[$arTab['DIV']] = array(
                "DIV" => $arTab['DIV'],
                "TYPE" => $arTab['TYPE'],
                "TAB" => $arTab['TAB'],
                "TITLE" => $arTab['TITLE']
            );

            if($arTab['TYPE'] !== 'SPECIAL')
            {
                foreach($arTab['GROUPS'] as &$arGroup)
                {
                    foreach($arGroup['OPTIONS'] as $name => &$arOption)
                    {
                        $this->arOptions[$name] = $arOption['VALUE'];
                    }
                }
            }

            $this->arGroups[$arTab['DIV']] = $arTab['GROUPS'];
        }
    }

    private function getRequests()
    {
        if(empty(self::$requests))
        {
            self::$requests = Context::getCurrent()->getRequest();
        }

        return self::$requests;
    }

    public function saveOptions()
    {
        self::getRequests();

        if(self::$requests->get('update') !== 'Y') return false;

        Application::getConnection()->query("DELETE FROM `b_option` WHERE `MODULE_ID` = '".$this->mid."'");

        foreach($this->arOptions as $name => $value)
        {
            if(empty(self::$requests[$name])) continue;

            Option::Set($this->mid, $name, self::$requests[$name]);

            $this->arCUROptions[$name] = self::$requests[$name];
        }

        return true;
    }

    public function showHTML()
    {
        if(!empty($this->js))
        {
            CJSCore::Init(array('jquery'));
            echo $this->js;
        }

        foreach($this->arFields as $arTab)
        {
            $obTab = new CAdminTabControl($arTab['DIV'], array($arTab));
            $obTab->Begin();

            $arForm = array('id' => $arTab['DIV'].'_form');

            if($arTab['TYPE'] == 'SPECIAL')
            {
                $arForm['onsubmit'] = 'ajaxProgress(this); return false;';
            }

            echo \Sepro\Form::start(
                    \Sepro\App::getInstance()->GetCurPageParam(
                        'mid='.htmlspecialchars($this->mid).'&lang='.LANGUAGE_ID.'&mid_menu=1',
                        array('mid', 'lang', 'mid_menu')
                    ),
                    'post',
                    $arForm
                );

            echo \Sepro\Form::input('hidden', 'sessid', bitrix_sessid());

            $obTab->BeginNextTab();

            foreach($this->arGroups[$arTab['DIV']] as $arGroup)
            {
                if(strlen($arGroup['TITLE']) > 0)
                {
                    echo '<tr class="heading"><td colspan="2">'.$arGroup['TITLE'].'</td></tr>';
                }

                foreach($arGroup['OPTIONS'] as $name => $arOption)
                {
                    $input = '';
                    $attr = array(
                        'PLACEHOLDER' => strlen($arOption['PLACEHOLDER']) > 0 ? htmlspecialchars($arOption['PLACEHOLDER']) : ''
                    );

                    if(!empty($this->arCUROptions[$name]))
                    {
                        $arOption['VALUE'] = $this->arCUROptions[$name];
                    }

                    switch($arOption['TYPE'])
                    {
                        case 'INTEGER':
                        case 'TEXT':

                            $attr = array_merge($attr, array(
                                'size' => 50,
                                'maxlength' => 255
                            ));

                            $input = \Sepro\Form::input(
                                $arOption['TYPE'] == 'INTEGER' ? 'number' : 'text',
                                $name,
                                $arOption['VALUE'],
                                false,
                                $attr
                            );

                            break;

                        case 'CHECKBOX':

                            if($arOption['VALUE'] == 'Y')
                            {
                                $attr['checked'] = 'checked';
                            }

                            $input = \Sepro\Form::input(
                                'checkbox',
                                $name,
                                $arOption['VALUE'],
                                false,
                                $attr
                            );

                            break;
                        case 'SELECT':

                            $input = \Sepro\Form::select($name, $arOption['OPTIONS'], $arOption['VALUE']);

                            break;

                        case 'TEXTAREA':

                            $attr = array_merge($attr, array(
                                'cols' => 70,
                                'rows' => 7
                            ));

                            $input = \Sepro\Form::textarea(
                                $name,
                                $arOption['VALUE'],
                                false,
                                $attr
                            );

                            break;
                    }

                    echo '<tr>';

                    if(strlen($arOption['TITLE']) > 0)
                    {
                        echo '<td valign="middle" width="40%">'.$arOption['TITLE'].'</td>';
                    }

                    echo '<td valign="top" nowrap'.(strlen($arOption['TITLE']) > 0 ? '' : ' colspan="2" align="center"').'>'.$input.'</td></tr>';
                }
            }

            $obTab->Buttons();

            $submit = 'Сохранить';

            switch($arTab['TYPE'])
            {
                case 'SPECIAL':

                    $submit = "Запустить";

                    break;

                case 'SETTING':

                    echo \Sepro\Form::input('hidden', 'update', 'Y');

                    break;
            }

            echo \Sepro\Form::input('submit', 'submit', $submit);

            if($arTab['TYPE'] == 'SPECIAL')
            {
                echo \Sepro\Form::input(
                    'button',
                    'abort',
                    'Сбросить',
                    false,
                    array(
                        'disabled' => 'disabled',
                        'class' => 'js-abort',
                        'onclick' => 'abortProgress(this);'
                    )
                );

                echo \Sepro\Form::input('hidden', 'DIV', $arTab['DIV']);
            }

            echo \Sepro\Form::end();

            $obTab->End();

            echo '<br>';
        }
    }
}