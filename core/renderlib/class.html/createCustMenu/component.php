<?php
// Component for side bar tool box slider
// Fetch an nProcessor token
$nProccessor	=	nApp::nToken()->setMultiToken('nProcessor','component');
// Set it (incase it's not set)
nApp::saveSetting('nProcessor',$nProccessor);
?>          <div style="width: 98%; padding: 1%; display: inline-block;"><?php $this->formDelete(); $this->formHelpDesk(); ?></div>
			<div class="componentElement">
				<form action="<?php echo (isset($_SERVER['HTTP_REFERER']))? $_SERVER['HTTP_REFERER']: ''; ?>" enctype="multipart/form-data" method="post">
					<input type="hidden" name="requestTable" value="<?php echo $this->table; ?>" />
					<input type="hidden" name="token[nProcessor]" value="<?php echo $nProccessor; ?>" />
					<input type="hidden" name="ID" value="<?php if(isset($this->inputArray[0]['ID'])) echo $this->inputArray[0]['ID']; ?>" />
					<input type="hidden" name="override" value="1" />
					<input type="hidden" name="unique_id" value="<?php if(isset($this->inputArray[0]['unique_id'])) echo $this->inputArray[0]['unique_id']; ?>" />
					<input type="hidden" name="parent_id" value="<?php if(isset($this->inputArray[0]['parent_id'])) echo $this->inputArray[0]['parent_id']; ?>" />
					<input type="hidden" name="ref_page" value="<?php if(isset($this->inputArray[0]['ref_page'])) echo $this->inputArray[0]['ref_page']; else echo $unique_id; ?>" /></p>
<?php				if(isset($this->command) && !empty($this->command)) {
?>
					<input type="hidden" name="command" value="<?php echo $this->command; ?>" />
<?php 					}
?>					<div style="display: inline-block; width: 100%;">
						<?php	$this->dropMenu(); // Create the DIV dropdown Menu ?>
					</div>
<?php				// Create component guts
					$this->createFormElements();
?>
					<div class="nbr_button nbr_button_mod"><input disabled="disabled" type="submit" name="<?php echo $function; ?>" value="<?php echo strtoupper($function); ?>" style="margin: 15px auto 0 auto;" /></div>
				</form>
            </div>