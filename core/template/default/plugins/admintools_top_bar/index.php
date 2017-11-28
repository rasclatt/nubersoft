<?php
if(!$this->isAdmin())
	return;

$buttons	=	(!empty($buttons))? $buttons : false;
$nImage		=	$this->getHelper('nImage');
$nHtml		=	$this->getHelper('nHtml');
$imgPath	=	NBR_ROOT_DIR.DS.'media'.DS.'images'.DS.'buttons';
$qLinks		=	$this->getDirList(array("dir"=>NBR_CLIENT_DIR.DS.'settings'.DS.'toolpallet.quicklinks'.DS,"type"=>array("php")));
$admin_link	=	$this->toArray($this->getAdminPage());
$Inspector	=	$this->getPlugin('\nPlugins\Nubersoft\InspectorPallet');
?>

<!-- START TOP BAR -->
<div class="toggle_bar_wrap nbr_ux_element">
	<div class="col-count-<?php echo (is_array($buttons))? '7' : '6' ?> toggle_bar_cont">
		<!-- GEAR ICON -->
		<div class="nbr_tool_bar_menu nTrigger" id="nbr_tooltoggle" data-instructions='{"FX":{"fx":["sideSlide"],"acton":["#InspectorPalletWrap"],"fxspeed":["fast"]}}'></div>
		<!-- TABLES LINKS -->
		<div class="nbr_tool_bar_menu col-count-2 push-5 med">
			<div class="span-2">
				<a href="<?php echo $this->localeUrl($admin_link['full_path']) ?>" class="admintools-menu"><?php echo $admin_link['menu_name']; ?></a>
			</div>
			<div class="span-2">
				<div class="admin_tools_popup">
					<ul class="admin_tools_menu_cont">
						
						<?php echo $Inspector->AdminToolsQuickLinks($admin_link); ?>
						
					</ul>
				</div>
			</div>
		</div>
		<!-- TOGGLE BUTTON -->
		<div class="nbr_tool_bar_menu push-col-6 medium">
			<?php echo $Inspector->button(); ?>
		</div>
		<!-- CACHE BUTTON -->
		<div class="nbr_tool_bar_menu nTrigger push-col-6 medium" data-instructions='{"FX":{"fx":["opacity"],"acton":["body"]},"action":"nbr_delete_cache_folder_ajax","DOM":{"html":["<img src=\"/media/images/ui/loader.gif\" style=\"max-height: 15px; margin: 0 0 0 5px;\" />"],"sendto":[".nbr_loader_now"],"event":["click"]},"data":{"deliver":{"ux_loader_reset":[".nbr_loader_now"]}}}'>
			<a href="#" class="admintools-menu">Delete Cache<span class="nbr_loader_now"></span></a>
		</div>
		<!-- SITE PREFS BUTTON -->
		<div class="nbr_tool_bar_menu nTrigger push-col-6 medium" data-instructions='{"action":"nbr_open_site_prefs","FX":{"acton":["body"],"fx":["opacity"]},"DOM":{"html":["<img src=\"/media/images/ui/loader.gif\" style=\"max-height: 15px; margin: 0 0 0 5px;\" />"],"sendto":[".nbr_loader_prefs"],"event":["click"]},"data":{"deliver":{"ux_loader_reset":[".nbr_loader_prefs"]}}}'>
			<a href="#" class="admintools-menu">Site Preferences<span class="nbr_loader_prefs"></span></a>
		</div>
		
		<?php if(is_array($buttons)) { ?>
		<!-- ADDITIONAL BUTTONS -->
		<div class="nbr_tool_bar_menu push-col-6 medium">
			<a href="#" class="admintools-menu">MORE...</a>
			<div class="admin_tools_popup">
				<ul class="admin_tools_menu_cont">	
<?php				foreach($buttons as $name => $link) {
?>					<li><a href="<?php echo $link; ?>" ><?php echo ucwords(str_replace("_"," ",strtolower($name))); ?></a></li>
<?php				}
?>				</ul>
			</div>
		</div>
<?php	} ?>
		
		<!-- LOGOUT BUTTON -->
		<div class="nbr_tool_bar_menu push-col-6 medium">
			<form action="" method="post">
				<input type="hidden" name="action" value="logout" />
				<button style="font-family: inherit; background-color: transparent; color: inherit; font-size: inherit; border: none; padding: 10px; cursor: pointer;">Welcome <span class="fullsite"><?php echo $_SESSION['first_name']; ?>.</span> Logout?</button>
			</form>
		</div>
	</div>
</div>
<!-- END TOP BAR -->

<div id="InspectorPalletWrap" class="nbr_ux_element" data-subfx='{"sideSlide":{"speed":"1000","data":{"width":"toggle"}}}'>
	<div id="InspectorPalletPrefs">
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<?php
				# Fetch buttons from xml file
				foreach($Inspector->getXmlInterfaces() as $button) {
				?>
				<td>
					<?php echo $button ?>
				</td>
				<?php
				}
				?>
			</tr>
		</table>
		<div class="nbr_closer_small nTrigger" data-instructions='{"FX":{"fx":["sideSlide"],"acton":["#InspectorPalletWrap"],"fxspeed":["1000"]}}'></div>
	</div>
	<div id="InspectorPalletCont">
		<div id="InspectorPalletMenuOpts">
			<?php (new \nPlugins\Nubersoft\ToolInspector())->execute($Inspector->getPageId()); ?>
		</div>
	</div>
</div>
<script>
$('#nbr_tooltoggle').animate({width: '50px' }, 500);
</script>