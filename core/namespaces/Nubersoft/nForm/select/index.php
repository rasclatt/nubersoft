<?php
if(empty($this->settings))
	return;
	
$hasSelect	=	(isset($this->settings['select']));
$defSelect	=	($hasSelect)? $this->settings['select'] : false;
// The options array must have value at the 0 key and title at the 1 key


?>
	<div class="nbr_form_input wrap">
		<?php if(!empty($this->settings['label'])) { ?><label><span class="nbr_sub_label"><?php echo $this->settings['label']; ?></span><?php } ?><?php if(!empty($this->settings['label']) && !$this->labelWrap) { ?></label><?php } ?>
			<div class="nbr_form_input_cont<?php if(!empty($this->settings['wrap_class'])) echo ' '.$this->settings['wrap_class']; ?>">
				<select name="<?php echo $this->settings['name']; ?>"<?php echo $this->settings['other'].$this->settings['id'].$this->settings['class'].$this->settings['style'].$this->settings['disabled']; ?>>
<?php	if(is_array($this->settings['options'])) {

			foreach($this->settings['options'] as $key => $row) {
				$OptValue	=	(!isset($row['value']))? $key : ((isset($row['value']))? $row['value'] : '');
				$OptName	=	(!isset($row['name']))? $row : ((isset($row['name']))? $row['name'] : '');
				$selected	=	'';
				if(!empty($row['selected']))
					$selected	=	' selected';
				else {
					if($hasSelect) {
						$selected	=	($OptValue == $defSelect)? ' selected' : '';
					}
				}
				
?>					<option value="<?php echo $OptValue ?>"<?php echo $selected ?>><?php echo $OptName ?></option>
<?php		}
		}
?>				</select>
			</div>
		<?php if(!empty($this->settings['label']) && $this->labelWrap) { ?></label><?php } ?>
	</div>