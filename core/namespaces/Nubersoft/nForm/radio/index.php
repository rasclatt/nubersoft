<?php if(empty($this->settings)) return; ?>
	<div class="nbr_form_input wrap">
		<?php if(!empty($this->settings['label'])) { ?><label><span class="nbr_sub_label"><?php echo $this->settings['label']; ?></span><?php } ?><?php if(!empty($this->settings['label']) && !$this->labelWrap) { ?></label><?php } ?>
			<div class="nbr_form_input_cont<?php echo (!empty($this->settings['wrap_class']))? ' '.$this->settings['wrap_class'] : ''; ?>">
				<span<?php echo $this->settings['other'].$this->settings['id'].$this->settings['class'].$this->settings['style'].$this->settings['disabled']; ?>>
				
<?php	if(is_array($this->settings['options'])) {
			foreach($this->settings['options'] as $row) {
?>					<input type="radio" name="<?php echo $this->settings['name']; ?>" value="<?php if(isset($row[0])) echo $row[0]; ?>"<?php if(!empty($row[1])) echo ' checked'; ?> />
<?php		}
		}
?>				</span>
			</div>
		<?php if(!empty($this->settings['label']) && $this->labelWrap) { ?></label><?php } ?>
	</div>