<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * @global CMain $APPLICATION
 */

global $APPLICATION;

//delayed function must return a string
if(empty($arResult))
	return "";

$strReturn = '';
$strReturn .= '<div class="br-box breadcrumb-one" itemscope itemtype="http://schema.org/BreadcrumbList">';

$itemSize = count($arResult);
$counter = 0;
for($index = 1; $index < $itemSize; $index++)
{
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
    //$arrow = ($index > 0? '<i class="icon-arr-l"></i>' : '');
    $arrow = '<i class="icon-arr-l"></i>';

	if($arResult[$index]["LINK"] <> "")
	{
        $counter++;
		$strReturn .= '
			<span class="bx-breadcrumb-item" id="bx_breadcrumb_'.$index.'" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
				<a href="'.$arResult[$index]["LINK"].'" class="br-one" title="'.$title.'" itemprop="item">
				'.$arrow.'
					<span itemprop="name">'.$title.'</span>
				</a>
				<meta itemprop="position" content="'.$counter.'" />
			</span>';
	}
	else
	{
		$strReturn .= '
			<span>
				'.$arrow.'
				<span>'.$title.'</span>
			</span>';
	}
}

$strReturn .= '</div>';

return $strReturn;
?>
