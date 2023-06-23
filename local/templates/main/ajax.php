<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arPost = array_map(array('\Sepro\Helpers', 'rhtmlspecialchars'), $_POST);

if(!empty($arPost['AJAX']))
{
    \Sepro\App::getInstance()->RestartBuffer();

    $text = '';

    switch ($arPost['AJAX'])
    {
        case 'ADD2BASKET':

            if (intval($arPost['NID']) > 0) {
                $count = intval($_SESSION['BASKET'][$arPost['NID']]);

                $_SESSION['BASKET'][$arPost['NID']] = $count + 1;
            }

            echo json_encode(true);

            break;
        case 'ENTER':
            ?>
            <div class="m-caption">Войти</div>
            <?
            if($GLOBALS['USER']->IsAuthorized())
            {
                echo '<p>Вы зарегистрированы и успешно авторизованы, '.$USER->GetFirstName().'!</p>';
            }
            else {
                ?>
                <form action="#" class="f-form validation js-form" method="post">
                    <input type="hidden" name="AJAX" value="SEND_ENTER"/>
                    <label class="ps-input err-target">
                        <span class="error-msg parsley-err"><i class="icon-minus" style="background-color: #ED1F24;"></i></span>
                        <input type="text" autocomplete="off" data-parsley-errors-messages-disabled="" required="" name="login" placeholder="">
                        <span class="pl-active">Ваш телефон или e-mail<b>*</b></span>
                    </label>
                    <label class="ps-input err-target">
                        <span class="error-msg parsley-err"><i class="icon-minus" style="background-color: #ED1F24;"></i></span>
                        <input type="password" autocomplete="off" data-parsley-errors-messages-disabled="" required="" name="pass" placeholder="">
                        <span class="pl-active">Введите пароль<b>*</b></span>
                    </label>
                    <a href="#remember-password" data-modal class="ot-link ot-link-small">Не помню пароль</a>
                    <div class="m-btn-wrp">
                        <input type="submit" class="btn" value="Войти" name="button">
                        <a href="#registration-applicant" data-modal class="btn btn-border">Зарегистрироваться</a>
                    </div>
                    <ul class="social-icons custom">
                        <li>
                            <a href="#" class="sc-ico">
                                <i class="icon-vk"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="sc-ico">
                                <i class="icon-ok"></i>
                            </a>
                        </li>
                    </ul>
                </form>
                <?
            }
            ?>
            <?
            break;
        case 'SEND_ENTER':
            $arAuthResult = $GLOBALS['USER']->Login(
                strval($arPost['login']),
                strval($arPost['pass'])
            );
            //echo Sepro\Helpers::printPre($arAuthResult);
            if($arAuthResult['TYPE']=='ERROR')
            {
                //foreach ($arResult['ERRORS'] as $error):
                $arResult['ERROR'] =  $arAuthResult['MESSAGE'];
                //endforeach;
            }
            elseif($arAuthResult)
            {
                $arResult['SEND'] = '<p>Вы успешно авторизованы</p>';//, '.$USER->GetFirstName().'!</p>';
            }
            echo json_encode($arResult);
            break;
        case 'SEND_REGISTER':
        //echo Sepro\Helpers::printPre($arPost);
        $arResult = array();
        $arResult["USER_REMEMBER"] = 'Y';
        $arResult['VALUES']['PASSWORD'] = $arPost['pass'];
        $arResult['VALUES']['CONFIRM_PASSWORD'] = $arPost['passconf'];
        $arResult['VALUES']['EMAIL'] = $arPost['email'];
        $arResult['VALUES']['LOGIN'] = $arPost['email'];
        $arResult['VALUES']['NAME'] = $arPost['name'];
        $arResult['VALUES']['PERSONAL_PHONE'] = $arPost['phone'];
//echo Sepro\Helpers::printPre($arResult);
        if(COption::GetOptionString('main', 'use_encrypted_auth', 'N') == 'Y')
        {
            //possible encrypted user password
            $sec = new CRsaSecurity();
            if(($arKeys = $sec->LoadKeys()))
            {
                $sec->SetKeys($arKeys);
                $errno = $sec->AcceptFromForm(array('REGISTER'));
                if($errno == CRsaSecurity::ERROR_SESS_CHECK)
                    $arResult["ERRORS"][] = GetMessage("main_register_sess_expired").' main_register_sess_expired';
                elseif($errno < 0)
                    $arResult["ERRORS"][] = GetMessage("main_register_decode_err", array("#ERRCODE#"=>$errno)).' main_register_decode_err';
            }
        }


        $GLOBALS['USER_FIELD_MANAGER']->EditFormAddFields("USER", $arResult["VALUES"]);

        //this is a part of CheckFields() to show errors about user defined fields
        if (!$GLOBALS['USER_FIELD_MANAGER']->CheckFields("USER", 0, $arResult["VALUES"]))
        {
            $e = $GLOBALS['APPLICATION']->GetException();
            $arResult["ERRORS"][] = substr($e->GetString(), 0, -4); //cutting "<br>"
            $GLOBALS['APPLICATION']->ResetException();
        }



        if(count($arResult["ERRORS"]) > 0)
        {
            if(COption::GetOptionString("main", "event_log_register_fail", "N") === "Y")
            {
                $arError = $arResult["ERRORS"];
                foreach($arError as $key => $error)
                    if(intval($key) == 0 && $key !== 0)
                        $arError[$key] = str_replace("#FIELD_NAME#", '"'.$key.'"', $error);
                //CEventLog::Log("SECURITY", "USER_REGISTER_FAIL", "main", false, implode("<br>", $arError));
                echo Sepro\Helpers::printPre($arError);
            }
        }
        else // if there;s no any errors - create user
        {
            //$bConfirmReq = (COption::GetOptionString("main", "new_user_registration_email_confirmation", "N") == "Y" && $arResult["EMAIL_REQUIRED"]);

            $arResult['VALUES']["CHECKWORD"] = md5(CMain::GetServerUniqID().uniqid());
            $arResult['VALUES']["~CHECKWORD_TIME"] = $GLOBALS['DB']->CurrentTimeFunction();
            $arResult['VALUES']["ACTIVE"] = "Y";
            //$arResult['VALUES']["CONFIRM_CODE"] = $bConfirmReq? randString(8): "";
            $arResult['VALUES']["LID"] = SITE_ID;
            $arResult['VALUES']["LANGUAGE_ID"] = LANGUAGE_ID;

            //$arResult['VALUES']["USER_IP"] = $_SERVER["REMOTE_ADDR"];
            //$arResult['VALUES']["USER_HOST"] = @gethostbyaddr($_SERVER["REMOTE_ADDR"]);

            if($arResult["VALUES"]["AUTO_TIME_ZONE"] <> "Y" && $arResult["VALUES"]["AUTO_TIME_ZONE"] <> "N")
                $arResult["VALUES"]["AUTO_TIME_ZONE"] = "";

            $def_group = COption::GetOptionString("main", "new_user_registration_def_group", "");
            if($def_group != "")
                $arResult['VALUES']["GROUP_ID"] = explode(",", $def_group);
            //$arResult['VALUES']['GROUP_ID'][] = $arPost['utype'];
            if($arPost['utype']==USER_GROUP_OPT)
            {// если желает в группу "опт" включаем его в группу для премодерации
                $arResult['VALUES']['GROUP_ID'][] = USER_GROUP_CHECK;
            }
            $bOk = true;

            $events = GetModuleEvents("main", "OnBeforeUserRegister", true);
            foreach($events as $arEvent)
            {
                if(ExecuteModuleEventEx($arEvent, array(&$arResult['VALUES'])) === false)
                {
                    if($err = $GLOBALS['APPLICATION']->GetException())
                        $arResult['ERRORS'][] = $err->GetString();

                    $bOk = false;
                    break;
                }
            }
            //AddMessage2Log('$arResult = '.print_r($arResult, true), 'users');

            $ID = 0;
            $user = new CUser();
            if ($bOk)
            {
                $ID = $user->Add($arResult["VALUES"]);
            }

            if (intval($ID) > 0)
            {
                $register_done = true;
                //echo 'Зарегистрирован '.$ID.'<br/>';
                // authorize user
                if (REGISTER_USER_AUTHORIZE == "Y" && $arResult["VALUES"]["ACTIVE"] == "Y")
                {
                    $rememberMe = $arResult["USER_REMEMBER"] == 'Y' ? 'Y' : 'N';
                    if (!$arAuthResult = $GLOBALS['USER']->Login($arResult["VALUES"]["LOGIN"], $arResult["VALUES"]["PASSWORD"], $rememberMe))
                        $arResult["ERRORS"][] = $arAuthResult;
                }

                $arResult['VALUES']["USER_ID"] = $ID;

                $arEventFields = $arResult['VALUES'];
                unset($arEventFields["CONFIRM_PASSWORD"]);
                //unset($arEventFields["PASSWORD"]);
                $user_type = 'розничный';
                $more_mess = '';
                $event_type = "NEW_USER";
                if($arPost['utype']==USER_GROUP_OPT)
                {
                    $user_type = 'оптовый';
                    $event_type = "NEW_USER_OPT";
                    $more_mess = '<div>В течение 24 часов с Вами свяжется менеджер для подтверждения статуса оптового покупателя.</div>';
                    //$arEventFields['OPT_TYPE'] = 'Пользователь желает зарегистрироваться в группу оптовых покупателей!<br />'; разделили почтовые шаблоны 29.09.20
                }
                $event = new CEvent;
                $event->Send($event_type, SITE_ID, $arEventFields);
                echo '<div><p class="js-refr-ok">Вы успешно зарегистрированы как '.$user_type.' пользователь. Регистрационная информация отправлена на Вашу почту'.$more_mess.'</p></div>';
            }
            else
            {
                $arResult["ERRORS"][] = $user->LAST_ERROR;
            }

            if(count($arResult["ERRORS"]) <= 0)
            {
                if(COption::GetOptionString("main", "event_log_register", "N") === "Y")
                    CEventLog::Log("SECURITY", "USER_REGISTER", "main", $ID);
            }
            else
            {
                if(COption::GetOptionString("main", "event_log_register_fail", "N") === "Y")
                    CEventLog::Log("SECURITY", "USER_REGISTER_FAIL", "main", $ID, implode("<br>", $arResult["ERRORS"]));
            }

            $events = GetModuleEvents("main", "OnAfterUserRegister", true);
            foreach ($events as $arEvent)
                ExecuteModuleEventEx($arEvent, array(&$arResult['VALUES']));
            if(!empty($arResult['ERRORS']))
            {
                ?>
                <div>
                    <div class="js-errors">
                        <?
                        foreach ($arResult['ERRORS'] as $error):
                            echo $error;
                        endforeach;
                        ?>
                    </div>
                </div>
                <?
            }
        }

        break;
        case 'SEND':
            //if($APPLICATION->CaptchaCheckCode($_POST["captcha_word"], $_POST["captcha_code"])) {
            $arNames = ['name' => 'Имя',
            'famil' => 'фамилия',
            'email' => 'e-mail',
            'phone' => 'телефон',
            'position' => 'должность',
            'company' => 'компания',
            'theme' => 'тема',
            'comment' => 'комментарий'];
            $html = '';
            foreach ($arPost as $field => $value)
            {
                if($field != 'AJAX' && $field != 'checkbox')
                {
                    $html .= $arNames[$field].': '.$value.'<br/>';
                }

            }
            $arEventFields = array('HTML' => $html);

                //AddMessage2Log('$arPost = '.print_r($arPost, true),'');
                //$arSend = array(, 'ru');
                if (CEvent::Send("FEEDBACK", SITE_ID, $arEventFields, 'Y', '', $arPost['fid'])) {
                    $arResult['SEND'] = 'Спасибо, Ваше сообщение принято!';
                }
                else
                {
                    $arResult['ERROR'] = 'Не удалось отправить сообщение';
                }
            /*}
            else
            {
                $arResult['ERROR'] = 'Неверное значение кода с картинки';
            }*/

            echo json_encode($arResult);

            break;
    }
    die();
}