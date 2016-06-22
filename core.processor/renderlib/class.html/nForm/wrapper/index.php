<?php if(empty($this->settings)) return; ?>
<div class="nbr_form_text_wrap wrap">
	<div<?php echo $this->settings['style'].$this->settings['id'].$this->settings['class'].$this->settings['other']; ?>>
		<?php echo $this->settings['value']; ?>
	</div>
</div>