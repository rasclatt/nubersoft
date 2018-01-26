<?php
$instr	=	json_encode(array(
				"FX"=>array(
					"fx"=>array("slideUp","slideToggle"),
					"acton"=>array(".nbr_tool_panel_opts","next::accordian"),
					"fxspeed"=>array("fast","fast")
				)
			));

$page	=	$this->toArray($this->nQuery()
				->query("select * from `main_menus` where `ID` = :0",array($ID))
				->getResults(true));
?>
<div id="nbr_toolpallet_constr_wrap nbr_ux_element">
	<div id="nbr_toolpallet_box">
		<table style="display: inline-block;" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td>
					<div id="add_menu" class="nbr_tool_panel">
						<?php
						if(!empty($ID)) {
						?>
						
						<h3 class="toolBox nTrigger" data-instructions='<?php echo $instr ?>'>Menu/Directory Options</h3>
						<div id="main_currMenu" class="nbr_tool_panel_opts">
							<div class="CB_MainMenu_BlockHD">
								<span class="CB_MainMenu_Hdr"><?php echo preg_replace('/[^0-9a-zA-Z\.\-\_]/','',$this->safe()->decode($page['menu_name'])); ?></span>
							</div>
							<?php echo $this->setDisplayLayout('ToolInspector')->display($page); ?>
						</div>

						<?php } ?>
					</div>
				</td>
			</tr><tr>
				<td>
					<div id="add_menu" class="nbr_tool_panel">
						<h3 class="toolBox nTrigger" data-instructions='<?php echo $instr ?>'>Create Menu</h3>
						<div id="main_currMenu" class="nbr_tool_panel_opts">
							<?php echo $this->setDisplayLayout('ToolInspector')->display(); ?>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="nbr_tool_panel">
						<h3 class="toolBox nTrigger" data-instructions='<?php echo $instr ?>' id="sub_showMenuToggle">All Menus/Directories</h3>
						<div id="sub_showMenu" class="nbr_tool_panel_opts">
							<?php  $this->setErrorMode(1); echo $this->getHelper('View\Menus')->getHtml('div','div','quickjump-menu',function($row,$obj){
								return '<a class="nbr button small green" href="'.$obj->localeUrl($row['full_path']).'">'.$row['menu_name'].'</a>';
							}) ?>
						</div>
					</div>
				</td>
			</tr> 
		</table>
	</div>
</div>