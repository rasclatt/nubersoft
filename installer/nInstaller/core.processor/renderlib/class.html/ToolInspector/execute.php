<?php if(!function_exists("AutoloadFunction")) return; ?>
<div id="nbr_toolpallet_constr_wrap">
	<div id="nbr_toolpallet_box">
		<table style="display: inline-block;" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td>
					<div id="add_menu" class="nbr_tool_panel">

						<?php
						// Just sets the dropdowns
						self::UpdateCurrentMenu()->SetDropDowns($this->dropdowns);
						// Because we use an extended, needs both set
						self::CreateNewMenu()->SetDropDowns($this->dropdowns);
						// If on a main menu, write a header
						if(isset(NubeData::$settings->page_prefs->ID)) {
						?>
							
						<h3 class="toolBox nbrAccordion">Menu/Directory Options</h3>
						<div id="main_currMenu" class="nbr_tool_panel_opts">
							<div class="CB_MainMenu_BlockHD">
								<span class="CB_MainMenu_Hdr"><?php echo preg_replace('/[^0-9a-zA-Z\.\-\_]/','',Safe::decode(NubeData::$settings->page_prefs->menu_name)); ?></span>
							</div>
							<?php self::UpdateCurrentMenu()->component(array(NubeData::$settings->page_prefs), 'main_menus', 'page_builder', 'full_path'); ?>
						</div>

						<?php } ?>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<!----------------------- ADD NEW MAIN MENU ----------------------->
					<div class="nbr_tool_panel">
						<h3 class="toolBox nbrAccordion">Create New Menu/Directory</h3>
						<div id="main_addMenu" class="nbr_tool_panel_opts">
							<?php self::CreateNewMenu()->component(array(), 'main_menus', 'page_builder', 'full_path'); ?>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<!--------------------- SHOW ALL MAIN MENUS ------------------------->
					<div class="nbr_tool_panel">
						<h3 class="toolBox nbrAccordion" id="sub_showMenuToggle">All Menus/Directories</h3>
						<div id="sub_showMenu" class="nbr_tool_panel_opts">
							<?php $this->MenuTable(array('table'=>'main_menus','order'=>'menu_name'),false); ?>
						</div>
					</div>
				</td>
			</tr> 
		</table>
	</div>
</div>
</div>
</div>