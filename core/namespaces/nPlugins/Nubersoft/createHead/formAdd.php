
			<div class="component_buttons_wrap">
				<form action="<?php echo (isset($_SERVER['HTTP_REFERER']))? $_SERVER['HTTP_REFERER']: ''; ?>" enctype="multipart/form-data" method="post">
					<input type="hidden" name="requestTable" value="<?php echo fetch_table_id('media',$this->nuber); ?>" />
					<input type="hidden" name="ID" value="" />
					<input type="hidden" name="unique_id" value="" />
					<input type="hidden" name="ref_page" value="<?php echo parent::$settings->page_prefs->unique_id; ?>" />
					<div class="add_button"><input disabled="disabled" type="submit" name="add" value="<?php echo strtoupper('add'); ?>" /></div>
				</form>
			</div>