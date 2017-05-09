<?php
AutoloadFunction("render_masthead,ValidateToken,check_install,PaginationInitialize,PaginationSearchBar,PaginationCounter,PaginationLimits,PaginationResults,jQuery_scroll_top,Input,get_directory_list,fetch_plugins,fetch_admin_link,create_dropdown_nav,create_query_string,fetch_token,javascript_expire_bar,javascript_array_to_obj,render_javascript,fetch_token");
include_once(__DIR__.DS.'config.php');
// Builds one-level array from registry file
function build_at_menu()
	{
		// Get registry
		$aRegMenu	=	nApp::getRegistry();
		// Assign array
		if(!empty($aRegMenu['admintoolsmenu'])) {
			$array		=	(!empty($aRegMenu['admintoolsmenu']['menu']))? $aRegMenu['admintoolsmenu']['menu'] : array($aRegMenu['admintoolsmenu']);
		}
		// If empty just return empty
		if(empty($array))
			return array();
		// Loop through array
		AutoloadFunction("use_markup");
		foreach($array as $menu) {
			if(empty($menu['name']) || empty($menu['name']))
				continue;
				
			$new[use_markup($menu['name'])]	=	use_markup($menu['url']);
		}
		
		return (!empty($new))? $new : array();
	}
	
function getProcessingErrors()
	{
		if(empty(nApp::getIncidental()->nQuery))
			return false;
			
		$errs	=	organize(Safe::to_array(nApp::getIncidental()->nQuery),'success',true);
		
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
?>	errors.push(<?php echo nApp::jsEngine()->makeObject($eArr); ?>);
<?php	}
?>	console.log(errors);
});
</script>
<?php	$data	=	ob_get_contents();
		ob_end_clean();
		return $data;
	}