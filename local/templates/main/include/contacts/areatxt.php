
<section class="ct-main m-df">
    <h2>
        <?
        $APPLICATION->IncludeFile(
            SITE_TEMPLATE_PATH."/include/contacts/head.php",
            Array(),
            Array("MODE"=>"html", "NAME"=>"заголовок")
        );
        ?></h2>
    <p style="max-width: 1580px;">
        <?
        $APPLICATION->IncludeFile(
            SITE_TEMPLATE_PATH."/include/contacts/text.php",
            Array(),
            Array("MODE"=>"html", "NAME"=>"описание")
        );
        ?>
    </p>
</section>
<section class="ct-tel-section m-df">
    <a href="tel:<?=CAkon::setPhoneNum(CONTACT_PHONE)?>" class="ct-tel"><?=CONTACT_PHONE?></a>
    <div class="ct-sub-tel">
        <?
        $APPLICATION->IncludeFile(
            SITE_TEMPLATE_PATH."/include/contacts/phon_desc.php",
            Array(),
            Array("MODE"=>"html", "NAME"=>"описание")
        );
        ?>
    </div>
</section>