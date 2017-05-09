<?php
include_once($this->getFrontEnd(DS.'inclusions'.DS.'config.php'));
if(!function_exists('build_at_menu')) {
// Builds one-level array from registry file
function build_at_menu($nApp)
	{
		// Get registry
		$aRegMenu	=	$nApp->getRegistry();
		// Assign array
		if(!empty($aRegMenu['admintoolsmenu'])) {
			$array		=	(!empty($aRegMenu['admintoolsmenu']['menu']))? $aRegMenu['admintoolsmenu']['menu'] : array($aRegMenu['admintoolsmenu']);
		}
		// If empty just return empty
		if(empty($array))
			return array();
		// Loop through array
		$nApp->autoload("use_markup");
		foreach($array as $menu) {
			if(empty($menu['name']) || empty($menu['name']))
				continue;
				
			$new[use_markup($menu['name'])]	=	use_markup($menu['url']);
		}
		
		return (!empty($new))? $new : array();
	}
}
if(!function_exists('getProcessingErrors')) {
function getProcessingErrors($nApp)
	{
		if(empty($nApp->getIncidental()->nquery))
			return false;
			
		$errs	=	$nApp->organizeByKey($nApp->toArray($nApp->getIncidental()->nquery),'success',true);
		
		if(empty($errs['fail']))
			return false;
		
		ob_start();
?>
<script>
njQuery(document).ready(function() {
	var	errors	=	[];
<?php
		foreach($errs['fail'] as $error) {
			$eArr	=	json_decode($error['error'],true);
?>	errors.push(<?php echo $nApp->jsEngine()->makeObject($eArr); ?>);
<?php	}
?>	console.log(errors);
});
</script>
<?php	$data	=	ob_get_contents();
		ob_end_clean();
		return $data;
	}
}