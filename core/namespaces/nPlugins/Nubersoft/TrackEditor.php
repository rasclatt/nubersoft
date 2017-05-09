<?php
namespace	nPlugins\Nubersoft;

class	TrackEditor extends \nPlugins\Nubersoft\RenderPageElements
	{
		public		$payload,
					$content,
					$render_content,
					$hiarchy_content,
					$style_arr,
					$element,
					$classifications,
					$element_style,
					$CCS_List,
					$core,
					$root_folder;
		
		public	function __construct($root_folder)
			{
				$this->root_folder	=	$root_folder;
				return parent::__construct();
			}
		
		public	function defaultCSS()
			{ 
			}
		
		public	function track($payload = '',$content = '')
			{
				$component		=	new create();
				$row_wrap		=	(isset($content[key($payload)]['component_type']) && in_array($content[key($payload)]['component_type'],array('div','row')));
				$_div_element	=	$this->elementId($content,$payload,$row_wrap);
				
				# If the component is a row, then add the default two container divs
				if($row_wrap) {
?>				<div<?php echo $_div_element['id'].$_div_element['style'].$_div_element['class']; ?>>
<?php 				}
				if($this->getEditStatus()) {
					if(isset($_SESSION['toggle']['edit']['type']) && $_SESSION['toggle']['edit']['type'] == 'live')
						$this->renderLayout($content, $payload);
					else
						$this->trackEditorRender($content, $payload);
				}
			//	else
			//		$this->pageRender($content, $payload);
				
				if($row_wrap) {
?>					</div>
<?php 			}
			}
		
		public	function trackEditorRender($render_content, $hiarchy_content)
			{
				$this->hiarchy_content	=	$hiarchy_content;
				$this->render_content	=	$render_content;
				$divSet					=	(is_array($this->hiarchy_content));
				
				if($divSet) {
?>					<div class="track_editor">
<?php			}
					
				# Loop through the tree array
				foreach($this->hiarchy_content as $keys => $values) {
					# This is the content array+unique_id value 
					$info		=	$this->render_content[$keys];
					# Styles for this container component if not row
					$this->renderContentElements($info, $this->getCSSList());
					$is_div	=	($this->checkEmpty($info,'component_type','div'));
					if($is_div) {
?>						<div <?php $styles = $this->createStyles($info,$this->getCSSList()); if(!empty($styles)) echo 'style="'.$styles.'"'; ?>>
						<div class="track_editor_nested" <?php if(!empty($info['admin_tag'])) { ?>style="background-color: <?php echo $info['admin_tag']; ?>;"<?php } ?>>
<?php 				} 

					if($this->getEditStatus()) {
						echo $this->componentEditors($info);
					}
					# If value is an array, loop back through this method
					if(is_array($values))
						$this->track($values,$this->render_content);
						
					if($is_div) {
?>							</div>
					</div>
<?php					}
				}

				if($divSet){
?>					</div>
<?php			}
			}
		
		public	function pageRender($render_content, $hiarchy_content)
			{
				$this->hiarchy_content	=	$hiarchy_content;
				$this->render_content	=	$render_content;
				# Check if div
				$_div					=	array('row','div');
				# Loop through the tree array
				foreach($this->hiarchy_content as $keys => $values) {
					# This is the content array+unique_id value 
					$info	=	$this->render_content[$keys];
					# Check permissions for this component
					$perms	=	Inclusion::Check($info);
					if($perms) {
						//	if(!in_array($info['component_type'],$_div)) 
						//		echo $this->elementId($info,$this->hiarchy_content,'cont');
						if(!in_array($info['component_type'],$_div)) {
?>							<div<?php echo implode('',$this->elementId($info,$this->hiarchy_content,'div')); ?>>
<?php						}

						# If value is an array, loop back through this method
						if(is_array($values)) 
								$this->track($values,$this->render_content);
							
						if(!in_array($info['component_type'],$_div)) {
?>							</div>
<?php					}
					}
				}
			}
		
		public	function elementId($_element = '',$_element_hiarchy = '',$row = '')
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
				$GETS	=	$this->rowStyles($_element,$_element_hiarchy);
			//	print_r($GETS);
				*/
				# If row, return styles
				if($row !== 'cont') {
					$_process_format	=	$this->generateStyles($_element);
					$_final['class']	=	(!empty($_element['class']))? ' class="'.$_element['class'].'"':'';
					$_final['id']		=	(!empty($_element['_id']))? ' id="'.$_element['_id'].'"':'';
					$_final['style']	=	(!empty($_process_format))? ' style="'.$_process_format.'"':'';
				}
				# If content, filter what is to be dispayed and return
				else {
					$this->autoload('use_markup');
					$_process_format	=	$this->generateStyles($_element);
					$_final				=	use_markup($rendElem->Render($_element, $_process_format));
				}
					
				if(isset($_final))
					return $_final;
			}
		
		# This function sets the stles from the returned css keys found on the getCSSList function
		public	function rowStyles($content,$payload)
			{
				$this->content	=	$content;
				$this->payload	=	$payload;
				$css			=	$this->getCSSList();
				$this->renderContentElements($this->content[key($this->payload)], $css);
				
				$this->style_arr['class']	=	(!empty($this->content[key($this->payload)]['class']))? $this->content[key($this->payload)]['class']:'';
				$this->style_arr['style']	=	(!empty($this->finalStyle))? $this->finalStyle:'';
				$this->style_arr['row']		=	($this->content[key($this->payload)]['component_type'] == 'row' || empty($this->content[key($this->payload)]['component_type']));
				$this->style_arr['id']		=	(!empty($this->content[key($this->payload)]['_id']))? $this->content[key($this->payload)]['_id']:'';
				return $this->style_arr;
			}

		# This function returns all the keys in the db that are css
		public	function getCSSList()
			{
				$this->autoload('get_css_fields');
				$this->CCS_List		=	false;
				return $this->CCS_List;
			}
		
		public	function createStyles($element,$classifications)
			{
				$this->element			=	$element;
				$this->classifications	=	(is_array($classifications))? $classifications : array();
				
				# Filter out the variables for this element and build a styles string  
				foreach($this->element as $keys => $values) {
					# Delete all styles unrelated to the styles string
					if(!empty($values) && in_array($keys, $this->classifications)) {
						# Replace underscores with dashes for web-friendly style use
						$keys			=	str_replace('_', '-', $keys);
						# Explode the keys to check if there is a dash at the front
						$explode_keys	=	explode('-', $keys);
						# If there is a dash (empty array value on 0 key)
						if(empty($explode_keys[0])) {
							# Delete the key
							array_shift($explode_keys);
							# Rebuild the key to include the dash again
							$keys	=	implode("-", $explode_keys);
						}
						
						$styles[0]	=	'';
						
						# Fix box shadow element
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
		
		public	function renderLayout($render_content,$hiarchy_content)
			{
				$this->hiarchy_content	=	$hiarchy_content;
				$this->render_content	=	$render_content;
				$component_type			=	(isset($this->render_content[key($this->hiarchy_content)]['component_type']))? $this->render_content[key($this->hiarchy_content)]['component_type']:'';
				include(__DIR__.DS.'TrackEditor'.DS.'renderLayout.php');
			}
		
		public	function getComponentHeader()
			{
				if(!empty($this->curr['content'])) {
					if(strlen($title = html_entity_decode($this->curr['content'],ENT_QUOTES)) > 20)
						return htmlentities(substr($title,0,20), ENT_QUOTES)."...";
					else
						return $this->curr['content'];
				}
				
				return "COMPONENT SETTINGS";
			}
	}