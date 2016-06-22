<?php
if(!function_exists('AutoloadFunction'))
	die('Direct linking not allowed.');
	
// Builds one-level array from registry file
function build_at_menu()
	{
		// Get registry
		$aRegMenu	=	nApp::getRegistry();
		// Assign array
		if(!empty($aRegMenu['admintoolsmenu'])) {
			$array		=	(!empty($aRegMenu['admintoolsmenu']['menu']))? $aRegMenu['admintoolsmenu']['menu'] : array($aRegMenu['admintoolsmenu']);
		}
		// If empty just return empty
		if(empty($array))
			return array();
		// Loop through array
		AutoloadFunction("use_markup");
		foreach($array as $menu) {
			if(empty($menu['name']) || empty($menu['name']))
				continue;
				
			$new[use_markup($menu['name'])]	=	use_markup($menu['url']);
		}
		
		return (!empty($new))? $new : array();
	}

AutoloadFunction('render_masthead,ValidateToken,check_install,PaginationInitialize,PaginationSearchBar,PaginationCounter,PaginationLimits,PaginationResults,jQuery_scroll_top,Input,get_directory_list,fetch_plugins,fetch_admin_link,create_dropdown_nav,create_query_string,fetch_token,default_jQuery,render_element_css,render_element_js');

echo get_header(array("head"=>false)); ?>
<head profile="http://www.w3.org/2005/10/profile">
<?php
echo render_meta().PHP_EOL;
echo nApp::getFavicons().PHP_EOL;
echo default_jQuery().PHP_EOL;
echo render_element_css(ROOT_DIR.'/css/').PHP_EOL;
echo render_element_js(ROOT_DIR.'/js/',false).PHP_EOL;
// Fetch plugins
$plugins	=	fetch_plugins();
// Loop through the array if there are plugins or javascripts
if(!empty($plugins['admin_local'])) {
	foreach($plugins['admin_local'] as $hincludes) {
		if(preg_match('/\.css$/',$hincludes)) {
?>
<link rel="stylesheet" href="<?php echo $hincludes; ?>" />
<?php	}
		elseif(preg_match('/\.js$/',$hincludes)) {
?><script src="<?php echo $hincludes; ?>"></script>
<?php	}
	}
}
?>
<style>
.footerContainer {
	padding: 0;
}
html {
	overflow: -moz-scrollbars-horizontal;
	overflow-x: scroll;
}
body {
	background-color: #444;
}
div.wrapper {
	display: block;
	postion: absolute;
	top: 0;
	left: 0;
	bottom: 0;
	right: 0;
}
#admin_menu_wrap	{
	background-color: #000;
	background: linear-gradient(360deg, #000,#555);
	display: inline-block;
	border-top: 1px solid #666;
	border-bottom: 1px solid #111;
	width: 100%;
	text-align: center;
}
#primary_nav_wrap	{
	margin: 0 auto;
	margin-bottom: -4px;
	display: inline-block;
	z-index: 1;
	position: relative;
}
#primary_nav_wrap ul {
	list-style:none;
	float:left;
	margin:0;
	padding:0
}
#primary_nav_wrap ul a {
	display:block;
	color:#FFF;
	text-decoration:none;
	font-weight:700;
	font-size:14px;
	line-height:32px;
	padding:0 15px;
	font-family:"HelveticaNeue","Helvetica Neue",Helvetica,Arial,sans-serif;
	text-shadow: 1px 1px 3px #000;
}
#primary_nav_wrap ul a:hover	{
	color: #333;
	text-shadow: none;
}
#primary_nav_wrap ul li {
	position:relative;
	float:left;
	margin:0;
	padding:0;
}
#primary_nav_wrap ul li.current-menu-item {
	background:#ddd;
}
#primary_nav_wrap ul li:hover {
	background:#CCC;
	box-shadow: inset 0 0 4px rgba(0,0,0,0.5);
}
#primary_nav_wrap ul ul {
	display: none;
	position: absolute;
	top:100%;
	left:0;
	background: #900;
	box-shadow: 1px 1px 4px rgba(0,0,0,0.5); 
	padding:0;
}
#primary_nav_wrap ul ul li {
	float:none;
	width:200px;
	border-bottom: 1px solid #000;
}
#primary_nav_wrap ul ul a {
	line-height:120%;
	padding:10px 15px;
}
#primary_nav_wrap ul ul ul {
	top:0;
	left:100%;
}
#primary_nav_wrap ul li:hover > ul {
	display:block;
}
html	{
	overflow-x: hidden;
}
.allblocks	{
	text-align: center;
}
</style>
</head>
<body class="nbr">
<?php

