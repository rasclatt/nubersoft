
<label><div>Timezone</div>
	<div class="form-input">
		<select name="content[timezone]">
			<?php $timezone	=	(!empty($settings['timezone']))? $settings['timezone']:"America/Los_Angeles"; ?>
			<option value="<?php echo $timezone; ?>"><?php echo ucwords(str_replace("_", " ", $timezone)); ?></option>
			<?php
				$all = timezone_identifiers_list();
				foreach($all as $zone) {
					if($zone != $settings['timezone']) { ?>
			<option value="<?php echo $zone; ?>"><?php echo ucwords(str_replace("_", " ", $zone)); ?></option>
			<?php	}
				} ?>
		</select>
	</div>
</label>