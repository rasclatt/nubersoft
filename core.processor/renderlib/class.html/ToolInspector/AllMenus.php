<?php if(!function_exists("AutoloadFunction")) return; ?>
<div class="nbr_tool_dir<?php if($count_dirs > 1) echo "_multi"; ?>">
	<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td class="nbr_pagelive_led">
				<?php echo display_toggle_icon($data['page_live']); ?>
			</td>
			<td class="nbr_tool_fullpath_link">
				<p>
					<a href="<?php echo $data['full_path']; ?>"><span style="font-size: 16px;<?php $col = ($count_dirs > 1)? ' color: #999; text-shadow: 1px 1px 2px #000;':''; echo $col; ?>"><?php echo preg_replace('/[^0-9a-zA-Z\.\-\_]/','',ucwords(Safe::decode($data['menu_name']))); ?></span></a>
				</p>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="nbr_tools_miniedit">
				<div class="showsub" id="quickedit<?php echo $data['ID']; ?>" onClick="ShowHide('#quickedit<?php echo $data['ID']; ?>_panel','quickedit<?php echo $data['ID']; ?>','slide')">
					<div class="quickfullpath">EDIT: <?php echo $data['full_path']; ?></div>
				</div>
				<div class="nbr_tool_panel_wrap displayNone" id="quickedit<?php echo $data['ID']; ?>_panel">
					<form method="post" enctype="multipart/form-data">
						<input type="hidden" name="requestTable" value="<?php echo fetch_table_id($this->_table); ?>" />
						<input type="hidden" name="command" value="page_builder" />
						<?php Input($data,'ID',"","fullhide",$this->dropdowns); ?>
						<?php Input($data,'unique_id',"","fullhide",$this->dropdowns); ?>
						<div class="fieldsGrad tools-mini-edit">
							<label for="link">
							<?php Input($data,'link','width: 80px',"text",$this->dropdowns); ?>
							</label>
							<label for="page_order">
							Menu Order (If in menu)
							<?php Input($data,'page_order','width: 40px',"text",$this->dropdowns); ?>
							</label>
						</div>
						<div class="tools-mini-edit">
							<label for="parent_id">
								Parent Folder
								<div style="display: inline-block; width: 100%;">
									<?php AutoloadFunction('render_url_dropdown'); echo render_url_dropdown($data['unique_id']); ?>
								</div>
							</label>
							<label for="session_status">
							Login Required?
							<?php Input($data,'session_status','width: 80px',"select",$this->dropdowns); ?>
							</label>
							<label for="usergroup">
							User Group
							<?php Input($data,'usergroup','width: 80px',"select",$this->dropdowns); ?>
							</label>
							<label for="auto_cache">
							<?php echo display_toggle_icon($data['auto_cache']); ?>&nbsp;Page-Cached Status
							<?php Input($data,'auto_cache','width: 80px',"select",$this->dropdowns); ?>
							</label>
							<label for="page_live">
							<?php echo display_toggle_icon($data['page_live']); ?>&nbsp;Page-Live Status
							<?php Input($data,'page_live','width: 80px',"select",$this->dropdowns); ?>
							</label>
						</div>
						<div class="nbr_button"><input disabled="disabled" type="submit" name="update" value="SAVE" /></div>
					</form>
				</div>
			</td>
		</tr>
	</table>
</div>