<?php
	if(!function_exists("is_admin"))
		return;
	
	if(!is_admin())
		return;

	$name	=	strip_tags(Safe::decode($values['ref_anchor']));
	$key	=	$values['component_type'];
	if(empty($key))
		$key	=	"unknown";
?>
		<form action="<?php echo (isset($_SERVER['HTTP_REFERER']))? $_SERVER['HTTP_REFERER']: ''; ?>" method="post">
			<input type="hidden" name="send_to" value="<?php echo $this->table; ?>" />
			<input type="hidden" name="ref_page" value="<?php echo $this->ref_page; ?>" />
			<input type="hidden" name="command" value="dup" />
			<input type="hidden" name="filter_request" value="1" />
			<input type="hidden" name="ID" value="<?php echo (isset($values['ID']))? $values['ID']:""; ?>" />
			<?php
			foreach($values as $cols => $vals) {
					if(!empty($vals) && $cols != 'ID' && $cols != 'unique_id' && $cols != 'parent_id') {
						$settings[]	=	'
						<tr>
							<td style="padding: 5px; background: linear-gradient(#CCC,#888); color: #FFF;">'.ucwords(str_replace("_"," ",$cols)).'</td>
							<td style="padding: 5px; background: linear-gradient(#888,#555); color: #FFF; max-width: 240px; overflow: hidden;">'.$vals.'</td>
						</tr>';
						}
				} ?>             
			<div class="dup_guts_container" id="dup-container-ajax_<?php echo $values['ID']; ?>">
				<table cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td style="width: 30px;">
							<img src="/core_images/core/<?php echo $icon_arr[$key]; ?>" style="width: 20px;" id="prev_<?php echo $values['ID']; ?>" />
						</td>
						<td style="width: 250px; vertical-align: middle;color: #333333; text-shadow: 1px 1px 3px #FFFFFF; text-align: left; font-family: Arial, Helvetica, sans-serif; font-size: 14px; cursor: pointer;" onClick="PowerButton('prev_<?php echo $values['ID']; ?>','toggle','.dup-item-panel')">
							<?php echo substr($name, 0, 20); echo (strlen($name) > 20)? '...':''; ?>
						</td>
						<td style="width: 40px;">
							<div class="formButton"><input id="controller<?php echo $values['ID']; ?>" type="submit" name="add" value="+" style="width: 20px; height: 20px; float: right; font-size: 14px; padding: 0 4px; margin: 0; line-height: 18px;" /></div>
						</td>
						<td onClick="ShowHide('#are-you-sure<?php echo $values['ID']; ?>_panel','','slide')">
							<div style="text-align: center; padding: 1px 4px 7px 4px; border: 1px solid #333; background-color: #C00; width: 10px; height: 10px; border-radius: 4px; font-size: 14px; line-height: 14px; font-family: Arial, Helvetica, sans-serif; margin-left: 5px; background: linear-gradient(rgb(255,155,155),rgb(255,0,0),rgb(100,0,0)); color: ">x</div>
						</td>
					</tr>
					<tr>
						<td colspan="4">
							<div id="are-you-sure<?php echo $values['ID']; ?>_panel" onClick="AjaxSimpleCall('dup-container-ajax_<?php echo $values['ID']; ?>','/core.ajax/delete.component.php?id=<?php echo $values['ID']; ?>')" style="display: none;">
								<div style="font-size: 18px; background-color: #333; color: #FFF; padding: 5px 10px; border-radius: 4px;">Click to delete component.</div>
							</div>
							<div class="dup-item-panel" id="prev_<?php echo $values['ID']; ?>_panel" style="display: none; width: 100%; max-height: 100px; overflow: auto; border: 1px solid #222;">
								<table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
									<?php if(!empty($values['file_name'])) { ?>
									<tr>
										<td colspan="2">
										<?php AutoloadFunction("render_thumb_previewer");
										$thumb	=	render_thumb_previewer($values['file_path'].$values['file_name']);
										echo $thumb['layout'];
										?>
										</td>
									</tr>
									<?php } ?>
									<?php echo implode("\r\n",$settings); ?>
								</table>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</form>