<?php
function ajax_delete_cache()
	{
		
		if(!is_admin())
			return false;
		
		$checkFolder	=	function($dir)
			{
				$filter	=	array(".","..");
				$inDir	=	scandir($dir);
				$fCont	=	array_diff($inDir,$filter);
				return (!empty($fCont));
			};
		
		ob_start();
?>
<div class="nbr_errorbox">
<?php 	// Delete cache
		$dir		=	(defined("CACHE_DIR"))? str_replace("//","/",ROOT_DIR.'/'.CACHE_DIR) : nApp::getSite('cache_folder');
		$directory	=	(is_dir($dir));
		$hasCache	=	$checkFolder($dir);
		
		if(empty($dir))
			echo 'Cache folder not set. You must define it in the registry.xml file, then refresh the DEFINES: /client_assets/settings/registry.xml';
		elseif($directory && $hasCache) {
				DeleteEngine::addTarget($dir);
				echo (!$checkFolder($dir))? 'Cache has been cleaned out!' : 'An error occurred. Perhaps your permissions are not correct.';
		}
		else
			echo 'Cache already clean out.';
?>
</div>
<script>
	$('.nbr_errorbox').delay(100).fadeIn('slow');
	$('.nbr_errorbox').delay(1500).slideUp('fast');
</script>
<?php 
		$data	=	ob_get_contents();
		ob_end_clean();
		
		return $data;
	}