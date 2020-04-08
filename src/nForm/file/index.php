<?php if(empty($this->nform_settings)) return; ?>
    <div class="nbr_form_input wrap">
        <?php if(!empty($this->nform_settings['label'])) { ?><label><span class="nbr_sub_label"><?php echo $this->nform_settings['label']; ?></span><?php } ?><?php if(!empty($this->nform_settings['label']) && !$this->labelWrap) { ?></label><?php } ?>
            <div class="nbr_form_input_cont<?php echo (!empty($this->nform_settings['wrap_class']))? ' '.$this->nform_settings['wrap_class'] : ''; ?>">
                <input type="file" name="<?php echo $this->nform_settings['name']; ?>[]"<?php echo $this->nform_settings['other'].$this->nform_settings['id'].$this->nform_settings['class'].$this->nform_settings['style'].$this->nform_settings['disabled']; ?> />
            </div>
        <?php if(!empty($this->nform_settings['label']) && $this->labelWrap) { ?></label><?php } ?>
    </div>