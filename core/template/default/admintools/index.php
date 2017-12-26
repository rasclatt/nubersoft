<?php
/*
**	Set some preferences for use down the page
**	$frontEnd	=	$this->getFrontEnd();
*/

//echo printpre($this->getDataNode('workflow_run'));
//$this->nQuery()->query("update users set `user_status` = 'on' where `username` = 'ryan@mxicorp.com'");

$thisObj	=	$this;
$cacheBase	=	$this->getStandardPath();
$headCache	=	$cacheBase.DS.'header.html';
$footCache	=	$cacheBase.DS.$this->isLoggedIn().DS.'footer.html';
$layout		=	($this->isAdmin())? 'in' : 'out';
# Include some frontend/backend files
include($this->getBackEnd(DS.'inclusions'.DS.'config.admin.php'));
# Just make sure user is on whitelist if set to use
$this->useTemplatePlugin('whitelist');

if(is_file($headCache))
	unlink($headCache);

echo $this->getTemplateDoc('head.php','admintools');
# Render/cached head block
//echo $this->cacheLayout($headCache,function($thisObj) {
//	return $thisObj->getHeader('admintools');
//});
# Save my data if not yet done
$this->getHelper('CoreMySQL')->saveDatabaseScheme(NBR_ROOT_DIR.DS.'installer'.DS.'sql');
?>
<body class="nbr_admintools<?php if($this->isAdmin()) echo '_loggedin' ?>">
<!-- MODAL -->
<div id="loadspot_modal"></div>
<!-- CONTENT -->
<div id="content" class="nbr_wrapper">
	<!-- ADMIN CONTENT -->
	<?php echo $this->useTemplatePlugin('admintools',(($this->isAdmin())? DS.'logged_in.php' : DS.'logged_out.php')) ?>
	<!-- END ADMIN CONTENT -->
</div>
</body>
</html>