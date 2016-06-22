<?php
// Do a check just incase .htaccess file is not in place
if(!function_exists('AutoloadFunction'))
	die('Direct linking not allowed.');
// Include template settings
include(__DIR__._DS_.'inclusions'._DS_.'config.php');
// Include page header
echo get_header(array("head"=>false));
// Start a cache engine
$cache	=	nApp::cacheEngine();
// If there is a header file, just get it. If not, make it
$cache->checkCacheFile(nApp::getCacheFolder()._DS_.'template'._DS_.nApp::getUserId().'header.html')->startCaching();
// If there is no header file, render contents of the header
// (everything between braces will be stored in cache)
if($cache->allowRender()) {
	// Render all the header settings
	echo render_meta();
?><head profile="http://www.w3.org/2005/10/profile">
<link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
<?php
	echo nApp::getFavicons();
	echo default_jQuery();
	echo render_element_css(NBR_ROOT_DIR.'/css/');
	echo render_element_js(NBR_ROOT_DIR.'/js/',false);
	echo render_element_js(NBR_CLIENT_DIR.'/js/',false);
	echo render_link_rel();
	echo render_style_block();
}
// Stop the caching, save the contents into a file, render the block
// This portion is all irrelavent if file exists
// (for instance ->getCached() will be empty)
$cache->endCaching()->addContent($cache->getCached())->renderBlock();
?>
</head>
<!-- START BODY -->
<body class="nbr">
<?php echo render_admintools(); // Admin tool bar ?>
<?php ChangePassword(); // Changes the password ?>
	<div id="content" class="nbr_wrapper">
		<?php echo render_masthead(); // Create a standard head ?>
		<?php echo get_menubar(); // Create a standard menu bar ?>
		<div id="maincontent">
			<?php echo login_window(__DIR__._DS_."dialogue.php"); // Check to see if page requires a login ?>
			<?php echo render_contentcached(); // Renders the content. This function will toggle cached-enable pages ?>
			<?php echo render_error(array("display"=>nApp::getErrorTemplate())); // Renders placent of not-found errors ?>
		</div>
	</div>
<?php
$cache->checkCacheFile(nApp::getCacheFolder()._DS_.'template'._DS_.'footer.html')->startCaching();
if($cache->allowRender()) {
	echo render_footer();
	echo jQuery_scroll_top();
}
$cache->endCaching()->addContent($cache->getCached())->renderBlock();
echo nApp::getQueryCount();
//AutoloadFunction("delete_contents");
//delete_contents(nApp::getCacheFolder()._DS_.'template'._DS_);
?>
</body>
</html>