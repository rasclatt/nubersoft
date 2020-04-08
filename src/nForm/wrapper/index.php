<?php if(empty($this->nform_settings)) return; ?>
<div class="nbr_form_text_wrap wrap<?php echo (!empty($this->nform_settings['wrap_class']))? ' '.$this->nform_settings['wrap_class'] : ''; ?>">
    <div<?php echo $this->nform_settings['style'].$this->nform_settings['id'].$this->nform_settings['class'].$this->nform_settings['other']; ?>>
        <?php echo $this->nform_settings['value']; ?>
    </div>
</div>