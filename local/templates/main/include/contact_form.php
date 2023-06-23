<section class="contact-form-2">
    <div class="ct-form-flex">
        <div class="ct-form-main">
            <div class="ct-form-main-inner">
                <h2 class="m9" style="font-weight: 700;">
                    <?
                    $APPLICATION->IncludeFile(
                        SITE_TEMPLATE_PATH."/include/form_head.php",
                        Array(),
                        Array("MODE"=>"html", "NAME"=>"заголовок")
                    );
                    ?>
                </h2>
                <p class="m3" style="max-width: 1280px;">
                    <?
                    $APPLICATION->IncludeFile(
                        SITE_TEMPLATE_PATH."/include/form_txt.php",
                        Array(),
                        Array("MODE"=>"html", "NAME"=>"описание")
                    );
                    ?>
                </p>

                <form action="#" class="f-form validation js-form" method="post">
                    <input type="hidden" name="AJAX" value="SEND"/>
                    <div class="f-item full">
                        <label class="fc-component fc-input fc-placeholder-shift err-target" data-fs-type="input-default">
                            <span class="fc-in-paceholder"><span>Ваше имя</span></span>
                            <input type="text" placeholder="" value="" name="name">
                        </label>
                    </div>
                    <div class="f-item">
                        <label class="fc-component fc-input fc-placeholder-shift err-target" data-fs-type="input-default">
                            <span class="fc-in-paceholder"><span>Ваш e-mail</span></span>
                            <input type="email" placeholder="" value="" name="name">
                        </label>
                    </div>
                    <div class="f-item">
                        <label class="fc-component fc-input fc-placeholder-shift err-target" data-fs-type="input-default">
                            <span class="fc-in-paceholder"><span>Ваш телефон<b>*</b></span></span>
                            <input type="tel" placeholder="" data-parsley-pattern="\+[0-9]\s\([0-9]{3}\)\s[0-9]{3}-[0-9]{2}-[0-9]{2}" data-parsley-errors-messages-disabled required value="" name="name">
                        </label>
                    </div>

                    <div class="f-item full">
                        <label class="fc-component fc-textarea fc-input fc-placeholder-shift" data-fs-type="textarea-default">
                            <span class="fc-in-paceholder"><span>Ваш вопрос</span></span>
                            <textarea rows="1" name="textarea" placeholder=""></textarea>
                        </label>
                    </div>
                    <div class="f-item full form-btn-wrp">
                        <input type="submit" class="btn btn-black" value="СВЯЗАТЬСЯ С НАМИ" name="button">
                        <label class="cr err-target">
                            <input type="checkbox" checked data-parsley-required data-parsley-trigger="click" data-parsley-errors-messages-disabled name="checkbox">
                            <span class="cr-ps">
                                    <i class="cr-icon"></i>
                                    <span class="cr-text">
                                        Нажимая на кнопку «Связаться с нами» Вы даете согласие на обработку <a href="#">персональных данных</a>
                                    </span>
                                </span>
                        </label>
                    </div>
                </form>
            </div>
        </div>
        <div class="ct-form-decor">
            <div class="ct-form-img-outer">
                <div class="ct-form-img">
                    <img src="<?=SITE_TEMPLATE_PATH?>/img/form-decor.jpg" alt="">
                    <div class="ct-logo">
                        <svg width="194" viewBox="0 0 194 65">
                            <use xlink:href="#logo" width="64" height="45" />
                            <use xlink:href="#logo-text" height="65" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>