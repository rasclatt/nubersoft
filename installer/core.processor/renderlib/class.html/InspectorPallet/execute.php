<?php
if(!function_exists("AutoloadFunction")) return;
	AutoloadFunction("fetch_admin_link,get_directory_list,site_url");

$qLinks		=	get_directory_list(array("dir"=>NBR_CLIENT_DIR.'/settings/toolpallet.quicklinks/',"type"=>array("php")));
$admin_link	=	Safe::to_array(nApp::getAdminPage());
?>
<nav class="toggle_bar_wrap">
	<div class="toggle_bar_cont">
	
		<div class="nbr_tool_bar_menu" id="nbr_tooltoggle"></div>
	
		<div class="nbr_tool_bar_menu">
			<a href="<?php echo site_url().$admin_link['full_path']; ?>?requestTable=users"><?php echo $admin_link['menu_name']; ?></a>
			<div class="admin_tools_popup">
				<ul class="admin_tools_menu_cont">	
					<?php self::AdminToolsQuickLinks($admin_link); ?>
				</ul>
			</div>
		</div>
	
		<div class="nbr_tool_bar_menu">
			<?php self::Button(); ?>
		</div>
		
		<div class="nbr_tool_bar_menu ajaxtrigger" data-gopage="cache.delete" data-gopagekind="g" data-gopagesend="dir=<?php echo $cachedel = str_replace('==','',base64_encode(nApp::getGlobalArr('site','cache_folder'))); ?>">
			<a href="#">Delete Cache</a>
		</div>
		
		<div class="nbr_tool_bar_menu ajaxtrigger" data-gopage="form.site.prefs" data-gopagekind="g" data-gopagesend="edit=true" >
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
			<a href="?action=logout" >Welcome <span class="fullsite"><?php echo $_SESSION['first_name']; ?>.</span> Logout?</a>
		</div>
		<div id="nbrDocSize"></div>
	</div>
</nav>
<!--</div>-->
<div id="InspectorPalletWrap">
		<div id="InspectorPalletPrefs">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td>
						<div class="site_builder ajaxtrigger" data-gopage="form.site.prefs" data-gopagekind="g" data-gopagesend="edit=true" >
							<img src="/core_images/buttons/pagePrefs.png" />
						</div>
					</td>
					<td>
						<div class="site_builder ajaxtrigger" data-gopage="cache.delete" data-gopagekind="g" data-gopagesend="dir=<?php echo $cachedel; ?>">
							<img src="/core_images/buttons/deleteCache.png" />
						</div>
					</td>
					<td>
						<div class="site_builder">
							<a href="<?php echo $admin_link['full_path']; ?>?page_editor=true"><img src="/core_images/buttons/icn_page_edit.png" /></a>
						</div>
					</td>
<?php
					if(!empty($qLinks['host'])) {
						$qLinksCnt	=	count($qLinks['host']);
						for($i = 0; $i < $qLinksCnt; $i++) {
?>					<td>
						<?php include($qLinks['host'][$i]); ?>
					</td>
<?php 					}
					}
?>				</tr>
			</table>
			<div class="nbr_closer_small" data-closewhat="#InspectorPalletWrap"></div>
		</div>
	<div id="InspectorPalletCont">
		<div id="InspectorPalletMenuOpts">
			<?php self::AdminToolsPallet()->execute(); ?>
		</div>
	</div>
</div>