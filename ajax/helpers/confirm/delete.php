<?php
if(!function_exists("is_admin"))
	return false;

use \Nubersoft\nApp as nApp;
use \Nubersoft\nApp as Safe;

if(!is_admin())
	exit;
	
if(empty($setData['unique_id'])) {
	include(__DIR__.'/no.action.php');
	return;
}
// Save token
$nProccessor	=	nApp::nToken()->getSetToken('nProcessor',array('ajaxconfirm',rand(1000,9999)),true);
?>
<div class="nbr_overview_wrap">
	<table style="width: 100%;" border="0" cellpadding="0" cellspacing="0">
<?php		foreach($setData as $key => $value) {
				if(!empty($value)) {
?>
		<tr>
			<td style="padding: 8px; border-bottom: 2px groove; white-space: nowrap;"><strong><?php echo ucwords(str_replace("_", " ", $key)); ?>:</strong></td>
			<td style="border-bottom:  2px groove;"><div style="padding: 5px; max-height: 300px; overflow: auto;"><?php echo $value; ?></div></td>
		</tr>
<?php			}
			}
?>	</table>
</div>
<div style="display:block; padding: 5px;">
	<form action="<?php echo (!empty($_SERVER['HTTP_REFERER']))? Safe::encodeSingle($_SERVER['HTTP_REFERER']):""; ?>" enctype="multipart/form-data" method="post">
		<input type="hidden" name="token[nProcessor]" value="<?php echo $nProccessor; ?>">
		<input type="hidden" name="requestTable" value="<?php echo $table; ?>" />
		<input type="hidden" name="delete" value="on" />
		<input type="hidden" name="ID" value="<?php echo $setData['ID']; ?>" />
		<input type="hidden" name="unique_id" value="<?php echo $setData['unique_id']; ?>" />
		<div class="nbr_button"><input type="submit" name="update" value="CONFIRM" /></div>
	</form>
</div>