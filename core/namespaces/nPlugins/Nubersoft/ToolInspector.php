<?php
namespace nPlugins\Nubersoft;

class ToolInspector extends \nPlugins\Nubersoft\AdminToolsComponentEditor
	{
		protected	$doc_root,
					$dropdowns,
					$menu_set,
					$payload;
		
		private		$_table_prefs,
					$_return_array;
		
		public	function __construct()
			{
				# This will set a long or short path for the link/mkdir
				# true = full directory from root
				# false = just relative links
				$this->doc_root	=	NBR_ROOT_DIR;
				# Sets the action to use when saving the component
				$this->setActionType('nbr_save_menu');
				# Sets the table to draw settings from
				$this->useTable('main_menus');
				# Sets which data menu format to draw from
				$this->useComponentMap('main_menus');
				return parent::__construct();
			}
		
		public	function containerDropDown()
			{
				$settings	=	func_get_args();
				$parent_id	=	(isset($settings[0]) && !empty($settings[0]))? trim($settings[0]) : false;
				$unique_id	=	(isset($settings[1]) && !empty($settings[1]))? trim($settings[1]) : false;
				$sql	=	"select * from `main_menus` where `is_admin` != 2 AND `is_admin` != 1";
				if(!empty($parent_id))
					$sql	.=	" AND `parent_id` != '{$parent_id}'";
				
				if(!empty($unique_id))
					$sql	.=	" AND `unique_id` != '{$unique_id}'";
					
				$containers	=	$this->nQuery()->query($sql." ORDER BY `menu_name` ASC")->getResults();
				
				if(is_array($containers)) {
					foreach($containers as $_objects) {
						if(!empty($_objects['parent_id']))
							$_parents[$_objects['parent_id']][]	=	$_objects['unique_id'];
					}
				}
				
				ob_start();
				include(__DIR__.DS.'ToolInspector'.DS.'ContainerDropDown.php');
				$data	=	ob_get_contents();
				ob_end_clean();
				return $data;
			}
			
		public function	execute($ID = false)
			{
				if(!$this->siteValid())
					return;
				
				if($this->isAdmin()) {
					$nubquery	=	$this->nQuery();
					# Fetch dropdowns
					$this->dropdowns	=	$this->inputFields("main_menus");
					
					if(empty($ID) && !empty($this->getPost('deliver')->ID))
						$ID	=	$this->getPost('deliver')->ID;
					
					include(__DIR__.DS.'ToolInspector'.DS.'execute.php');	
				} 
			}
		
		protected	function inputFields($table = false, $display = false)
			{
				if(empty($table))
					return false;
					
				$query		=	$this->getDropdowns($table);
				
				if($query != 0 && $display == true) {
					foreach($query as $select => $options) {
						$design[]	=	'<select name="'.$select.'">';
						foreach($options as $settings) {
							if($this->checkEmpty($settings,'page_live','on'))
								$design[]	=	'<option value="'.$settings['menuVal'].'">'.$settings['menuName'].'</option>';
						}
						$design[]	=	'</select>';
					}
						
					return (isset($design))? $design:false;
				}
				else
					return $query;
			}
		
		public	function getDropDowns($table = false)
			{	
				if($table == false)
					return false;
				
				# Fetch columns in table
				$newCols	=	$this->getTableColumns($table);
				try {
					# Check if stored dropdown settings
					$fields		=	$this->nQuery()
										->select(array("assoc_column","menuName","menuVal","page_live"))
										->from("dropdown_menus")
										->wherein("assoc_column",$newCols)
										->orderBy(array("page_order"=>"ASC"))
										->fetch();
				}
				catch (Exception $e){
					throw new \Exception('Menus are not installed properly.');
				}
					
				return $this->organizeByKey($fields,'assoc_column',array('unset'=>false,'multi'=>true));
			}
		
		protected	function folderStucture($array,$key = false)
			{
				
				foreach($array as $col => $value) {
				?>
				<div class="inspector-mini-allwrap">
					<div class="inspector-mini-wrap">
							<?php echo $this->allMenus($col); ?>
					</div>
					<?php if(!is_array($value)) { ?>
				</div>
				<?php	}
						
						if(is_array($value)) {
							$this->folderStucture($value,$col); ?>
				</div>
					<?php	}
					}
			}
		
		protected	function menuTable($_table_prefs,$_return_array)
			{
				$this->payload	=	$this->toArray($this->getDataNode('menu_data'));
				$structure		=	$this->toArray($this->getDataNode('menu_struc'));
				return $this->render($this->folderStucture($structure));
			}
			
		public	function addNewComponent($CompSet = false)
			{
				ob_start();
				include(__DIR__.DS.'ToolInspector'.DS.'AddNewComponent.php');
				$data	=	ob_get_contents();
				ob_end_clean();
				
				return $data;
			}
			
		public	function allMenus($unique_id = false)
			{
				if(!$unique_id)
					return;
				
				if(empty($this->payload[$unique_id]))
					return;
				
				$id			=	$this->payload[$unique_id]['ID'];
				$nubquery	=	$this->nQuery();
				$query		=	$nubquery
									->select()
									->from("main_menus")
									->where(array("ID"=>$id))
									->fetch();
											
				$data		=	($query != 0)? $query[0] : false;
				
				if(!$data)
					return;

				return $this->render(__DIR__.DS.'ToolInspector'.DS.'AllMenus.php',$data);
			}
		
		public	function renderToggleIcon($value = false)
			{
				$value	=	strtolower($value);
				$live	=	'def';
				
				if($value == 'off')
					$live	=	'red';
				elseif($value == 'on')
					$live	=	'green';
				
				return	$this->getHelper('nImage')->image(NBR_MEDIA_IMAGES.DS.'core'.DS."led_{$live}.png",array("style"=>"width: 15px; height: 15px;"),true,false);
			}
	}