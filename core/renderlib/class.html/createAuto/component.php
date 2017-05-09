
            <div style="width: 98%; padding: 1%; display: inline-block;"><?php $this->formAdd(); if($echoField == true) $this->formDelete();  $this->formTinyMCE(); $this->formHelpDesk(); ?></div>
			<div class="componentElement"><?php if($echoField == true) { ?>
				<form action="<?php echo (isset($_SERVER['HTTP_REFERER']))? $_SERVER['HTTP_REFERER']: ''; ?>" enctype="multipart/form-data" method="post">
					<input type="hidden" name="requestTable" value="<?php echo fetch_table_id($this->table,$this->nuber); ?>" />
					<input type="hidden" name="override" value="1" />
					<input type="hidden" name="ID" value="<?php if(isset($this->inputArray[0]['ID'])) echo $this->inputArray[0]['ID']; ?>" />
					<input type="hidden" name="unique_id" value="<?php if(isset($this->inputArray[0]['unique_id'])) echo $this->inputArray[0]['unique_id']; ?>" />
					<div style="display: inline-block; width: 100%;">
						<?php	$this->dropMenu(); // Create the DIV dropdown Menu ?>
					</div>
<?php				if(isset($this->command) && !empty($this->command)) { ?>
					<input type="hidden" name="command" value="<?php echo $this->command; ?>" /><?php }
					
					$this->createFormElements();
					
					if(isset($_POST['ref_page']) || (isset($this->inputArray[0]['ref_page']) && !empty($this->inputArray[0]['ref_page']))) { ?>
					<input type="hidden" name="ref_page" value="<?php if(isset($_POST['ref_page'])) echo $_POST['ref_page']; ?>" /><?php } ?>
					<div class="formButton"><input disabled="disabled" type="submit" name="<?php echo $function; ?>" value="<?php echo strtoupper($function); ?>" style="margin: 15px auto 0 auto;" /></div>
				</form><?php }
				else
					echo 'Component Locked: <br />You must be a Superuser to Unlock.'; ?>
            </div>