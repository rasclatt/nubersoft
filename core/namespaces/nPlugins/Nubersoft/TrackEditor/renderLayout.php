<?php
if($this->getEditStatus() && ($component_type == 'row')) {
?>
<div class="point_editors">
	<?php $this->ComponentEditors($this->content[key($this->payload)]); ?>
</div>
<?php
}
# Loop through the tree array
foreach($this->hiarchy_content as $keys => $values) {
	# This is the content array+unique_id value 
	$info	=	$this->render_content[$keys];
	$is_row	=	$is_div	=	false;
	if(isset($info['component_type'])) {
		$is_row	=	($info['component_type'] == 'row');
		$is_div	=	($info['component_type'] == 'div');
	}

	if(get_edit_status() && !$is_row && !$is_div) {
?>
<div class="point_editors">
	<?php $this->componentEditors($info); ?>
</div>
<?php
	}
			
	if($is_row !== true && $is_div !== true) {
		if($info['page_live'] == 'on')
			echo $this->elementId($info,$this->hiarchy_content,'cont');
	}
	
	if($is_row) {
	?>
	<div<?php echo implode(' ',$this->elementId($this->render_content,$this->hiarchy_content,'row')); ?>>
	<?php
	}
	
	if($is_div) {
	?>
		<div<?php echo implode(' ',$this->elementId($info,$this->hiarchy_content,'div')); ?>>
	<?php
	}
	
	if($this->getEditStatus() && $is_div) {
	?>
			<div class="div_wrap_editors nbr_ux_element">
				<div class="point_editors">
					<?php $this->ComponentEditors($info); ?>
				</div>
	<?php
	}

	# If value is an array, loop back through this method
	if(is_array($values)) {
		$this->track($values,$this->render_content);
	}
	
	if($this->getEditStatus() && $is_div) {
	?>
			</div>
	<?php
	}
   
	if($is_row) {
	?>
		</div>
	<?php
	}
	
	if($is_div) {
	?>
	</div>
	<?php
	}
}