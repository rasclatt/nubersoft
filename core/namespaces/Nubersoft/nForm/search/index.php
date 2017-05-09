<?php if(empty($this->settings)) return;?>
	<div class="nbr_form_input wrap">
		<?php if(!empty($this->settings['label'])) { ?><label><span class="nbr_sub_label"><?php echo $this->settings['label']; ?></span><?php } ?><?php if(!empty($this->settings['label']) && !$this->labelWrap) { ?></label><?php } ?>
			<div class="nbr_form_input_cont<?php echo (!empty($this->settings['wrap_class']))? ' '.$this->settings['wrap_class'] : ''; ?>">
				<input type="search" name="<?php echo $this->settings['name']; ?>" value="<?php echo $this->settings['value']; ?>"<?php echo $this->settings['selected'].$this->settings['other'].$this->settings['id'].$this->settings['class'].$this->settings['style'].$this->settings['size'].$this->settings['disabled'].$this->settings['placeholder']; ?> />
			</div>
		<?php if(!empty($this->settings['label']) && $this->labelWrap) { ?></label><?php } ?>
	</div>