<?php
if(!function_exists('AutoloadFunction'))
	die('Direct linking not allowed.');
include(__DIR__._DS_.'inclusions'._DS_.'config.admin.php');
$nAdminTools	=	new Nubersoft\nAdminTools(
						new Nubersoft\configFunctions(new Nubersoft\nAutomator()),
						new Nubersoft\nFunctions()
					);
$nAdminTools	->useConfigs(NBR_CLIENT_DIR);

if(!\nApp::onWhiteList($_SERVER['REMOTE_ADDR']))
	die('Invalid request.');

echo get_header(array("head"=>false)).PHP_EOL;
// Start a cache engine
echo '<head profile="http://www.w3.org/2005/10/profile">'.PHP_EOL;
echo render_meta().PHP_EOL;
echo nApp::getFavicons();
echo default_jQuery();
echo render_element_js(NBR_ROOT_DIR._DS_.'js'._DS_,false);
echo render_element_js(NBR_ROOT_DIR._DS_.'core.plugins'._DS_);
echo render_element_js(NBR_CLIENT_DIR._DS_.'js'._DS_,false);
echo render_element_css(NBR_ROOT_DIR._DS_.'css'._DS_);
echo render_element_css(NBR_ROOT_DIR._DS_.'core.plugins'._DS_);
echo (new \Nubersoft\nView())->getStyles();
$cache	=	nApp::cacheEngine();
// If there is a header file, just get it. If not, make it
//$cache->checkCacheFile(nApp::getCacheFolder()._DS_.'admin'._DS_.'header.html')->startCaching();
//if($cache->allowRender()) {
	
//}
//$cache->endCaching()->addContent($cache->getCached())->renderBlock();

	// Fetch plugins
	$plugins	=	fetch_plugins();
?>
</head>
<body class="nbr">
<?php

//************ Create the top/side bar tools *****************************//
//************************************************************************//
// Add the admintools bar
echo render_admintools(array("toolbar"=>build_at_menu()));

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
$table	=	nApp::getDefaultTable();
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
			$additions['insert']['Nubersoft']["Updater"]		=	'?reinstall='.rtrim($token_reinstall.create_query_string(array("reinstall","command"),$_GET),"&");
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
			$settings['layout']		=	_DS_."core.processor"._DS_."renderlib"._DS_."admintools.pagination.results.php";
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
				// See if there are any on-table loads
				$layout	=	$nAdminTools->getAdminToolsOnTable(nApp::getDefaultTable())->isValid();
				// echo printpre($nAdminTools->getConfigs());
				// If there is a valid layout
				if($layout) {
					// Include
					$nAdminTools->includeByTableName();
				}
				else {
?>			<div class="allblocks" style="text-align: center;">
				<ul id="nbr_searchbar_cont" style="text-align: center; margin: 0 auto; padding: 0;">
					<li id="nbr_search_bar"><?php echo PaginationSearchBar(array("submit"=>$settings['submit'])); ?></li>
					<li id="nbr_search_counter"><?php echo PaginationCounter(); ?></li>
					<li id="nbr_search_limits"><?php echo PaginationLimits(array('max_range'=>$settings['max_range'])); ?></li>
					<li id="nbr_search_results" style="text-align: center; margin: 0 auto; list-style: none;">
						<div style="text-align: center; overflow: auto; box-shadow: inset 0 0 20px rgba(0,0,0,0.6); width: 100%; margin: 0 auto; display: inline-block; background-color: #EBEBEB; border: 1px solid #888; border-top: none; padding-bottom: 30px;">
							<div style="display: inline-block; margin: auto;"><?php echo PaginationResults($settings); ?></div>
						</div>
					</li>
					<li id="nbr_search_core" style="text-align: center; margin: 0 auto; list-style: none;">
						<?php AutoloadFunction('render_core'); echo render_core(); ?>
					</li>
				</ul>
            </div>
<?php 			}
			}
?>        </div>
	</div>
	
	<?php	//	global $_dbrun;
			//	printpre($_dbrun);
		}
        else {
?>        <table style="width: 100%; height: 100%; vertical-align: middle;">
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
?>				<div class="nbr_general_form">
					<form id="remote" enctype="application/x-www-form-urlencoded" method="post">
						<input type="hidden" name="token[login]" value="<?php echo fetch_token('login'); ?>" />
                    	<input type="hidden" id="action" name="action" value="login" />
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" />
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" />
                        <input type="text" name="token[nProcessor]" value="<?php echo nApp::getSetToken('nProcessor','admintools',true); ?>" />
                        <div class="nbr_button"><input disabled="disabled" type="submit" name="login" value="Login" style="margin-right: 0px; margin-top: 10px;" /></div>
                    </form>
			</div>
<?php						}
						}
					}
?>
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