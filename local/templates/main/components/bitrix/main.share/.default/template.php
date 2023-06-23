<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//echo Sepro\Helpers::printPre($arResult);
if ($arResult["PAGE_URL"] <> '')
{
	?>
    <?
		if (is_array($arResult["BOOKMARKS"]) && !empty($arResult["BOOKMARKS"]))
		{
			?>
            <ul class="social-list custom">
            <?
			foreach($arResult["BOOKMARKS"] as $name => $arBookmark)
			{
				?>
                <li>
                        <?=$arBookmark["ICON"]?>
                        <!--svg width="23" viewBox="0 0 175 157">
                            <use xlink:href="#tlg" />
                        </svg-->
                </li>
                <?
			}
			?></ul>
            <?
		}
		?><?
}
else
{
	?><?=GetMessage("SHARE_ERROR_EMPTY_SERVER")?><?
}
?>