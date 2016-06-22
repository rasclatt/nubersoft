<?php
/*
** @param [post var] action = rebuild_reg
** @description	Rebuilds htaccess files based on the config.xml file
*/

if(!function_exists("is_admin"))
	return;

if(!is_admin())
	return;
$run	=	nApp::getIncidental('rebuild_htaccess');
$txt	=	(!empty($run->{0}) && $run->{0} == 'run')? '<span style="color: green;">REBUILT!</span>' :'REBUILD HTACCESS';
?><form method="post" action="">
	<input type="hidden" name="action" value="rebuild_htaccess" />
	<button style="display: inline-block; background-color: transparent; border: none; margin-top: 11px; cursor: pointer;"><img src="<?php echo site_url().str_replace(ROOT_DIR,"",__DIR__.'/images/icn.png'); ?>" style="max-height: 55px;" />
<div style="font-size: 10px;"><?php echo $txt; ?></div></button>
</form>
