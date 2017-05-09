<?php if(empty($this->settings)) return; ?>
	<div class="nbr_form_input wrap">
		<?php if(!empty($this->settings['label'])) { ?><label><span class="nbr_sub_label"><?php echo $this->settings['label']; ?></span><?php } ?><?php if(!empty($this->settings['label']) && !$this->labelWrap) { ?></label><?php } ?>
			<div class="nbr_form_input_cont">
				<textarea name="<?php echo $this->settings['name']; ?>"<?php echo $this->settings['other'].$this->settings['id'].$this->settings['class'].$this->settings['style'].$this->settings['disabled'].$this->settings['placeholder']; ?>><?php echo $this->settings['value']; ?></textarea>
			</div>
		<?php if(!empty($this->settings['label']) && $this->labelWrap) { ?></label><?php } ?>
	</div>