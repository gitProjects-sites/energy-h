<?
if(isset($arParams['DOCS']['ID'])) // документы идут не списком
{
    $arParams['DOCS'] = [$arParams['DOCS']];
}
//echo  Sepro\Helpers::printPre($arParams);
?>
<div class="ou-slider swiper">
    <div class="swiper-wrapper">
        <? foreach ($arParams['DOCS'] as $arDoc):
            $arFileData = explode('.', $arDoc['ORIGINAL_NAME']);
            $flast = $arFileData[count($arFileData) - 1];
            if(count($arFileData) == 2)
            {
                $fname = $arFileData[0];
            }
            else
            {
                unset($arFileData[count($arFileData) - 1]);
                $fname = implode( '.', $arFileData);
            }
            ?>
            <div class="swiper-slide">
                <a href="<?=$arDoc['SRC']?>" target="_blank" class="dw-box">
                        <span class="dw-name">
                            <?=$fname?>
                        </span>
                    <span class="dw-type">
                            Скачать фаил <?=$flast?>
                        </span>
                </a>
            </div>
        <? endforeach;?>
    </div>
</div>
<div class="ou-btn-wrp">
    <button class="ou-btn sw-button-prev"><i class="icon-arr-md-l"></i></button>
    <button class="ou-btn sw-button-next"><i class="icon-arr-md-r"></i></button>
</div>