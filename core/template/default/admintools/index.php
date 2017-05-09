<?php
/*
**	Set some preferences for use down the page
**	$frontEnd	=	$this->getFrontEnd();
*/
$cacheBase	=	DS.'pages'.DS.'admintools';
$headCache	=	$cacheBase.DS.'header.html';
$footCache	=	$cacheBase.DS.$this->isLoggedIn().DS.'footer.html';
$layout		=	($this->isAdmin())? 'in' : 'out';
# Include some frontend/backend files
include($this->getBackEnd(DS.'inclusions'.DS.'config.admin.php'));
# Just make sure user is on whitelist if set to use
$this->useTemplatePlugin('whitelist');
# Render/cached head block
echo $this->cacheBlock($this->getDoc('head.php'),$headCache);
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
				<?php echo $this->useTemplatePlugin('admintool_layouts',"logged{$layout}.php") ?>
			</div>
		</div>
	</div>
	<?php
	if($this->isLoggedIn()) { ?>
	<div id="foot_cache_block">
	<?php } ?>
		<?php echo $this->cacheBlock($this->getFooter(),$footCache); ?>
	<?php
	if($this->isLoggedIn()) { ?>
	</div>
	<?php } ?>
</body>
</html>