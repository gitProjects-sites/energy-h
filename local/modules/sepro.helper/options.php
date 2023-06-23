<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$arRequest = array_map(array('\Sepro\Helpers', 'rhtmlspecialchars'), $_REQUEST);
$rights = \Sepro\App::getInstance()->GetGroupRight($module_id);
$module_id = "sepro.helper";
$arLanguages = array();
$js = '';

// Перехватываем AJAX
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($arRequest['DIV']))
{
    \Sepro\App::getInstance()->RestartBuffer();

    $counter = intval($arRequest['COUNTER']);
    $step = intval($arRequest['STEP']) > 0 ? $arRequest['STEP'] : 1;

    // FINISH ?
    if(isset($arRequest['LENGTH']) && $counter >= $arRequest['LENGTH'])
    {
        $MESSAGE = new \CAdminMessage(array(
            'MESSAGE' => 'Завершено!',
            'DETAILS' => 'Обработано: '.intval($arRequest['LENGTH']).' стр;',
            'TYPE' => 'OK'
        ));

        echo json_encode(array(
            'DIV' => $arRequest['DIV'],
            'HTML' => $MESSAGE->Show(),
            'FINISH' => true
        ));

        die();
    }

    if(check_bitrix_sessid())
    {
        switch($arRequest['DIV'])
        {
            case 'TEST':

                if(intval($arRequest['LENGTH']) <= 0)
                {
                    $arRequest['LENGTH'] = \Sepro\ElementTable::GetList(array(
                        'filter' => array(
                            'ACTIVE' => 'Y'
                        )
                    ))->getSelectedRowsCount();
                }

                $rsElements = \Sepro\ElementTable::GetList(array(
                    'order' => array('ID' => 'ASC'),
                    'filter' => array(
                        '>=ID' => intval($arRequest['ELEMENT_ID']),
                        'ACTIVE' => 'Y'
                    ),
                    'select' => array('ID')
                ));

                $N = 1;
                while($arElement = $rsElements->fetch())
                {
                    if($N >= intval($step))
                    {
                        $arRequest['ELEMENT_ID'] = $arElement['ID'];
                        break;
                    }

                    $counter++;
                    $N++;
                }

                break;
        }

        $M = new \CAdminMessage(array(
            'MESSAGE' => 'Процесс выполнения:',
            "DETAILS" => "#PROGRESS_BAR#",
            "HTML" => true,
            "TYPE" => "PROGRESS",
            "PROGRESS_TOTAL" => 100,
            "PROGRESS_VALUE" => round(100 / ($arRequest['LENGTH'] / $counter), 2)
        ));

        $arRequest['HTML'] = $M->Show();
    }
    else
    {
        $arRequest['ERRORS'][] = 'Ваша сессия больше не актуальна';
    }

    if(!empty($arRequest['ERRORS']))
    {
        $MESSAGE = new \CAdminMessage(array(
            'MESSAGE' => 'Ошибки импорта каталога:',
            'DETAILS' => implode('<br>',$arRequest['ERRORS']),
            'TYPE' => 'ERROR'
        ));

        echo json_encode(array(
            'DIV' => $arRequest['DIV'],
            'HTML' => $MESSAGE->Show(),
            'FINISH' => true
        ));

        die();
    }

    echo json_encode(array(
        'COUNTER' => $counter,
        'DIV' => $arRequest['DIV'],
        'STEP' => $arRequest['STEP'],
        'HTML' => $arRequest['HTML'],
        'ELEMENT_ID' => $arRequest['ELEMENT_ID'],
        'LENGTH' => $arRequest['LENGTH'],
        'autosave_id' => $arRequest['autosave_id'],
        'sessid' => $arRequest['sessid']
    ));

    die();
}

