<?php if(empty($this->settings)) return; ?>
<?php
	if(is_array($this->settings['value'])) 
		echo printpre($this->settings['value']);
?>
	<div class="nbr_form_input wrap">
		<div class="nbr_form_input_cont<?php echo (!empty($this->settings['wrap_class']))? ' '.$this->settings['wrap_class'] : ''; ?>">
			<input type="hidden" name="<?php echo $this->settings['name']; ?>" value="<?php echo $this->settings['value']; ?>"<?php echo $this->settings['other'].$this->settings['id'].$this->settings['class'].$this->settings['disabled']; ?> />
		</div>
	</div>