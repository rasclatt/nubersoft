<?php
	if(!isset($this->inputArray))
		return; 
	
	if(is_array($value)) { ?>
										
	<form action="<?php echo (isset($_SERVER['HTTP_REFERER']))? $_SERVER['HTTP_REFERER']: ''; ?>" enctype="application/x-www-form-urlencoded" method="post">
		<input type="hidden" name="requestTable" value="<?php echo fetch_table_id('components'); ?>" />
		<input type="hidden" name="ref_page" value="<?php echo $unique_id; ?>" />
		<?php if($this->inputArray[0]['component_type'] !== 'row' || !empty($this->inputArray[0]['component_type'])) { ?><input type="hidden" name="parent_id" value="<?php echo $this->inputArray[0]['unique_id']; ?>" /><?php } ?>
		<?php $this->driveDownArr($value); ?>              
		<input type="hidden" name="ID"/>
		<input type="hidden" name="unique_id"/>
		<div class="dup_guts_container">
			<p style="float:left; color: #333333; text-shadow: 1px 1px 3px #FFFFFF;">
			<img src="/core_images/core/<?php echo $icon_arr[$this->curr['component_type']]; ?>" style="width: 20px;" />
				<?php echo substr($this->curr['component_name'], 0, 20); echo (strlen($this->curr['component_name']) > 20)? '...':''; ?></p>
			<div class="formButton"><input id="controller<?php echo $this->curr['ID']; ?>" type="submit" name="add" value="+" style="width:auto; float: right; font-size: 14px; padding: 0 4px; margin: 0; line-height: 18px;" /></div>
		</div>
	</form>
<?php
										}
									else {
										if(!empty($value)) { ?>

		<input type="hidden" name="<?php echo $key; ?>" value="<?php echo Safe::decode($value); ?>" />
										<?php }
										}