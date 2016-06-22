<?php if(empty($this->settings)) return; ?>
	<div class="nbr_form_input wrap">
		<div class="nbr_form_input_cont">
			<input type="hidden" name="<?php echo $this->settings['name']; ?>" value="<?php echo $this->settings['value']; ?>"<?php echo $this->settings['other'].$this->settings['id'].$this->settings['class'].$this->settings['disabled']; ?> />
		</div>
	</div>