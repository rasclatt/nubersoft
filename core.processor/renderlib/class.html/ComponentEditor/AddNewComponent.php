<?php
	if(!function_exists("is_admin"))
		return;
	
	if(!is_admin())
		return;
	
	$nProccessor	=	nApp::nToken()->setMultiToken('nProcessor','component');
?>		<div class="component_buttons_wrap">
			<form action="<?php echo (isset($_SERVER['HTTP_REFERER']))? $_SERVER['HTTP_REFERER']: ''; ?>" enctype="multipart/form-data" method="post">
				<input type="hidden" name="token[nProcessor]" value="<?php echo $nProccessor; ?>" />
				<input type="hidden" name="requestTable" value="<?php echo $this->table; ?>" />
				<input type="hidden" name="ID" />
				<input type="hidden" name="unique_id" />
				<?php
				if(!empty($this->page_id))
					$pageid	=	$this->page_id;
				elseif(!empty($this->ref_page))
					$pageid	=	$this->ref_page;
				
				// If current element is a div, allow for nesting
				if(isset($this->data['component_type']) && ($this->data['component_type'] == 'div' || $this->data['component_type'] == 'row')) { ?>
				<input type="hidden" name="parent_id"		value="<?php if(!empty($this->data['unique_id'])) echo $this->data['unique_id'] ?>" />
				<?php } ?>
				<input type="hidden" name="ref_page" value="<?php echo (isset($this->data['ref_page']))? $this->data['ref_page']: $pageid; ?>" />
				<?php
				if(!empty($this->command)) { ?>
				<input type="hidden" name="command" value="<?php echo $this->command; ?>" /><?php } ?>
				<div class="nbr_component_add">
					<label>
						<input disabled="disabled" style="display: none;" type="submit" name="add" value="ADD" />
					</label>
				</div>
			</form>
		</div>