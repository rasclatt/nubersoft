<form id="uploadLogo" method="post" enctype="multipart/form-data" action="/">
	<input type="hidden" name="token[ajax_edit_logo]" value="<?php echo $this->getFunction('fetch_token','ajax_edit_logo'); ?>" />
	<input type="hidden" name="action" value="ajax_edit_logo" />
	<input type="hidden" name="data" value='<?php echo json_encode(array('deliver'=>array('send_back'=>'#logo_msg','success'=>"Logo uploaded! Reload page for change.","fail"=>'Logo failed to upload.' )),JSON_FORCE_OBJECT); ?>' />
	<label>
		<div>Company Logo Upload</div>
		<div id="logo_msg"></div>
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="273" style="max-width: 200px; overflow: hidden;">
					<input type="file" name="file[]" />
				</td>
				<td width="14" rowspan="4" style="min-width: 300px; vertical-align: top;">
					<div id="logoList" data-instructions='{"action":"ajax_load_logo"}'></div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="nbr_button"><input type="submit" value="UPLOAD" style="font-size: 14px; padding: 8px 16px; float: right;" /></div>
				</td>
			</tr>
		</table>
	</label>
</form>