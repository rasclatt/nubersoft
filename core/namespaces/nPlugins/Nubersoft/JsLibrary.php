<?php
namespace nPlugins\Nubersoft;
/*
**	@description	This class is a repository for php-to-javascript applets
*/
class	JsLibrary extends \Nubersoft\nRender
	{
		private	$data,
				$jName;
		
		const	WP	=	'jQuery';
		const	RAW	=	'$';
		
		public	function __construct($objName = 'njQuery')
			{
				$this->jName	=	(!empty($objName))? $objName : 'njQuery';
				$this->data		=	array();
				
				return parent::__construct();
			}
		/*
		** @param	$content	[string] This allows for adding in new script
		**							 	 Script will be added into the chain in the order the methi is called
		*/
		public	function addScript($content = false)
			{
				echo $content;
				
				if(!empty($content) && is_string($content))
					$this->data[]	=	$content;
				
				return $this;
			}
		/*
		** @param	$link	[string] This allows for adding in new javascript libraries
		**							 via a link returning itself for method chaining
		*/
		public	function addLibrary($link)
			{
				if(is_array($link)) {
					foreach($link as $value) {
						$this->data[]	=	'<script type="text/javascript" src="'.$value.'"></script>';
					}
				}
				else
					$this->data[]	=	'<script type="text/javascript" src="'.$link.'"></script>';
				
				return $this;
			}
		/*
		** @param	[array] $settings	This allows the method to decide what to include for output to browser
		*/
		public	function defaultJQuery($settings = false)
			{
				$jqLib		=	(isset($settings['jq_version']))? $settings['jq_version'] : '3.0.1';
				$uLib		=	(isset($settings['ui_version']))? $settings['ui_version'] : '1.9.2';
				$vLib		=	(isset($settings['jv_version']))? $settings['jv_version'] : '1.11.1';
				$incVLib	=	(isset($settings['jv']))? $settings['jv'] : true;
				$incULib	=	(isset($settings['ui']))? $settings['ui'] : true;
				ob_start();
?>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/<?php echo $jqLib; ?>/jquery.min.js"></script>
<?php			if($incULib) {
?><script type="text/javascript" src="//code.jquery.com/ui/<?php echo $uLib; ?>/jquery-ui.js"></script>
<?php			}
				if($incVLib) {
?><script type="text/javascript" src="//ajax.aspnetcdn.com/ajax/jquery.validate/<?php echo $vLib; ?>/jquery.validate.js"></script>
<?php			}

				$this->data[]	=	ob_get_contents();
				ob_end_clean();
			
				return $this;
			}
		/*
		** @param $arr	[bool] On true, will return the array of all javascript elements in the assembly
		*/
		public	function getResults($array = false)
			{
				return ($array)? $this->data : implode(PHP_EOL,$this->data);
			}
		/*
		** @param $arr	[array]	Add in an array and get a javascript object back
		** @param $json	[bool]	true just returns a json string.
		*/
		private	function arrayToJsObject($arr = false,$json = false)
			{
				if(empty($arr)) {
					if(is_bool($arr))
						return (!$arr)? 'false':'true';
					else
						return '{}';
				}
				
				if($json)
					return json_encode($arr);
				
				if(is_array($arr)) {			
					foreach($arr as $k => $v) {
							$return[$k]	=	$k.': '.$this->arrayToJsObject($v);
					}
				}
				else {
					$arr	=	(is_numeric($arr) || $arr === 'true' || $arr === 'false' || strpos($arr,"[") !== false)? $arr: "'$arr'";
					$return	=	(strpos($arr,'{') !== false && strpos($arr,'}') !== false)? trim($arr,"'") : $arr;
				}
		
				return (is_array($return))? '{ '.PHP_EOL."\t".implode(",\t".PHP_EOL."\t",$return).PHP_EOL.' }' : $return;
			}
		/*
		** @description			This is a wrapper for the above method.
		** @param	$arr	[array]	Add in an array and get a javascript object back
		** @param	$json	[bool]	true just returns a json string.
		** @return			[string]
		*/
		public	function makeObject($array = false,$json = false)
			{
				return $this->arrayToJsObject($array,$json);
			}

		public	function makeExpireBar($settings = false)
			{
				if(!is_loggedin())
					return $this;

				// Javascript setting
				$expire		=	(isset($settings['expire']))? $settings['expire'] : $this->getSessExpTime();
				$warn		=	(isset($settings['warn_at']) && is_numeric($settings['warn_at']))? $settings['warn_at'] : 120;
				$msg		=	(isset($settings['message']))? addslashes($settings['message']) : '<div class="nbr_expire_bar">SESSION WILL EXPIRE SOON.</div>';
				$onClick	=	(!empty($settings['on_click']))? $settings['on_click'] : $this->siteUrl().$this->getPage('full_path');
				$onReload	=	(!empty($settings['on_reload']))? $settings['on_reload'] : $onClick;
				$append		=	(isset($settings['append']))? $settings['append'] : "body";
				
				$wrap		=	(isset($settings['wrap']))? $settings['wrap'] : true;
				$dReady		=	(isset($settings['doc_ready']))? $settings['doc_ready'] : true;
				$class		=	(isset($settings['class']))? $settings['class'] : "nbr_expire_bar";
				$addJq		=	(isset($settings['jqlib']) && is_bool($settings['jqlib']))? $settings['jqlib'] : false;
				$jqobj		=	(isset($settings['jqobj']) && is_bool($settings['jqobj']))? $settings['jqobj'] : '$';
				$objName	=	(!empty($settings['objName']))? preg_replace('/[^a-zA-Z\.\_]/',"",$settings['objName']) : "nXpireBar";
				
				$filter[]	=	'expire';
				$filter[]	=	'warn_at';
				$filter[]	=	'message';
				$filter[]	=	'on_click';
				$filter[]	=	'on_reload';
				$filter[]	=	'append';
				
				$options	=	array();
				
				if(is_array($settings)) {
					foreach($settings as $keys => $values) {
						if(!in_array($keys,$filter))
							continue;
						
						$options[$keys]	=	$values;
					}
				}
				
				$settings	=	$options;
				
				ob_start();
				
				if($addJq) {
					echo $this->defaultJQuery();
				}
				
				if($wrap) {
?>
<script>
<?php			}
		
				if($dReady) {
?>
<?php echo $this->jName; ?>(document).ready(function() {
<?php			}
?>	// See user is logged in
	var is_loggedout	=	(typeof <?php echo $this->jName; ?>(".fullsite").html() === "undefined");
	// If the menu bar is present,<br>
	// run the bar
	if(!is_loggedout) {
		// Create new instance
		var <?php echo $objName;?>	=	new nExpire();
		// Create settings
		var nExpireSettings	=	<?php echo $this->makeObject($settings); ?>;
		// Initialize
		<?php echo $objName;?>.setStart(<?php echo $expire; ?>).execute(nExpireSettings);
	}
<?php			if($dReady) {
?>		});
<?php			}
		
				if($wrap) {
?></script>
<?php			}
		
				$this->data[]	=	ob_get_contents();
				ob_end_clean();
				return $this;
			}
		/*
		** @description	This method creates a container for a scroll-to-top button. The mechanism is already built into the onthefly.js
		** @param	$settings	[array] Allows for custom settings to the container
		*/
		public	function nScroller($settings = false)
			{
				$wrap	=	(!empty($settings['wrap']))? $settings['wrap']: 'div';
				$img	=	(!empty($settings['img']))? $settings['img']: $this->siteUrl()."/media/images/ui/arrowup.png";
				$class	=	(!empty($settings['class']))? $settings['class'].' ':'';
				$id		=	(!empty($settings['id']))? $settings['id']:"nScroller";
				ob_start();
?><<?php echo $wrap; ?> class="<?php echo $class.'scroll-top'; ?>" id="<?php echo $id; ?>"><img src="<?php echo $img; ?>" /></<?php echo $wrap; ?>>
<?php			$data	=	ob_get_contents();
				ob_end_clean();
				
				return $data;
			}
		
		public	function getHandle()
			{
				return (!empty($this->jName))? $this->jName : 'njQuery';
			}
		
		public	function saveDocument($name = '/client/js/myScripts.js')
			{
				// File path
				$build = NBR_ROOT_DIR.str_replace(NBR_ROOT_DIR,"",$name);
				// Path info for file
				$path	=	pathinfo($build);
				// See if directory exists
				if(!$this->isDir($path['dirname'])) {
					// Try making it
					if(!$this->isDir($path['dirname'],true))
						return false;
					else {
						// Load the create file
						$this->autoload(array('create_htaccess'));
						// Create htaccess file
						create_htaccess(array('rule'=>'server_r','dir'=>$path['dirname']));
						// If not made register error
						if(!file_exists($path['dirname'].'/.htaccess'))
							$this->saveIncidental('htaccess_write',array('error'=>'Failed to write htaccess file. Possibly permissions.'));
					}
				}
				
				if(file_exists($build))
					unlink($build);
					
				$content	=	trim(rtrim(ltrim(trim(self::call('Safe')->decode($this->getResults())),'<script>'),'</script>'));

				$this->autoload(array("write_file"));
				write_file(array('content'=>$content,'save_to'=>$build));
				
				if(is_file($build))
					$this->saveIncidental('javascript_write',array('success'=>true));
			}
		
		public function __toString()
			{
				return $this->getResults();
			}
	}