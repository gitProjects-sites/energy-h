<?php if(!check_bitrix_sessid()) return;?>
<?php
$errors=false;

if($errors===false):
	echo CAdminMessage::ShowNote(GetMessage("MOD_UNINST_OK"));
else:
	for($i=0; $i<count($errors); $i++)
		$alErrors .= $errors[$i]."<br>";
	echo CAdminMessage::ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE" =>GetMessage("MOD_UNINST_ERR"), "DETAILS"=>$alErrors, "HTML"=>true));
endif;
?>
<form action="<?php echo $APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?php echo LANG?>">
	<input type="submit" name="" value="<?php echo GetMessage("MOD_BACK")?>">	
<form>
