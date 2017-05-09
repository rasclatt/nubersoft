	<?php 
	if(!function_exists('autoload_function'))
		return;
	
	autoload_function('nquery');
	$nubquery	=	nquery();
	
	if(!empty($dropdowns)) {
		if(isset($dropdowns[$name]))
			$options	=	$dropdowns[$name];
	}
	
	if(!isset($options))
		$options	=	$nubquery	->select(array("menuName","menuVal"))
									->from("dropdown_menus")
									->where(array("assoc_column"=>$name))
									->orderBy(array("page_order"=>"ASC"))
									->Record(__FILE__)
									->getResults();
									

?>
	<div class="form-input">
		<?php if(!empty($options)) { ?>
		<table cellpadding="0" cellspacing="0" border="0">
		<?php
			foreach($options as $option) {
				$OptName	=	$option['name'] ?>
			<tr>
				<td style="padding: 4px; background-color: transparent; vertical-align: middle; border: none; background: none;">
				<input type="radio" name="<?php echo $name; ?>" value="<?php echo $option['value']; ?>"<?php if(isset($values[$name]) && $values[$name] == $option['value']) { ?>checked<?php } ?> />
				</td>
				<td style="padding: 4px; background-color: transparent; vertical-align: middle; border: none; background: none;">
					<label style="font-family: Arial, Helvetica, sans-serif; font-size: 10px;"><?php echo $OptName; ?></label>
				</td>
			</tr>
			<?php } ?>
		</table>
		<?php } ?>
	</div>