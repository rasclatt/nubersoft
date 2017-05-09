<?php if(empty($this->settings)) return; ?>
<div class="nbr_form_text_wrap wrap<?php echo (!empty($this->settings['wrap_class']))? ' '.$this->settings['wrap_class'] : ''; ?>">
	<div<?php echo $this->settings['style'].$this->settings['id'].$this->settings['class'].$this->settings['other']; ?>>
		<?php echo $this->settings['value']; ?>
	</div>
</div>