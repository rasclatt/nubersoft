<?php
$this->autoload('get_edit_status');
$this->payload	=	$payload;
$this->content	=	$content;
$component		=	new create();
# Create a style string for subsequent style="" if set
$this->setStyle();
$row_wrap		=	($this->content[key($this->payload)]['component_type'] == 'row');
# If the component is a row, then add the default two container divs ?>
<?php if($row_wrap): ?>

<div<?php echo $this->style_arr['style']; ?> class="<?php echo $this->style_arr['class']; ?>">
	<div class="graybar_1_content">
		<?php endif ?>
		
		<?php
		$divSet		=	(is_array($this->payload));

		if(get_edit_status()):
			# Track editor
			$this->trackEditorRender($this->content, $this->payload);
		endif;
		?>

		<?php if($row_wrap): ?>
	</div>
</div>
<?php endif ?>