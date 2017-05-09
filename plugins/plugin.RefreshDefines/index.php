<?php
/*
** @param [post var] action = rebuild_reg
*/

if(!function_exists("is_admin"))
	return;
elseif(!is_admin())
	return;

$run	=	nApp::getIncidental('rebuild_reg');
$txt	=	(!empty($run->{0}))? '<span style="color: blue;">'.strtoupper($run->{0}).'!</span>' :'REBUILD DEFINES';
?>
<form method="post" action="?requestTable=<?php echo (nApp::getGet('requestTable'))? nApp::getGet('requestTable') : 'users'; ?>">
	<input type="hidden" name="action" value="rebuild_reg" />
	<input type="hidden" name="token[nProcessor]" value="<?php echo nApp::nToken()->getSetToken('nProcessor',array('refreshdefines',rand(1000,9999)),true); ?>" />
	<button style="display: inline-block; background-color: transparent; border: none; margin-top: 11px; cursor: pointer;"><img src="<?php echo site_url().str_replace(NBR_ROOT_DIR,"",__DIR__.'/images/rebuild.png'); ?>" style="max-height: 55px;" />
<div style="font-size: 10px;"><?php echo $txt; ?></div></button>
</form>