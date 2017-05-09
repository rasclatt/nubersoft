<?php
if(!function_exists('is_admin'))
	return;
if(!is_admin())
	return;
$dir			=	(!empty(nApp::getDataNode('site')->cache_folder))? nApp::getDataNode('site')->cache_folder : NBR_ROOT_DIR.CACHE_DIR;
$valid_dir		=	is_dir($dir);
$valid_start	=	$valid_dir;
$try_delete		=	(isset(NubeData::$incidentals->cache_delete));
$icn			=	"";
if($try_delete)
	echo printpre(NubeData::$incidentals->cache_delete);
// If no folder
if(!$valid_dir) {
	// If no folder, but was folder else empty
	$icn	=	($valid_start)? "_success":"_empty";
}
?>	<div id="plugin_DeleteCache_wrap">
		<div class="button_trigger" data-instructions='{"action":"nbr_cache_delete","data":{"deliver":{"requestTable":"<?php if(nApp::getGet('requestTable')) echo nApp::getGet()->requestTable; ?>","toggle":"<?php echo ($valid_dir)? "on":"off"; ?>","nProcessor":"<?php echo nApp::nToken()->getSetToken('nProcessor',array('deletecache',rand(1000,9999)),true); ?>"}}}' style="background-image: url(/images/buttons/deleteCache<?php echo $icn; ?>.png); <?php if($valid_dir) { ?>cursor: pointer;<?php } ?>"><?php
if(isset(\NubeData::$incidentals->delete_cache->{0}->success)) {
	$success	=	\NubeData::$incidentals->delete_cache->{0}->success;
	echo '<div style="font-size: 10px; margin-top: 60px;">';
	echo ($success)? '<span style="color: green;">REFRESHED</span>' : '<span style="color: red;">ERROR</span>';
	echo '</div>';
}
?>
		</div>
	</div>