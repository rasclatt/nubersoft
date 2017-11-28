<?php
namespace nPlugins\Nubersoft;

class RenderPageElements extends \nPlugins\Nubersoft\RenderElements
{
	public		$display_inline,
				$display;

	protected	$compile_inline,
				$rendered;

	public	function __construct()
	{
		return parent::__construct();
	}

	public	function initialize($payload = false)
	{
		$this->payload			=	$payload;
		$this->display_inline	=	false;
		$this->compile_inline	=	false;

		return $this;
	}

	// Array required
	public	function setStyles()
	{
		if(!empty($this->payload['css'])) {
			$this->compile_inline['style']	=	$this->renderInlineCss(array("css"=>$this->payload['css'],"decode"=>true));
		}

		return $this;
	}

	public	function setIdClass($type = 'class')
	{
		if(isset($this->payload) && is_array($this->payload)) {
			if(!empty($this->payload[$type])) {				
				$name	=	trim($type,"_");
				$this->compile_inline[$name]	=	$name.'="'.$this->getHelper('Safe')->decode($this->payload[$type]).'"';
			}
		}

		return $this;
	}

	public	function checkPermissions()
	{
		// If the element is live
		$_settings['live']		=	($this->checkEmpty($this->payload,'page_live','on'));
		// If the element requires login
		$_settings['login']		=	($this->checkEmpty($this->payload,'login_view','on'));
		// If the track editor is on
		$_settings['track']		=	($this->getPlugin('\nPlugins\Nubersoft\core')->getEditStatus());
		// If the element requires a usergroup
		$_settings['perm']		=	true;
		if($_settings['login']) {
			$user	=	(empty($this->payload['usergroup']))? $this->getUsergroup('NBR_WEB') : $this->getUsergroup($this->payload['usergroup']);
			// Check if the user is logged in and has good enough permissions
			$_settings['perm']	=	($this->isLoggedIn() && $this->getHelper('UserEngine')->allowIf($user));
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

	public	function compile()
	{
		if(!empty($this->compile_inline) && is_array($this->compile_inline))
			$this->display_inline	=	implode(" ",$this->compile_inline);

		$this->display_inline	=	(isset($this->display_inline))? $this->display_inline:"";

		return $this;
	}

	public	function display($force = false)
	{
		$this->display			=	'';
		$this->compile_inline	=	array();
		$query					=	$this->nQuery();
		$_comp					=	(!empty($this->payload['component_type']));
		// Save all the localized css, id, class to one string
		$inline					=	$this	->setStyles()
											->setIdClass('_id')
											->setIdClass('class')
											->compile()
											->display_inline;

		if(!$_comp) {
			if($force && !empty($this->payload['content']))
				$this->display	=	$this->safe()->decode($this->payload['content']);

			return $this;
		}

		ob_start();
		// Decode content block
		$this->payload['content']	=	(!empty($this->payload['content']))? $this->safe()->decode($this->payload['content']):"";

		// Set rules for TEXT INPUT
		switch($this->payload['component_type']) {
			case('text') :
?>							<span <?php echo $inline; ?>><?php echo $this->payload['content']; ?></span>
<?php						break;
			// Set rules for CODE INPUT
			case('code') :
				$this->autoload(array("use_markup"));
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
					$file_check_res	=	$query->select(array("file","file_path"))
											->from("image_bucket")
											->where (array("ref_page"=>\Nubersoft\Singleton::$settings->page_prefs->unique_id,"ID"=>$this->payload['ID']))
											->fetch();
					$file_check_dir	=	($file_check_res != 0)? str_replace(NBR_ROOT_DIR, "", $file_check_res[0]['file_path']): '/client/images/default/';
					$filePath		=	$file_check_dir.$file_check_res[0]['file'];
				}

				if(isset($filePath)) {
?>						  <img src="<?php echo $filePath; ?>" <?php echo $inline; ?> />
<?php						}
				break;
			// Set rules for BUTTON INPUT
			case('button'):
?>							<a href="<?php echo $this->safe()->decode($this->payload['a_href']); ?>" <?php echo $inline; ?>><?php echo $this->payload['content']; ?></a>
<?php 						break;
			// Set rules for EMAIL INPUT
			case('form_email'):
				echo $this->getPlugin('\nPlugins\Nubersoft\Emailer')
					->create(array("attributes"=>$inline,"info"=>$this->payload));
				break;
			default:
				$inc = NBR_CLIENT_DIR.DS.'Components'.DS.$this->payload['component_type'].DS.'view.php';
				if(is_file($inc))
					include($inc);
		}

		$data	=	ob_get_contents();
		ob_end_clean();

		$this->display	=	(isset($_final))? $_final : $data;

		return $this;
	}

	public	function getDisplay()
	{
		return $this->display;
	}

	public	function renderInlineCss($settings = false)
	{
		$cssArray		=	(!empty($settings['css']))? $settings['css'] : false;
		$decode			=	(!empty($settings['decode']))? $settings['decode'] : false;
		$component_id	=	(!empty($settings['ID']))? $settings['ID'] : false;

		if(!$cssArray && !$cssArray)
			return;

		if($component_id != false) {
			$id		=	$this->nQuery()
							->select("css")
							->from("components")
							->where(array("ID"=>$component_id))
							->fetch();

			if($id != 0) {
				if(isset($id[0]['css']) && !empty($id[0]['css'])) {
					$cssArray	=	$id[0]['css'];
					$decode		=	true;
				}
			}
		}

		// jSON decode if requested
		$css	=	(!empty($cssArray) && $decode)? json_decode($cssArray,true) : $cssArray;

		// Return whatever remains
		if(!empty($css)) {
			foreach($css as $key => $value) {
				$value	=	trim($value);
				if(!empty($value))
					$new[]	=	$key.": ".$value;
			}

			return (!empty($new))? 'style="'.implode("; ",$new).';"':"";
		}
	}
}