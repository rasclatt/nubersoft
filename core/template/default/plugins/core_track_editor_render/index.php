<?php
$this->hiarchy_content	=	$hiarchy_content;
$this->render_content	=	$render_content;
$divSet					=	(is_array($this->hiarchy_content))? true: false;

if($divSet): ?>
<div class="track_editor">
<?php endif ?>
	<?php foreach($this->hiarchy_content as $keys => $values): ?>
		<?php
		# This is the content array+unique_id value 
		$info	=	$this->render_content[$keys];
		# Start an instance of a render
		$rendElem		=	new RenderPageElements;
		# Styles for this container component if not row
		$rendElem->renderContentElements($info, $this->GetCSSList());
		$is_div	=	(isset($info['component_type']) && $info['component_type'] == 'div');
	
		if($is_div): ?>
	
	<div <?php $styles = $this->CreateStyles($info,$this->GetCSSList()); if(!empty($styles)) echo 'style="'.$styles.'"'; ?>>
		<div class="track_editor_nested" <?php if(isset($info['admin_tag']) && !empty($info['admin_tag'])) { ?>style="background-color: <?php echo $info['admin_tag']; ?>; background-image: url(/images/core/window_grad.png); background-repeat: repeat-x;"<?php } ?>>
		<?php  endif ?>
			
			<?php
			if(get_edit_status()) 
				$this->componentEditors($info);

			# If value is an array, loop back through this method
			if(is_array($values)) {
				$this->trackView($values,$this->render_content);
			}

			if($is_div): ?>

		</div>
	</div>
		<?php endif ?>
	<?php endforeach ?>
<?php if($divSet): ?>
</div>
<?php endif ?>