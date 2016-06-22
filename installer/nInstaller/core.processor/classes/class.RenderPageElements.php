<?php
	
	class RenderPageElements
		{
			public		$nuber;
			public		$display_inline;
			public		$display;
			
			protected	$payload;
			protected	$compile_inline;
			protected	$rendered;
			
			public	function __construct()
				{
					register_use(__METHOD__);
					
					AutoloadFunction('check_empty,nQuery');
				}
			
			public	function Initialize($payload = false)
				{
					register_use(__METHOD__);
					
					$this->payload			=	$payload;
					$this->display_inline	=	false;
					$this->compile_inline	=	false;
					
					return $this;
				}
			
			// Array required
			public	function SetStyles()
				{
					
					register_use(__METHOD__);
					
					if(!empty($this->payload['css'])) {
							AutoloadFunction('render_inline_css');
							$this->compile_inline['style']	=	render_inline_css(array("css"=>$this->payload['css'],"decode"=>true));
						}
					
					return $this;
				}
			
			public	function SetIdClass($type = 'class')
				{	
					register_use(__METHOD__);
					
					if(isset($this->payload) && is_array($this->payload)) {
							if(!empty($this->payload[$type])) {				
									$name	=	trim($type,"_");
									$this->compile_inline[$name]	=	$name.'="'.Safe::decode($this->payload[$type]).'"';
								}
						}
					
					return $this;
				}
			

			public	function CheckPermissions()
				{
					AutoloadFunction('get_edit_status');
						
					// If the element is live
					$_settings['live']		=	(check_empty($this->payload,'page_live','on'));
					// If the element requires login
					$_settings['login']		=	(check_empty($this->payload,'login_view','on'));
					// If the track editor is on
					$_settings['track']		=	(get_edit_status());
					// If the element requires a usergroup
					$_settings['perm']		=	true;
					if($_settings['login']) {
						AutoloadFunction('allow_if,is_loggedin');
						$user				=	(!empty($this->payload['login_permission']))? 3 : $this->payload['login_permission'];
						// Check if the user is logged in and has good enough permissions
						$_settings['perm']	=	(is_loggedin() && allow_if($user));
					}
					// Always return true if the track editor is on
					if($_settings['track'])
						return true;
					else {
						// If the element is live
						if($_settings['live']) {
							// If the login is required
							if($_settings['login']) {
								// If the correct permissions set
								if($_settings['perm'])
									// return true
									return true;
							}
							// If login not required, render ok
							else
								return true;
						}
					}
				}
			
			public	function Compile()
				{
					register_use(__METHOD__);
					if(!empty($this->compile_inline) && is_array($this->compile_inline))
						$this->display_inline	=	implode(" ",$this->compile_inline);
					
					$this->display_inline	=	(isset($this->display_inline))? $this->display_inline:"";
					
					return $this;
				}
			
			public	function Display()
				{
					register_use(__METHOD__);
					$this->compile_inline	=	array();
					$query					=	nQuery();
					$_comp					=	(!empty($this->payload['component_type']));
					// Save all the localized css, id, class to one string
					$inline					=	$this	->SetStyles()
														->SetIdClass('_id')
														->SetIdClass('class')
														->Compile()
														->display_inline;

					if(!$_comp)
						return false;
					
					ob_start();
					// Decode content block
					$this->payload['content']	=	(!empty($this->payload['content']))? Safe::decode($this->payload['content']):"";
						
					// Set rules for TEXT INPUT
					switch($this->payload['component_type']) {
						case('text') :
?>							<span <?php echo $inline; ?>><?php echo $this->payload['content']; ?></span>
<?php						break;
						// Set rules for CODE INPUT
						case('code') :
							AutoloadFunction("use_markup");
							if($inline) {
?>							<div <?php echo $inline; ?>>
<?php 						}
							echo PHP_EOL."\t\t\t\t\t\t\t\t".use_markup($this->payload['content']);
							if($inline) {
?>							</div>
<?php 						}
							break;
						// Set rules for IMAGE INPUT
						case('image'):
							if(!empty($this->payload['file_path']))
								$filePath	=	$this->payload['file_path'].$this->payload['file_name'];
							else {
								$file_check_res	=	$query->select(array("file","file_path"))->from("image_bucket")->where (array("ref_page"=>NubeData::$settings->page_prefs->unique_id,"ID"=>$this->payload['ID']))->fetch();
								$file_check_dir	=	($file_check_res != 0)? str_replace(ROOT_DIR, "", $file_check_res[0]['file_path']): '/client_assets/images/default/';
								$filePath		=	$file_check_dir.$file_check_res[0]['file'];
							}
								
							if(isset($filePath)) {
?>						  <img src="<?php echo $filePath; ?>" <?php echo $inline; ?> />
<?php						}
							break;
						// Set rules for BUTTON INPUT
						case('button'):
?>							<a href="<?php echo Safe::decode($this->payload['a_href']); ?>" <?php echo $inline; ?>><?php echo $this->payload['content']; ?></a>
<?php 						break;
						// Set rules for EMAIL INPUT
						case('form_email'):
							AutoloadFunction('create_emailer');
							echo create_emailer(array("attributes"=>$inline,"info"=>$this->payload));
							break;
						default:
							if(is_file($inc = CLIENT_DIR.'/components/'.$this->payload['component_type'].'/view.php'))
								include($inc);
					}
						
					$data	=	ob_get_contents();
					ob_end_clean();
					
					$this->display	=	(isset($_final))? $_final : $data;
					
					return $this;
				}
		}