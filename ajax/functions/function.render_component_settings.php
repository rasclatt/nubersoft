<?php
function render_component_settings($comp_settings = false)
	{
		$compname	=	(!empty($comp_settings['title']))? $comp_settings['title'] : 'Title';
		$compvals	=	(!empty($comp_settings['options']))? $comp_settings['options'] : false;
		$settings	=	(!empty($comp_settings['settings']))? $comp_settings['settings'] : false;
		$values		=	(!empty($comp_settings['values']))? $comp_settings['values'] : false;
		$options	=	(!empty($comp_settings['options']))? $comp_settings['options'] : false;
		$table		=	(!empty($comp_settings['table']))? $comp_settings['table'] : "menu_display";
		
		ob_start();		
?>				<div class="toolsheaders nbrAccordion"><?php echo ucwords($compname); ?></div>
			<div class="nbr_tools_headers_panels">
				<div style="padding: 10px;">
					<div class="nbr_tool_component_contain">
<?php						$compCnt	=	count($compvals);
							for($i = 0; $i < $compCnt; $i++) {
								$comKey						=	$compvals[$i]['component_value'];
								$useSettings['name']		=	$comKey;
		
								if(strpos($comKey,"[") !== false) {
										$revKey	=	explode("[",$comKey);
										$comKey	=	array_shift($revKey);
									}
									
								$colName					=	(!empty($settings['format'][$comKey]['column_name']))? $settings['format'][$comKey]['column_name'] : false;
								$useSettings['type']		=	(!empty($settings['format'][$comKey]['column_type']))? $settings['format'][$comKey]['column_type'] : false;
								$useSettings['dropdowns']	=	(!empty($options['options'][$comKey]))? $options['options'][$comKey]:false;
								$useSettings['values']		=	(!empty($values))? $values : false;
								
								if($useSettings['type'] == 'textarea')
									$useSettings['class']	=	'textarea';
								
								$useSettings['label']	 		=	(empty($colName))? ucwords(str_replace("_"," ",$comKey)) : ucwords(str_replace("_"," ",$colName));
								$useSettings['placeholder']	=	$useSettings['label'];
								
								if($useSettings['type'] == 'textarea') {
									
?>
					<div style="font-size:10px;" class="ajaxtrigger" data-gopage="edit.component" data-gopagekind="g" data-gopagesend="requestTable=<?php echo Safe::bcrypt_encode($table); ?>&ID=<?php if(isset($values['ID'])) echo $values['ID']; ?>">WYSIWYG</div>
<?php 									}
?>
				<?php echo form_field($useSettings).PHP_EOL; ?>

<?php							}
?>						</div>
				</div>
			</div>
<?php
		$data	=	ob_get_contents();
		ob_end_clean();
		
		return $data;
	}