//************ Create the top/side bar tools *****************************//
//************************************************************************//
// Add the admintools bar
echo render_admintools(array("toolbar"=>build_at_menu($aRegMenu)));

//************ See if there reinstall is requested ***********************//
//************************************************************************//
$force_install	=	check_install(); // Returns true if reinstall set to go

//************ Convert wysiwyg object ************************************//
// Activate, deactivate tinymce
$GET			=	(isset($_GET))? (object) $_GET : false;
//************************************************************************//
if(isset($GET->wysiwyg)) {
		if(isset($_SESSION['wysiwyg']) && $GET->wysiwyg == 'off')
			unset($_SESSION['wysiwyg']);
		elseif($GET->wysiwyg == 'on')
			$_SESSION['wysiwyg']	=	true;
	}
	
// Sanitize Variables
$table	=	NubeData::$settings->engine->table;
?>
    <div id="content" class="nbr_wrapper">
	<div id="first-start"></div>
<!------ HEADER IMAGE ------>
<?php echo render_masthead(); ?>
		<div id="admincontent">
	<?php
	// Activate tinyMCE
	if(isset($_SESSION['wysiwyg'])) {
			AutoLoadFunction('TinyMCE');
			echo TinyMCE(true);
		}
		
	if(is_admin()) { ?>
        <div style="display: block; background-color: #CCC;box-shadow: inset 0 0 6px rgba(0,0,0,0.6); text-align: center;">
		<?php
			//********************************************************//
			//************ Create instance of admin tools ************//
			$_Layout			=	new AdminToolsMaster();
			//************ Create Token for installing db ************//
			$token_reinstall	=	(!isset($_SESSION['token']['reinstall']))? fetch_token('reinstall'):$_SESSION['token']['reinstall'];
			//********************************************************//
			//************ Settings for the menu items ***************//
			// Additional menus
			$additions['insert']['nUberSoft']["Page_Editor"]	=	$pg_editor;
			$additions['insert']['nUberSoft']["Updater"]		=	'?reinstall='.rtrim($token_reinstall.create_query_string(array("reinstall","command"),$_GET),"&");
			$additions['no_menu']								=	false;
			$additions['insert_where']							=	true;
			
			//************ END Settings for the menu items************//
			//********************************************************// ?>
			<div id="admin_menu_wrap">
				<?php echo create_dropdown_nav($additions); ?>
			</div>
			<div style="display: inline-block; margin: 0 auto;">
				<?php $_Layout->Plugins($plugins['admin_root']); ?>
			</div>
			<?php
			//********************************************************//
			//************ Pagination Settings ***********************//
			$settings['table'] 		=	nApp::getTableName();
			$settings['spread'] 	=	2;
			$settings['admin'] 		=	true;
			$settings['max_range']	=	"2,5,10,20,50,100";
			$settings['layout']		=	"/core.processor/renderlib/admintools.pagination.results.php";
			$settings['submit']		=	"SEARCH";
			
			//********************************************************//
			//************ END Pagination Settings *******************//
			
			// Initialize Pagination
			PaginationInitialize($settings);
			
			// Admintools core installer
			$guts		=	new coreInstaller(); ?>
            <div class="hideall" id="loadWindow_panel">
                <div id="loadWindow">
                    <?php $guts->execute(); ?>
                </div>
			</div>
        </div>
		<div id="tersert"></div>
		<div class="hideall" id="AdToolsWindow_panel" style="text-align: center; display: block; border-top: 5px solid red; background-color: #FFF; <?php if(!isset($_GET['page_editor'])) { ?>padding: 30px 0;<?php } ?>">
		<?php if(isset($_GET['page_editor'])) { ?>
			<div class="allblocks" style="padding: 0; max-width: none; text-align: left;">
				<?php
				AutoloadFunction('app_text_editor');
				app_text_editor();
				?>
			</div>
<?php 			}
			else {
?>
			<div class="allblocks" style="text-align: center;">
				<ul id="nbr_searchbar_cont" style="text-align: center; margin: 0 auto; padding: 0;">
					<li id="nbr_search_bar"><?php echo PaginationSearchBar(array("submit"=>$settings['submit'])); ?></li>
					<li id="nbr_search_counter"><?php echo PaginationCounter(); ?></li>
					<li id="nbr_search_limits"><?php echo PaginationLimits(array('max_range'=>$settings['max_range'])); ?></li>
					<li id="nbr_search_results" style="text-align: center; margin: 0 auto; list-style: none;">
						<div style="text-align: center; overflow: auto; box-shadow: inset 0 0 20px rgba(0,0,0,0.6); width: 100%; margin: 0 auto; display: inline-block; background-color: #EBEBEB; border: 1px solid #888; border-top: none; padding-bottom: 30px;">
							<div style="display: inline-block; margin: auto;"><?php echo PaginationResults($settings); ?></div>
						</div>
					</li>
					<li id="nbr_search_core" style="text-align: center; margin: 0 auto; list-style: none;"><?php AutoloadFunction('render_core'); echo render_core(); ?></li>
				</ul>
            </div>
			<?php } ?>
        </div>
	</div>
	
	<?php	//	global $_dbrun;
			//	printpre($_dbrun);
			 }
        else { ?>
        <table style="width: 100%; height: 100%; vertical-align: middle;">
            <tr>
                <td style="width: inherit; text-align: center;">
                    <div style="width: 100%; max-width: 300px; display: inline-block; margin: 0 auto;">
                    <?php
					if(nApp::siteValid()) {
							$nubquery	=	nQuery();
							if($nubquery != false) {
									// Check if users exist in database
									$_checkuser	=	$nubquery->select("COUNT(*) as count")->from("users")->fetch();
									if($_checkuser != 0) { 
?>
				<div class="nbr_general_form">
					<form id="remote" enctype="application/x-www-form-urlencoded" method="post">
						<?php AutoloadFunction('fetch_token'); ?>
						<input type="hidden" name="token[login]" value="<?php echo fetch_token('login'); ?>" />
                    	<input type="hidden" id="action" name="action" value="login" />
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" />
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" />
                        <div class="nbr_button"><input disabled="disabled" type="submit" name="login" value="Login" style="margin-right: 0px; margin-top: 10px;" /></div>
                    </form>
			</div>
<?php									}
								}
						} ?>
                    </div>
                </td>
            </tr>
        </table>
	<?php }  ?>

		</div>
<?php
if($force_install) { ?>
<span class="js_trigger" data-instruct="install" style="display: none;"></span>
<?php }

global $_error;
if(isset($_error)) {
	function Implodify($array, $key = false)
		{
			foreach($array as $keys => $values)
				return (is_array($values))? "<br />".ucwords($keys).">".Implodify($values,$keys) : htmlentities(ucwords($values),ENT_QUOTES);
		}  ?>
<span class="js_trigger" data-instruct="errors" style="display: none;"><?php echo Implodify($_error); ?></span>
<?php } ?>
	</div>
	<div style="padding: 30px; background: linear-gradient(#111,#444); text-align: center; font-size: 14px; color: #FFF;">
		Copyright &reg;<?php echo date("Y"); ?> nUbersoft.
	</div>
<?php jQuery_scroll_top(); ?>
</body>
</html>
<?php echo printpre($_SESSION);
unset($_SESSION['q']);
?>