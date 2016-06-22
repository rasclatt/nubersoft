<?php if(!isset($values)) return; ?>
	<div class="form-input">
		<?php if(isset($label) && $label) { ?><label><span class="label-hd"><?php echo $settings['label']; ?></span><?php } ?>
		<label><span style="font-size: 10px;">ON</span>
		<?php $active	=	(isset($values[$column]) && $values != false)? true:false; ?>
			<input type="checkbox" name="<?php echo $name; ?>" value="on" <?php if($active && $values[$column] == 'on') { ?> checked<?php } ?> />
		</label>
		<?php if(isset($label) && $label) { ?></label><?php } ?>
	</div>