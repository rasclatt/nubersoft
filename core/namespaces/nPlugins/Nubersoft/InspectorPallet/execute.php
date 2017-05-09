<?php
$nImage		=	$this->getHelper('nImage');
$nHtml		=	$this->getHelper('nHtml');
$imgPath	=	NBR_ROOT_DIR.DS.'media'.DS.'images'.DS.'buttons';
$qLinks		=	$this->getDirList(array("dir"=>NBR_CLIENT_DIR.DS.'settings'.DS.'toolpallet.quicklinks'.DS,"type"=>array("php")));
$admin_link	=	$this->toArray($this->getAdminPage());
?>
<nav class="toggle_bar_wrap nbr_ux_element">
	<div class="toggle_bar_cont">
		<div class="nbr_tool_bar_menu nTrigger" id="nbr_tooltoggle" data-instructions='{"FX":{"fx":["sideSlide"],"acton":["#InspectorPalletWrap"],"fxspeed":["fast"]}}'></div>
		<div class="nbr_tool_bar_menu">
			<a href="<?php echo $this->siteUrl().$admin_link['full_path']; ?>?requestTable=users"><?php echo $admin_link['menu_name']; ?></a>
			<div class="admin_tools_popup">
				<ul class="admin_tools_menu_cont">
					<?php echo $this->AdminToolsQuickLinks($admin_link); ?>
				</ul>
			</div>
		</div>
		<div class="nbr_tool_bar_menu">
			<?php echo $this->button(); ?>
		</div>
		
		<div class="nbr_tool_bar_menu nTrigger" data-instructions='{"action":"nbr_delete_cache_folder_ajax"}'>
			<a href="#">Delete Cache</a>
		</div>
		
		<div class="nbr_tool_bar_menu nTrigger" data-instructions='{"action":"nbr_open_site_prefs","FX":{"acton":["body"],"fx":["opacity"]}}'>
			<a href="#">Site Preferences</a>
		</div>
		<?php if(is_array($buttons)) { ?>
		<div class="nbr_tool_bar_menu">
			<a href="#">MORE...</a>
			<div class="admin_tools_popup">
				<ul class="admin_tools_menu_cont">	
<?php				foreach($buttons as $name => $link) {
?>					<li><a href="<?php echo $link; ?>" ><?php echo ucwords(str_replace("_"," ",strtolower($name))); ?></a></li>
<?php				}
?>				</ul>
			</div>
		</div>
<?php	} ?>
		<div class="nbr_tool_bar_menu">
			<form action="" method="post">
				<input type="hidden" name="action" value="logout" />
				<button style="font-family: inherit; background-color: transparent; color: inherit; font-size: inherit; border: none; padding: 10px; cursor: pointer;">Welcome <span class="fullsite"><?php echo $_SESSION['first_name']; ?>.</span> Logout?</button>
			</form>
		</div>
		<div id="nbrDocSize"></div>
	</div>
</nav>
<!--</div>-->
<div id="InspectorPalletWrap" class="nbr_ux_element" data-subfx='{"sideSlide":{"speed":"1000","data":{"width":"toggle"}}}'>
		<div id="InspectorPalletPrefs">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<?php
					# Fetch buttons from xml file
					foreach($this->getXmlInterfaces() as $button) {
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
			<?php (new \nPlugins\Nubersoft\ToolInspector())->execute($this->getPageId()); ?>
		</div>
	</div>
</div>
<script>
$('#nbr_tooltoggle').animate({width: '50px' }, 500);
</script>