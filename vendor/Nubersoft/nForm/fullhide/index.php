<?php if(empty($this->nform_settings)) return; ?>
<?php
	if(is_array($this->nform_settings['value'])) 
		echo printpre($this->nform_settings['value']);
?>
	<div class="nbr_form_input wrap">
		<div class="nbr_form_input_cont<?php echo (!empty($this->nform_settings['wrap_class']))? ' '.$this->nform_settings['wrap_class'] : ''; ?>">
			<input type="hidden" name="<?php echo $this->nform_settings['name']; ?>" value="<?php echo $this->nform_settings['value']; ?>"<?php echo $this->nform_settings['other'].$this->nform_settings['id'].$this->nform_settings['class'].$this->nform_settings['disabled']; ?> />
		</div>
	</div>