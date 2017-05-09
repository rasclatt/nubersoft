<?php
	class	TrackEditor
		{
			public		$payload;
			public		$content;
			public		$render_content;
			public		$hiarchy_content;
			public		$style_arr;
			public		$element;
			public		$classifications;
			public		$element_style;
			public		$CCS_List;
			
			protected	$nuber;
			
			private		$root_folder;
			
			public	function __construct($root_folder)
				{
					$this->root_folder	=	$root_folder;
				}
			
			public	function DefaultCSS()
				{ 
				}
			
			public	function Track($payload = '',$content = '')
				{
					AutoloadFunction("get_edit_status");
					$component		=	new create();
					$row_wrap		=	(isset($content[key($payload)]['component_type']) && in_array($content[key($payload)]['component_type'],array('div','row')))? true: false;
					$_div_element	=	$this->ElementId($content,$payload,$row_wrap);
					
					// If the component is a row, then add the default two container divs
					if($row_wrap) {
?>				<div<?php echo $_div_element['id'].$_div_element['style'].$_div_element['class']; ?>>
<?php 				}
					
					if(get_edit_status()) {
						if(isset($_SESSION['toggle']['edit']['type']) && $_SESSION['toggle']['edit']['type'] == 'live')
							$this->RenderLayout($content, $payload);
						else
							$this->TrackEditorRender($content, $payload);
					}
				//	else
				//		$this->PageRender($content, $payload);
					
					if($row_wrap) {
?>					</div>
<?php 				}
				}
			
			public	function TrackEditorRender($render_content, $hiarchy_content)
				{
					AutoloadFunction("allow_if");
					$this->hiarchy_content	=	$hiarchy_content;
					$this->render_content	=	$render_content;
					$divSet					=	(is_array($this->hiarchy_content));
					
					if($divSet) {
?>					<div class="track_editor">
<?php				}
						
					// Loop through the tree array
					foreach($this->hiarchy_content as $keys => $values) {
						// This is the content array+unique_id value 
						$info		=	$this->render_content[$keys];
						// Start an instance of a render
						$rendElem	=	new renderElements();
						// Styles for this container component if not row
						$rendElem->renderContentElements($info, $this->GetCSSList());
						
						$is_div	=	(check_empty($info,'component_type','div'));
						if($is_div) {
?>						<div <?php $styles = $this->CreateStyles($info,$this->GetCSSList()); if(!empty($styles)) echo 'style="'.$styles.'"'; ?>>
							<div class="track_editor_nested" <?php if(!empty($info['admin_tag'])) { ?>style="background-color: <?php echo $info['admin_tag']; ?>;"<?php } ?>>
<?php 					} 

						if(get_edit_status()) 
							core::ComponentEditors($info);
										
						// If value is an array, loop back through this method
						if(is_array($values))
							$this->Track($values,$this->render_content);
							
						if($is_div) {
?>							</div>
						</div>
<?php					}
					}

					if($divSet){
?>					</div>
<?php				}
				}
			
			public	function PageRender($render_content, $hiarchy_content)
				{
					$this->hiarchy_content	=	$hiarchy_content;
					$this->render_content	=	$render_content;
					// Check if div
					$_div					=	array('row','div');
					// Start an instance of a render
					$rendElem				=	new renderElements;
					// Loop through the tree array
					foreach($this->hiarchy_content as $keys => $values) {
						// This is the content array+unique_id value 
						$info	=	$this->render_content[$keys];
						// Check permissions for this component
						$perms	=	Inclusion::Check($info);
						if($perms) {
							//	if(!in_array($info['component_type'],$_div)) 
							//		echo $this->ElementId($info,$this->hiarchy_content,'cont');
									
							if(!in_array($info['component_type'],$_div)) {
?>							<div<?php echo implode('',$this->ElementId($info,$this->hiarchy_content,'div')); ?>>
<?php						}

							// If value is an array, loop back through this method
							if(is_array($values)) 
									$this->Track($values,$this->render_content);
								
							if(!in_array($info['component_type'],$_div)) {
?>							</div>
<?php						}
						}
					}
				}
			
			public	function ElementId($_element = '',$_element_hiarchy = '',$row = '')
				{
					
					/*
					echo '<hr>';
					echo '<pre style="background-color: red;">';
				//	print_r($_element);
					echo '</pre>';
					echo '<hr>';
					echo '<pre style="background-color: green;">';
				//	print_r($_element_hiarchy);
					echo '</pre>';
					$GETS	=	$this->RowStyles($_element,$_element_hiarchy);
				//	print_r($GETS);
					*/
					
					$rendElem					=	new renderElements;
					// If row, return styles
					if($row !== 'cont') {
							$_process_format	=	$rendElem->generateStyles($_element);
							$_final['class']	=	(!empty($_element['class']))? ' class="'.$_element['class'].'"':'';
							$_final['id']		=	(!empty($_element['_id']))? ' id="'.$_element['_id'].'"':'';
							$_final['style']	=	(!empty($_process_format))? ' style="'.$_process_format.'"':'';
						}
					// If content, filter what is to be dispayed and return
					else {
							AutoloadFunction('use_markup');
							$_process_format	=	$rendElem->generateStyles($_element);
							$_final				=	use_markup($rendElem->Render($_element, $_process_format));
						}
						
					if(isset($_final))
						return $_final;
				}
			
			// This function sets the stles from the returned css keys found on the GetCSSList function
			public	function RowStyles($content,$payload)
				{
					$this->content	=	$content;
					$this->payload	=	$payload;
					$css			=	$this->GetCSSList();
					$rendElem		=	new renderElements;
					$rendElem->renderContentElements($this->content[key($this->payload)], $css);
					
					$this->style_arr['class']	=	(!empty($this->content[key($this->payload)]['class']))? $this->content[key($this->payload)]['class']:'';
					$this->style_arr['style']	=	(!empty($rendElem->finalStyle))? $rendElem->finalStyle:'';
					$this->style_arr['row']		=	($this->content[key($this->payload)]['component_type'] == 'row' || empty($this->content[key($this->payload)]['component_type']));
					$this->style_arr['id']		=	(!empty($this->content[key($this->payload)]['_id']))? $this->content[key($this->payload)]['_id']:'';
					return $this->style_arr;
				}

			// This function returns all the keys in the db that are css
			public	function GetCSSList()
				{
					AutoloadFunction('get_css_fields');
					$this->CCS_List		=	false;
					return $this->CCS_List;
				}
			
			public	function CreateStyles($element,$classifications)
				{
					$this->element			=	$element;
					$this->classifications	=	(is_array($classifications))? $classifications : array();
					
					// Filter out the variables for this element and build a styles string  
					foreach($this->element as $keys => $values) {
						// Delete all styles unrelated to the styles string
						if(!empty($values) && in_array($keys, $this->classifications)) {
							// Replace underscores with dashes for web-friendly style use
							$keys			=	str_replace('_', '-', $keys);
							// Explode the keys to check if there is a dash at the front
							$explode_keys	=	explode('-', $keys);
							// If there is a dash (empty array value on 0 key)
							if(empty($explode_keys[0])) {
								// Delete the key
								array_shift($explode_keys);
								// Rebuild the key to include the dash again
								$keys	=	implode("-", $explode_keys);
							}
							
							$styles[0]	=	'';
							
							// Fix box shadow element
							if($keys == 'box-shadow') {
									$compatibility	=	array('box-shadow', '-moz-box-shadow', '-webkit-box-shadow');
									for($bs = 0; $bs <= 2; $bs++) {
										$styles[0]	.=	" $compatibility[$bs]: ".$values.";";
									}
								}
							else {
								$styles[0]	.=	$keys.': '.$values.';';
							}
						}
					}
					
					return $this->element_style	= (isset($styles) && is_array($styles))? implode("", $styles):'';
				}
			
			public	function RenderLayout($render_content,$hiarchy_content)
				{ 
					$this->hiarchy_content	=	$hiarchy_content;
					$this->render_content	=	$render_content;
					
					// Start an instance of a render
					$rendElem				=	new renderElements;
					$component_type			=	(isset($this->render_content[key($this->hiarchy_content)]['component_type']))? $this->render_content[key($this->hiarchy_content)]['component_type']:'';
					
					if(get_edit_status() && ($component_type == 'row')) {
?>					<div class="point_editors">
						<?php core::ComponentEditors($this->content[key($this->payload)]); ?>
					</div>
<?php 				}
					
					// Loop through the tree array
					foreach($this->hiarchy_content as $keys => $values) {
						// This is the content array+unique_id value 
						$info	=	$this->render_content[$keys];
						$is_row	=	$is_div	=	false;
						if(isset($info['component_type'])) {
							$is_row	=	($info['component_type'] == 'row');
							$is_div	=	($info['component_type'] == 'div');
						}
					
						if(get_edit_status() && !$is_row && !$is_div) {
?>					<div class="point_editors">
						<?php core::ComponentEditors($info); ?>
					</div>
<?php 					}
								
						if($is_row !== true && $is_div !== true) {
							if($info['page_live'] == 'on')
								echo $this->ElementId($info,$this->hiarchy_content,'cont');
						}
						
						if($is_row) { ?><div<?php echo implode(' ',$this->ElementId($this->render_content,$this->hiarchy_content,'row')); ?>><?php }
						if($is_div) { ?><div<?php echo implode(' ',$this->ElementId($info,$this->hiarchy_content,'div')); ?>><?php }
						
						if(get_edit_status() && $is_div) {
?>					<div class="div_wrap_editors">
						<div class="point_editors">
							<?php core::ComponentEditors($info); ?>
						</div>
<?php 				}
								
					// If value is an array, loop back through this method
					if(is_array($values)) {
						$this->Track($values,$this->render_content);
					}
						
					if(get_edit_status() && $is_div) {
?>				   </div>
<?php 				}
					   
					if($is_row) {
?>				</div>
<?php 				}
					if($is_div) {
?>				</div>
<?php 				}
				}
			}
		}