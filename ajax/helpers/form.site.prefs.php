<?php
if(!$this->isAdmin()) {
	$msg	=	($this->isAjaxRequest())? json_encode(array('alert'=>$this->getAdminTxt())) : '<h2 class="nbr_block_error">'.$this->getAdminTxt().'</h2>';
	die($msg);
}

ob_start();
$nApp					=	\Nubersoft\nApp::call();
# Get all prefs
$siteArr				=	$this->getSitePrefs();
$headerArr				=	$this->getHeader();
$footerArr				=	$this->getFooter();
# Assign prefs
$site_vals['site']		=	$siteArr;
$site_vals['header']	=	$headerArr;
$site_vals['footer']	=	$footerArr;
$site_vals				=	$this->toObject($site_vals);
# Get content from each
# Same as $this->getSitePrefs()->content
$site					=	$nApp->getSiteContent();
$header					=	$nApp->getHeaderContent();
$footer					=	$nApp->getFooterContent();
$nProcToken				=	$this->getHelper('nToken')->getSetToken('nProcessor',array('formsiteprefs',rand(1000,9999)),true);
# Sets and returns a token
$this->saveSetting('nProcessor', $nProcToken);
# Render site prefs
echo $this->useData(array(
	'token'=>$nProcToken,
	'content'=>array(
		'site'=>$site,
		'header'=>$header,
		'footer'=>$footer
	),
	'settings'=>$site_vals
	))->useTemplatePlugin('site_preferences');

$html	=	ob_get_contents();
ob_end_clean();

die(json_encode(array(
	'html'=>array($html),
	'sendto'=>array('#loadspot_modal'),
	"acton"=>array("#loadspot_modal",'body'),
	"fx"=>array("slideDown",'rOpacity')
	)));