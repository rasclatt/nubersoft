<?php
/*
** @param [post var] action = rebuild_reg
*/

if(!function_exists("is_admin"))
	return;

if(!is_admin())
	return;

$run	=	nApp::getIncidental('rebuild_reg');
$txt	=	(!empty($run->{0}))? '<span style="color: blue;">'.strtoupper($run->{0}).'!</span>' :'REBUILD DEFINES';
?>
<form method="post" action="">
	<input type="hidden" name="action" value="rebuild_reg" />
	<button style="display: inline-block; background-color: transparent; border: none; margin-top: 11px; cursor: pointer;"><img src="<?php echo site_url().str_replace(ROOT_DIR,"",__DIR__.'/images/rebuild.png'); ?>" style="max-height: 55px;" />
<div style="font-size: 10px;"><?php echo $txt; ?></div></button>
</form>