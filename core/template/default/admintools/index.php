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
	<div id="loadspot_modal"></div>
	<div id="content" class="nbr_wrapper">
		<div id="admincontent">
			<div style="height: 50px; overflow: visible;">
				<?php echo $this->get3rdPartyHelper('\nPlugins\Nubersoft\InspectorPallet')->execute(array('ID'=>$this->getPage('ID'))) ?>
			</div>
			<div style="text-align: center;">
				<?php
				if($this->isAdmin()) {
				?>
				<div style="background-color: #333; margin-bottom: 15px; background-image: url('<?php echo $this->imagesUrl('/core/small_latice.jpg') ?>'); text-align: left;">
					<?php
					echo $this->useTemplatePlugin('button_user_deck') ?>
					<div style="display: inline-block; height: 80px; width: 5px; background-color: #888; margin: 0 12px;"></div>
						<?php echo $this->useTemplatePlugin('admintool_user_buttons') ?>
				</div>
				<?php
				}
				?>
				<?php echo $this->useTemplatePlugin('admintool_layouts',"logged{$layout}.php") ?>
			</div>
		</div>
	</div>
	<?php
	if($this->isLoggedIn()) { ?>
	<div id="foot_cache_block">
	<?php } ?>
		<footer class="nbr_foot">
			<?php echo $this->render(__DIR__.DS.'foot.php'); if($this->isAdmin()) echo 'My Ip: '.$this->getClientIp().'. Database: "'.$this->getDbName().'"' ?>
		</footer>
	<?php
	if($this->isLoggedIn()) { ?>
	</div>
	<?php } ?>
<span class="nListener" data-instructions='{"action":"nbr_get_email_receipt_count"}'></span>
</body>
</html>