<?php
	if(!isset($this->inputArray))
		exit;
	$nProccessor	=	nApp::nToken()->setMultiToken('nProcessor','component');
	$payload		=	(isset($this->inputArray[0]))? $this->inputArray[0]:false; ?>
	
            <div style="width: 98%; padding: 1%; display: inline-block; min-width: 275px;"><?php $this->formAdd(); if($echoField) $this->formDelete();  $this->formTinyMCE(); $this->formHelpDesk(); $this->dup_component(); ?></div>        
			<div class="componentElement">
				<?php if($echoField) { ?>
				<form action="<?php echo (isset($_SERVER['HTTP_REFERER']))? $_SERVER['HTTP_REFERER']: ''; ?>" enctype="multipart/form-data" method="post">
					<input type="hidden" name="token[nProcessor]" value="<?php echo $nProccessor; ?>" />
					<input type="hidden" name="requestTable" value="<?php echo SetTable::Fetch($this->table); ?>" />
					<input type="hidden" name="ID" value="<?php if(!empty($payload['ID'])) echo $payload['ID']; ?>" />
					<input type="hidden" name="unique_id" value="<?php if(!empty($payload['unique_id'])) echo $payload['unique_id']; ?>" />
					<input type="hidden" name="ref_page" value="<?php echo (!empty($payload['ref_page']))? $payload['ref_page'] : $unique_id; ?>" />
					<input type="hidden" name="parent_id" value="<?php if(!empty($payload['parent_id'])) echo $payload['parent_id']; ?>" /><?php
					if(isset($this->command) && !empty($this->command)) { ?>
					<input type="hidden" name="command" value="<?php echo $this->command; ?>" /><?php }
					
					// Create the DIV dropdown Menu
					if(!empty($payload['component_type'])) {
							if($payload['component_type'] !== 'row') {
?>					<div style="display: inline-block; width: 100%;">
						<?php	$this->dropMenu(); // Create the DIV dropdown Menu ?>
					</div>
<?php							}
						}
					
					$this->createFormElements();
					
					// Create component guts ?>
					<div class="formButton"><input disabled="disabled" type="submit" name="<?php echo $function; ?>" value="<?php echo strtoupper($function); ?>" style="margin: 15px auto 0 auto;" /></div>
				</form>
				<?php }
				else { ?>
					Component Locked: <br />You must be a Superuser to Unlock.
				<?php } ?>
            </div>