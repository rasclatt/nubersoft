<?php
// echo printpre(NubeData::$settings);
if(!function_exists('AutoloadFunction'))
	die('Direct linking not allowed.');
AutoloadFunction("get_default_meta,default_jQuery,render_element_css,render_element_js,render_meta,render_link_rel,jQuery_scroll_top,render_style_block");
global $_incidental; // load by-the-way errors ?>
<?php echo get_header(array("head"=>false)).PHP_EOL; ?>
<head profile="http://www.w3.org/2005/10/profile">
<link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
<?php
echo nApp::getFavicons().PHP_EOL;
echo default_jQuery().PHP_EOL;
echo render_element_css(ROOT_DIR.'/css/').PHP_EOL;
echo render_element_js(ROOT_DIR.'/js/',false).PHP_EOL;
echo render_meta().PHP_EOL;
echo render_link_rel().PHP_EOL;
echo render_style_block().PHP_EOL;
?>
</head>
<!-- START BODY -->
<body class="nbr">
<?php echo render_admintools(); ?>
<?php ChangePassword(); // Changes the password ?>
	<div id="content" class="nbr_wrapper">
		<?php echo render_masthead(); // Create a standard head ?>
		<?php echo get_menubar(); // Create a standard menu bar ?>
		<div id="maincontent">
			<?php echo login_window(__DIR__."/dialogue.php"); // Check to see if page requires a login ?>
			<?php echo render_contentcached(); // Renders the content. This function will toggle cached-enable pages ?>
			<?php echo render_error(array("display"=>NubeData::$settings->site->error_404)); // Renders placent of not-found errors ?>
		</div>
	</div>
	<?php echo render_footer(); ?>
	<?php echo jQuery_scroll_top(); ?>
</body>
</html>