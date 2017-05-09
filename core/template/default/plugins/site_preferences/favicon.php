<form id="uploadFAVICON" method="post" enctype="multipart/form-data" action="/index.php">
	<input type="hidden" name="token[ajax_edit_favicon]" value="<?php echo $this->getHelper('nToken')->setToken('ajax_edit_favicon'); ?>" />
	<input type="hidden" name="token[nProcessor]" value="<?php echo $nProcToken; ?>" />
	<input type="hidden" name="action" value="autoset" />
	<input type="hidden" name="use" value="ajax_edit_favicon" />
	<input type="hidden" name="nbr_dropspot" value="#fav_msg" />
	<input type="hidden" name="nbr_msg" value='<?php echo json_encode(array('success'=>"Icon uploaded!","fail"=>'Icon failed to upload.' ),JSON_FORCE_OBJECT); ?>' />
	<label>
		<div>FAVICON Upload</div>
		<div id="fav_msg"></div>
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="273" style="max-width: 200px; overflow: hidden;">
					<input type="file" name="upload" />
				</td>
				<td width="14" rowspan="4" style="min-width: 300px; vertical-align: top;">
					<div id="favIconList" data-instructions='{"action":"ajax_load_favicons"}'></div>
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