
            <div style="width: 98%; padding: 1%; display: inline-block;"><?php $this->formDelete(); $this->formHelpDesk(); ?></div>  
			<div class="componentElement">
				<form action="<?php echo (isset($_SERVER['HTTP_REFERER']))? $_SERVER['HTTP_REFERER']: ''; ?>" enctype="multipart/form-data" method="post">
					<input type="hidden" name="requestTable"	value="<?php echo fetch_table_id('media',$this->nuber); ?>" />
					<input type="hidden" name="ID"				value="<?php if(isset($this->inputArray[0]['ID'])) echo $this->inputArray[0]['ID']; ?>" />
					<input type="hidden" name="unique_id"		value="<?php if(isset($this->inputArray[0]['unique_id'])) echo $this->inputArray[0]['unique_id']; ?>" />
					<input type="hidden" name="ref_page"		value="<?php echo (isset($this->inputArray[0]['ref_page']) && !empty($this->inputArray[0]['ref_page']))? $this->inputArray[0]['ref_page']: $unique_id; ?>" /><?php
					
					$this->createFormElements();
					
					// Create component guts ?>
					<div class="formButton"><input disabled="disabled" type="submit" name="<?php echo $function; ?>" value="<?php echo strtoupper($function); ?>" style="margin: 15px auto 0 auto;" /></div>
				</form>
            </div>