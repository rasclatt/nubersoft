<?php
$load	=	array(
	'site_url',
	"default_jquery",
	"render_element_css",
	"render_element_js",
	"render_meta",
	"render_link_rel",
	"jquery_scroll_top",
	"render_style_block",
	"javascript_expire_bar",
	"javascript_array_to_obj",
	"render_javascript"
);

$nFunc		=	$this->autoload($load);
$encoded	=	$this->getHelper('Safe')->encOpenSSL($this->getPageLike('login'));
$expirebar['on_reload']	=	$this->siteUrl().$this->getPage()->full_path.'?jumppage='.$encoded."&action=logout";
$expirebar['expire']	=	$this->getSessExpTime();
$expirebar['warn_at']	=	120;
$expirebar['objName']	=	'nBar';
$expirebar['wrap']		=	false;
$expirebar['doc_ready']	=	false;
include_once(__DIR__.DS.'include.php');