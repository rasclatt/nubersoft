<?php
if(!function_exists("is_admin"))
	return false;

if(!is_admin())
	exit;
		
// Save token
$nProccessor	=	nApp::nToken()->getSetToken('nProcessor',array('ajaxconfirm',rand(1000,9999)),true);
?>
<div style="overflow: auto; display: block; margin: 15px auto; border: 1px solid #EBEBEB; padding: 30px; background-color: #EBEBEB; font-size: 14px;">
	<table style="width: 100%;" border="0" cellpadding="0" cellspacing="0">
<?php		foreach($show_data as $key => $value) {
				if(!empty($value)) { ?>
		<tr>
			<td style="padding: 5px; border-bottom: 1px solid #333; white-space: nowrap;"><strong><?php echo ucwords(str_replace("_", " ", $key)); ?>:</strong></td>
			<td style="padding: 5px; border-bottom: 1px solid #333;"><?php echo $value; ?></td>
		</tr>
<?php									}
			}
?>	</table>
</div>
<div style="display:block; padding: 5px;">
	<form action="<?php echo (!empty($_SERVER['HTTP_REFERER']))? $_SERVER['HTTP_REFERER']:""; ?>" enctype="multipart/form-data" method="post" style=" float: right;">
		<input type="hidden" name="token[nProcessor]" value="<?php echo $nProccessor; ?>">
<?php						if(is_array($useArray['requestTable'])) {
				foreach($useArray['requestTable'] as $keys => $values) {
?>
					<input type="hidden" name="requestTable[]" value="<?php echo $values; ?>" />
<?php										unset($keys, $values);
					}
			}
		else {
?>
			<input type="hidden" name="requestTable" value="<?php echo $useArray['requestTable']; ?>" /><?php
				unset($keys, $values);
			}
		
		if(!empty($useArray['unique_id'])) {
				if(is_array($useArray['unique_id'])) {
						foreach($useArray['unique_id'] as $keys => $values) {
?>
			<input type="hidden" name="unique_id[]" value="<?php echo $values; ?>" />
<?php												unset($keys, $values);
							}
					}
				else {
?>
			<input type="hidden" name="unique_id" value="<?php echo $useArray['unique_id']; ?>" />
<?php										unset($keys, $values);
					}
			}	
		
		if(is_array($useArray['ID'])) {
				foreach($useArray['ID'] as $keys => $values) { ?>
			<input type="hidden" name="ID[]" value="><?php $values; ?>" />
<?php										unset($keys, $values);
					}
			}
		else {
				echo '<input type="hidden" name="ID" value="' . $useArray['ID'] . '" />';
						unset($keys, $values);
			}
	
		$merge_array	=	array_merge($useArray,$show_data);
		
		foreach($merge_array as $keys => $values) {
				if($keys !== 'requestTable' && $keys !== 'ID' && $keys !== 'unique_id') {
?>								<input type="hidden" name="<?php echo $keys; ?>" value="<?php echo $values; ?>" />
<?php									}
			}
?>								<div class="nbr_button"><input type="submit" name="update" value="CONFIRM" /></div>
		</form>
	</div>