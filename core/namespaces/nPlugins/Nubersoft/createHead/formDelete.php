
			<div class="component_buttons_wrap">
				<form action="/ajax/confirm.php" enctype="application/x-www-form-urlencoded" method="post">
					<input type="hidden" name="requestTable" value="<?php echo fetch_table_id('media',$this->nuber); ?>" />
					<input type="hidden" name="ID" value="<?php if(isset($this->inputArray[0]['ID'])) echo $this->inputArray[0]['ID']; ?>" />
					<input type="hidden" name="unique_id" value="<?php if(isset($this->inputArray[0]['unique_id'])) echo $this->inputArray[0]['unique_id']; ?>" />
					<input type="hidden" name="delete" value="on" />
					<div class="delete_button"><input disabled="disabled" type="submit" name="update" value="<?php echo strtoupper('delete'); ?>" /></div>
				</form>
            </div>