	<?php 
	if(!function_exists('AutoloadFunction'))
		return;
	
	AutoloadFunction('nQuery');
	$nubquery	=	nQuery();
	
	if(isset($dropdowns) && !empty($dropdowns)) {
			
			if(isset($dropdowns[$name]))
				$options	=	$dropdowns[$name];
		}
	
	if(!isset($options))
		$options	=	$nubquery	->select(array("menuName","menuVal"))
									->from("dropdown_menus")
									->where(array("assoc_column"=>$name))
									->orderBy(array("page_order"=>"ASC"))
									->Record(__FILE__)
									->fetch(); ?>
	<div class="form-input">
		
			<?php if(!empty($options)) { ?>
			<table cellpadding="0" cellspacing="0" border="0">
			<?php
				foreach($options as $dropdown) { ?>
				<tr>
					<td style="padding: 4px; background-color: transparent; vertical-align: middle; border: none; background: none;">
					<input type="radio" name="<?php echo $name; ?>" value="<?php echo $dropdown['menuVal']; ?>"<?php if(isset($values[$name]) && $values[$name] == $dropdown['menuVal']) { ?>checked<?php } ?> />
					</td>
					<td style="padding: 4px; background-color: transparent; vertical-align: middle; border: none; background: none;">
						<label style="font-family: Arial, Helvetica, sans-serif; font-size: 10px;"><?php echo $dropdown['menuName']; ?></label>
					</td>
				</tr>
				<?php } ?>
			</table>
			<?php } ?>
	</div>