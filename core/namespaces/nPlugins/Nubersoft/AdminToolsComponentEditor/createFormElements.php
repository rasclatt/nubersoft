<?php
$sArray	=	function($array,$k1,$k2,$k3) {
	return (!empty($array[$k1][$k2][$k3]))? $array[$k1][$k2][$k3] : false;
};
$cValsCnt	=	count($compvals);

$nestGroup	=	($compname != '~NULL~');

if($nestGroup) {
?>
		<div class="toolsheaders nTrigger" data-instructions='{"FX":{"acton":[".nbr_tools_headers_panels","next::accordian"],"fx":["slideUp","slideToggle"],"fxspeed":["fast","fast"]}}'><?php echo ucwords($compname); ?></div>
<?php
}
?>
			<div class="nbr_tools_headers_panels"<?php if(!$nestGroup) { ?> style="display: block;"<?php } ?>>
				<div style="padding: 10px;">
					<div class="nbr_tool_component_contain">
						<?php
						for($i = 0; $i < $cValsCnt; $i++) {
							$comKey					=	$compvals[$i]['component_value'];
							$useSettings['name']	=	$comKey;
							# Checks for an array value
							if(strpos($comKey,"[") !== false) {
								# Explode on the array
								$revKey	=	explode("[",$comKey);
								# Remove first value, presumably the key
								$comKey	=	array_shift($revKey);
								# Loop through the rest nd remove the end key
								foreach($revKey as $cName)
									$col[]	=	trim($cName,']');
								# Trim the array indicators from the array keys
								$arrayMatch	=	array_map(function($v) {
									return str_replace(array('[',']'),'',$v);
								},$revKey);
								# Check if there are values associated with array
								if(isset($values[$comKey])) {
									# Convert them to an array if not an array yet
									if(!empty($values[$comKey]) && is_string($values[$comKey]))
										$values[$comKey]	=	json_decode($this->safe()->decode($values[$comKey]),true);
								}
							}
							$fetchType	=	$this->getMatchedArray(array($comKey,'column_type'),'',$array);
							$fetchSize	=	$this->getMatchedArray(array($comKey,'size'),'',$array);
							$type		=	(!empty($fetchType))? $fetchType['column_type'][0] : 'text';
							$size		=	(!empty($fetchSize))? $fetchSize['size'][0] : false;
							
							if(!empty($col)) {
								$colName	=	 implode('/',$col);
								unset($col);
							}
							else
								$colName	=	$comKey;
							
							$useSettings['type']		=	$type;
							$useSettings['options']		=	(!empty($options))? $options:false;
							$useSettings['value']		=	(!empty($values))? $values : false;
							if(!empty($size))
								$useSettings['style']	=	$size;
							
							if($type == 'textarea')
								$useSettings['class']	=	'textarea';
							
							$useSettings['label']	 	=	(empty($colName))? ucwords(str_replace("_"," ",$comKey)) : ucwords(str_replace("_"," ",$colName));
							$useSettings['placeholder']	=	$useSettings['label'];
							
						//	if(stripos($useSettings['name'],'page_options') !== false)
						//		echo printpre($useSettings);
							
							if($type == 'textarea' && isset($this->data['ID'])) {		
?>
					<div class="nTrigger" onmouseover="this.style.color='red'; this.style.cursor='pointer'" onmouseout="this.style.color='#FFF'; this.style.cursor='default'" data-instructions='{"FX":{"acton":["#comp_settings_cont<?php echo $this->data['unique_id'] ?>"],"fx":["fadeOut"]}}' style="font-size:10px; color: #FFF;" onClick="window.open('<?php echo $this->siteUrl('?action=nbr_load_single_editor&cId='.$this->data['ID']) ?>','_blank')">EDIT HTML (FULL PAGE)</div>
<?php 						}

							echo $this->getHelper('nForm')->multiValueDeterminer($useSettings).PHP_EOL;
						}
?>
					</div>
				</div>
			</div>