// Прячем в Буффер вывод JS
ob_start();
?>
    <script type="text/javascript">

        window.oPage = window.oPage || {};
        oPage.xhr = false;

        function abortProgress(btn)
        {
            var $this = $(btn);

            if($this.hasClass('active'))
            {
                $this.attr('disabled', 'disabled');
                $this.removeClass('active');

                CloseWaitWindow();
            }

            oPage.xhr.abort();
        }

        function ajaxProgress(request)
        {
            var data = request || {};

            if(request instanceof HTMLElement)
            {
                var $form = $(request),
                    query = $form.serializeArray(),
                    obj = {};

                for (var i in query)
                {
                    obj[query[i]['name']] = query[i]['value'];
                }

                data = obj;
            }

            var $special = $('#'+data.DIV+'_layout'),
                $abort = $special.find('.js-abort'),
                $errors = $('.adm-info-message-red'),
                $progress = $special.next('.js-progress'),
                counter = data.COUNTER || 0;

            ShowWaitWindow();

            if(!$abort.hasClass('active'))
            {
                $abort.attr('disabled', false);
                $abort.addClass('active');
            }

            if($errors.length > 0)
            {
                $errors.remove();
            }

            if(data.hasOwnProperty('HTML'))
            {
                if(data.hasOwnProperty('FINISH'))
                {
                    $special.before(data.HTML);
                    $progress.remove();

                    $abort.removeClass('active');
                    $abort.attr('disabled', 'disabled');

                    oPage.xhr.abort();
                    CloseWaitWindow();

                    return false;
                }

                if($progress.length > 0)
                {
                    $progress.html(data.HTML);
                }
                else
                {
                    var OPBar = document.createElement('div');
                    OPBar.className = 'js-progress';
                    OPBar.innerHTML = data.HTML;

                    $special.after(OPBar);
                }
            }

            //console.log(counter);

            oPage.xhr = $.ajax({
                data: data,
                type: 'post',
                dataType: 'json',
                success: function(result)
                {
                    return ajaxProgress(result);
                },
                error: function(xhr, st)
                {
                    console.log(xhr.responseCode);
                    if(st=='parsererror')
                    {
                        alert('ошибка json парсера');
                        CloseWaitWindow();
                    }
                }
            });

            return false;
        }
    </script>
<?
$js = ob_get_contents();
ob_end_clean();

if ($rights >= "R")
{
    $rsLanguages = \Bitrix\Main\Localization\LanguageTable::GetList(
        array(
            'order' => array('ID' => 'ASC'),
            'select' => array('ID', 'NAME')
        )
    );

    while($arLanguage = $rsLanguages->fetch())
    {
        $arLanguages[$arLanguage['ID']] = $arLanguage['NAME'];
    }

    $COptions = new CModuleOptions(
        $module_id,
        array(
            array(
                'DIV' => 'general',
                'TYPE' => 'SETTING',
                'TAB' => 'Настройки',
                'TITLE' => 'Настройка параметров модуля',
                'GROUPS' => array(
                    array(
                        'TITLE' => 'Константы',
                        'OPTIONS' => array(
                            'SITE_NAME' => array(
                                'TITLE' => 'Основной домен сайта',
                                'TYPE' => 'TEXT',
                                'SORT' => '0',
                                'VALUE' => SITE_NAME,
                            ),
                            'DOCUMENT_ROOT' => array(
                                'TITLE' => 'Абсолютный путь к корню сайта',
                                'TYPE' => 'TEXT',
                                'SORT' => '1',
                                'VALUE' => DOCUMENT_ROOT
                            ),
                            'LANGUAGE_ID' => array(
                                'TITLE' => 'Основной язык сайта',
                                'TYPE' => 'SELECT',
                                'SORT' => '2',
                                'VALUE' => LANGUAGE_ID,
                                'OPTIONS' => $arLanguages
                            ),
                            'SYSTEM_LOG' => array(
                                'TITLE' => 'Путь к файлу логирования о системных ошибках',
                                'TYPE' => 'TEXT',
                                'SORT' => '4',
                                'VALUE' => SYSTEM_LOG
                            )
                        )
                    )
                )
            ),
            array(
                'DIV' => 'TEST',
                'TYPE' => 'SPECIAL',
                'TAB' => 'AJAX',
                'TITLE' => 'Тестирование AJAX',
                'GROUPS' => array(
                    array(
                        'OPTIONS' => array(
                            'STEP' => array(
                                'TITLE' => 'Диапазон одной итерации',
                                'TYPE' => 'INTEGER',
                                'SORT' => '0',
                                'VALUE' => 5
                            )
                        )
                    )
                )
            )
        ),
        $js
    );

    $COptions->saveOptions();
    $COptions->showHTML();
}
