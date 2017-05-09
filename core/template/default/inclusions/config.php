<?php
AutoloadFunction("default_jQuery,render_element_css,render_element_js,render_meta,render_link_rel,jQuery_scroll_top,render_style_block,javascript_expire_bar,javascript_array_to_obj,render_javascript");

$expirebar['on_reload']	=	site_url().nApp::getPage('full_path').'?jumppage='.Safe::encOpenSSL(nApp::getPageLike('login'))."&action=logout";
$expirebar['expire']	=	nApp::getSessExpTime();
$expirebar['warn_at']	=	120;
$expirebar['objName']	=	'nBar';
$expirebar['wrap']		=	false;
$expirebar['doc_ready']	=	false;
include_once(__DIR__.DS.'include.php');