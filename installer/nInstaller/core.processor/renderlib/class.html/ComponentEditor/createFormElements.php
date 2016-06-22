<?php
	if(!function_exists("is_admin"))
		return;
	
	if(!is_admin())
		return;
?>				<div class="toolsheaders nbrAccordion"><?php echo ucwords($compname); ?></div>
				<div class="nbr_tools_headers_panels">
					<div style="padding: 10px;">
						<div class="nbr_tool_component_contain">
<?php						$sArray	=	function($array,$k1,$k2,$k3) {
								return (!empty($array[$k1][$k2][$k3]))? $array[$k1][$k2][$k3] : false;
							};
							
							$cValsCnt	=	count($compvals);
							for($i = 0; $i < $cValsCnt; $i++) {
								$comKey						=	$compvals[$i]['component_value'];
								$useSettings['name']		=	$comKey;
		
								if(strpos($comKey,"[") !== false) {
									$revKey	=	explode("[",$comKey);
									$comKey	=	array_shift($revKey);
								}
								
								// Try to cut down on same algorythms
								$formatCname				=	$sArray($settings,'format',$comKey,'column_name');
								$formatCtype				=	$sArray($settings,'format',$comKey,'column_type');
								
								$colName					=	$formatCname;
								$useSettings['type']		=	$formatCtype;
								$useSettings['dropdowns']	=	(!empty($options['options'][$comKey]))? $options['options'][$comKey]:false;
								$useSettings['values']		=	(!empty($values))? $values : false;
								
								if($useSettings['type'] == 'textarea')
									$useSettings['class']	=	'textarea';
								
								$useSettings['label']	 	=	(empty($colName))? ucwords(str_replace("_"," ",$comKey)) : ucwords(str_replace("_"," ",$colName));
								$useSettings['placeholder']	=	$useSettings['label'];
								
								if($useSettings['type'] == 'textarea') {
										
?>
						<div style="font-size:10px; color: #FFF;" class="ajaxtrigger" data-gopage="edit.component" data-gopagekind="g" data-gopagesend="requestTable=<?php echo Safe::bcrypt_encode($this->table); ?>&ID=<?php if(isset($this->data['ID'])) echo $this->data['ID']; ?>">WYSIWYG</div>
<?php 							}

								echo form_field($useSettings).PHP_EOL;
							}
?>						</div>
					</div>
				</div>