<?php
if(empty($this->nform_settings))
	return;
	
$hasSelect	=	(isset($this->nform_settings['select']));
$defSelect	=	($hasSelect)? $this->nform_settings['select'] : false;
# The options array must have value at the 0 key and title at the 1 key
$required	=	(!empty($this->nform_settings['other']) && (stripos($this->nform_settings['other'], 'required') !== false));
?>
	<div class="nbr_form_input wrap">
		<?php if(!empty($this->nform_settings['label'])) { ?><label<?php if($required) echo ' class="required"' ?>><span class="nbr_sub_label"><?php echo $this->nform_settings['label']; ?></span><?php } ?><?php if(!empty($this->nform_settings['label']) && !$this->labelWrap) { ?></label><?php } ?>
			<div class="nbr_form_input_cont<?php if(!empty($this->nform_settings['wrap_class'])) echo ' '.$this->nform_settings['wrap_class']; ?>">
				<select name="<?php echo $this->nform_settings['name']; ?>"<?php echo $this->nform_settings['other'].$this->nform_settings['id'].$this->nform_settings['class'].$this->nform_settings['style'].$this->nform_settings['disabled']; ?>>
<?php	if(is_array($this->nform_settings['options'])) {

			foreach($this->nform_settings['options'] as $key => $row) {
				$OptValue	=	(!isset($row['value']))? $key : ((isset($row['value']))? $row['value'] : '');
				$OptName	=	(!isset($row['name']))? $row : ((isset($row['name']))? $row['name'] : '');
				$isDisabled	=	(!empty($row['disabled']))? ' disabled' : '';
				$selected	=	'';
				if(!empty($row['selected']))
					$selected	=	' selected';
				else {
					if($hasSelect) {
						$selected	=	($OptValue == $defSelect)? ' selected' : '';
					}
				}
				
?>					<option value="<?php echo $OptValue ?>"<?php echo $selected.$isDisabled ?>><?php echo $OptName ?></option>
<?php		}
		}
?>				</select>
			</div>
		<?php if(!empty($this->nform_settings['label']) && $this->labelWrap) { ?></label><?php } ?>
	</div>