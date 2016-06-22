<?php if(!isset($this->inputArray)) return; ?>
			<div class="component_buttons_wrap">
				<form action="<?php echo (isset($_SERVER['HTTP_REFERER']))? $_SERVER['HTTP_REFERER']: ''; ?>" enctype="multipart/form-data" method="post"> 
					<input type="hidden" name="requestTable"	value="<?php echo fetch_table_id($this->table,$this->nuber); ?>" />
					<input type="hidden" name="ID"				value="" />
					<input type="hidden" name="unique_id"		value="" />
					<input type="hidden" name="ref_page"		value="<?php echo $unique_id; ?>" />
					<input type="hidden" name="parent_id"		value="<?php echo (!empty($this->inputArray[0]['parent_id']))? $this->inputArray[0]['parent_id']: ''; ?>" /><?php
					if(isset($this->command) && !empty($this->command)) { ?>
					<input type="hidden" name="command"			value="<?php echo $this->command; ?>" /><?php } ?>
					<div class="add_button"> <input disabled="disabled" type="submit" name="add" value="<?php echo strtoupper('add'); ?>" /></div> 
				</form> 
			</div>