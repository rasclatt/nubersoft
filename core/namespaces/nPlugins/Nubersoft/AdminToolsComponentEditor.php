<?php
namespace nPlugins\Nubersoft;

class	AdminToolsComponentEditor extends \nPlugins\Nubersoft\ComponentWidget
	{
		public		$ref_page,
					$page_id;

		protected	$command,
					$data,
					$allowAdd,
					$tempid,
					$curr,
					$display_path;
		
		const	DEFAULT_LAYOUT	=	'AdminToolsComponentEditor';
		
		public	function __construct($page_id = false, $ref_page = false,$isName = 'comp')
			{
				$this->page_id	=	$page_id;
				$this->ref_page	=	$ref_page;
				$this->tempid	=	$isName.uniqid(rand(1000, 10000));
				
				return parent::__construct();
			}
		
		public	function getDefPageIdVal($key,$array)
			{
				if(is_object($array))
					$array	=	$this->toArray($array);
				
				if(isset($array['data'][$key]))
					return $array['data'][$key];
				elseif(isset($array['deliver']['query_data'][$key]))
					return $array['deliver']['query_data'][$key];
				else
					return false;
			}
		
		public	function addNewComponent($CompSet = false)
			{
				ob_start();
				include($this->getDefTempFile('AddNewComponent'));
				$data	=	ob_get_contents();
				ob_end_clean();
				
				return $data;
			}
		
		public	function display($data = array(), $allowAdd = true, $command = false)
			{
				$this->data			=	$data;
				$this->command		=	$command;
				$this->allowAdd		=	$allowAdd;
				$this->display_path	=	$this->getDisplayLayout();
				
				ob_start();
				# Check if this component contains an active data set
				$CompSet	=	(isset($this->data['unique_id']));
				include($this->getDefTempFile('Display'));
				$data	=	ob_get_contents();
				ob_end_clean();
				return $data;
			}
		
		public	function getDisplayLayout()
			{
				return (!empty($this->display_path))? $this->display_path : self::DEFAULT_LAYOUT;
			}
		
		public	function setDisplayLayout($path)
			{
				$this->display_path	=	$path;
				return $this;
			}
		
		protected	function getDefTempFile($file)
			{
				$custom	=	__DIR__.DS.$this->getDisplayLayout().DS.$file.'.php';
				if(is_file($custom))
					return $custom;
				else
					return 	__DIR__.DS.self::DEFAULT_LAYOUT.DS.$file.'.php';
			}
		
		public	function deleteComponent($CompSet = false)
			{
				if(empty($this->data['ID']))
					return false;
				
				ob_start();
				include($this->getDefTempFile('DeleteComponent'));
				$data	=	ob_get_contents();
				ob_end_clean();
				
				return $data;
			}
		
		public	function duplicateComponent($CompSet = false)
			{
				ob_start();
				include($this->getDefTempFile('DuplicateComponent'));
				$data	=	ob_get_contents();
				ob_end_clean();
				
				return $data;
			}
		
		protected	function duplicateComponentsList($values = false)
			{
				$icon_arr				=	array();
				$icon_arr['row']		=	'icn_div.png';
				$icon_arr['div']		=	'icn_div.png';
				$icon_arr['button']		=	'icn_button.png';
				$icon_arr['code']		=	'icn_code.png';
				$icon_arr['button']		=	'icn_button.png';
				$icon_arr['form_email']	=	'icn_form_email.png';
				$icon_arr['image']		=	'icn_image.png';
				$icon_arr['text']		=	'icn_text.png';
				$icon_arr['unknown']	=	'icn_alert.png';
				
				if(!empty($values) && is_array($values)) {
					include($this->getDefTempFile('DuplicateComponentsList'));
				}
			}
		
		public	function containerDropDown()
			{
				$settings	=	func_get_args();
				$ref_page	=	(!empty($settings[0]))? $settings[0] : false;
				$column		=	(!empty($settings[1]))? $settings[1] : 'ref_page';
				$addCustom	=	(!empty($settings[2]))? $settings[2] : true;
				
				if(empty($ref_page) && $column == 'ref_page')
					return;
					
				if(!empty($addCustom))
					$addCustom	=	" and (component_type = 'div' or component_type = 'row')";
					
				$table		=	$this->table;
				$query		=	$this->nQuery();
				$_checkCol	=	$query->query("SHOW columns from `{$table}` where `field` = '{$column}'")->getResults();
				
				if($_checkCol != 0) {
					$query	->select()
							->from($table)
							->where(array($column=>$ref_page));
							
					if($addCustom)
						$query->addCustom($addCustom);
					
					$containers	=	$query->fetch();
					
					if(is_array($containers)) {
						foreach($containers as $_objects) {
							if(!empty($_objects['parent_id']))
								$_parents[$_objects['parent_id']][]	=	$_objects['unique_id'];
						}
					}
					
					$_parent	=	(!empty($this->data['unique_id']) && isset($_parents[$this->data['unique_id']]));
					$_child		=	(!empty($this->data['parent_id']) && isset($_parents[$this->data['parent_id']]))? true:true;
					$_isDiv		=	(!empty($this->data['component_type']) && in_array($this->data['component_type'],array('div','row'))); 
					
					ob_start();
					include($this->getDefTempFile('ContainerDropDown'));
					$data	=	ob_get_contents();
					ob_end_clean();
					return $data;
				}
			}
		
	# Main component function
	public function createFormElements()
			{
				$settings			=	$this->getComponentData(array("table"=>$this->getTable()));
				$layout				=	(!empty($settings['layout']))? $settings['layout']:array();
				$array				=	(!empty($settings['format']))? $settings['format']:array();
				$options			=	(!empty($settings['options']))? $settings['options']:array();
				$title				=	(!empty($settings['title']))? $settings['title']:"Create New";
				$values				=	$this->data;
				$table				=	(isset($settings['table']))? $settings['table']:"components";
				
				$this->resetTableAttr($table);
				
				ob_start();
				foreach($layout as $compname => $compvals)
					include($this->getDefTempFile('createFormElements'));
		
				$data	=	ob_get_contents();
				ob_end_clean();
				
				return $data;
			}
		
		public	function alterAttachedImage()
			{
				if(empty($this->data['file_path']))
					return;
					
				ob_start();
				include($this->getDefTempFile(__FUNCTION__));
				$data	=	ob_get_contents();
				ob_end_clean();
				
				return $data;
			}
	}