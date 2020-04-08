<?php if(empty($this->nform_settings)) return; ?>
    <div class="nbr_form_input wrap">
        <?php if(!empty($this->nform_settings['label'])) { ?><label><span class="nbr_sub_label"><?php echo $this->nform_settings['label']; ?></span><?php } ?><?php if(!empty($this->nform_settings['label']) && !$this->labelWrap) { ?></label><?php } ?>
            <div class="nbr_form_input_cont<?php echo (!empty($this->nform_settings['wrap_class']))? ' '.$this->nform_settings['wrap_class'] : ''; ?>">
                <span<?php echo $this->nform_settings['other'].$this->nform_settings['id'].$this->nform_settings['class'].$this->nform_settings['style'].$this->nform_settings['disabled']; ?>>
                
<?php    if(is_array($this->nform_settings['options'])) {
            foreach($this->nform_settings['options'] as $row) {
?>                    <input type="radio" name="<?php echo $this->nform_settings['name']; ?>" value="<?php if(isset($row[0])) echo $row[0]; ?>"<?php if(!empty($row[1])) echo ' checked'; ?> />
<?php        }
        }
?>                </span>
            </div>
        <?php if(!empty($this->nform_settings['label']) && $this->labelWrap) { ?></label><?php } ?>
    </div>