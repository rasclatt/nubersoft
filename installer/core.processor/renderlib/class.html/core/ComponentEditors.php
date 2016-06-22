<?php
AutoloadFunction("check_empty");
$unique_id	=	(!empty($unique_id))? $unique_id : false;
$settings	=	self::getCompSettings(self::$curr,$unique_id);
?>	<table cellpadding="0" cellspacing="0" border="0" class="preview_tble">
    	<tr>
        	<td<?php if($settings->is_new) { ?> style="background-color: red;"<?php } ?>>
				<div class="componentSetWrap" <?php if($settings->aTag || $settings->bImg) echo 'style="'.$settings->aTag.$settings->bImg.'"'; ?>>
<?php 			if($settings->aNotes) {
?>					<div class="notes_comp">
						<div class="notes_popup">
							<?php echo $settings->admin_notes; ?>
						</div>
					</div>
<?php 			}
				
?>					<div class="nbr_comp_elems"><?php echo implode('<br />'.PHP_EOL,$settings->attr); ?></div>
					<div class="cDispatcher component_block" data-action="autoset" data-returned="html" data-use="ajax_admintools_component" sendto="#test_<?php echo  self::$uId; ?>" data-vars='<?php echo json_encode($settings->sVars); ?>'>
                		<table style=" display: block;" cellpadding="0" cellspacing="0" border="0">
                    		<tr>
<?php 			// If not adding a new component, show the live status button (red / green)
				if(!self::$addNew) {
?>								<td>
									<img src="<?php echo $settings->sIcon; ?>" style="float: left; width: 20px;" />		
								</td>
<?php 			}
?>								<td style="text-align: center; <?php if(self::$addNew) { ?> display: block;<?php } ?>">
<?php 			// If not new component, show kind
				if(!self::$addNew) {
?>									<img src="<?php echo $settings->icon; ?>" style="width: 25px;" />
<?php			}
				// Show plus [+] icon for adding new component
				else {
?>									<img src="<?php echo site_url(); ?>/core_images/core/icn_add.png" style="max-height: 30px; margin: 0 auto;" />
<?php 			}
?>								</td>
							</tr>
						</table>
					</div>
					<!--WRAPPER-->
					<div class="dragonit"><!--WRAPPER-->
						<div class="templatePopup" id="comp_settings_cont<?php echo  self::$uId; ?>" style="min-width: 400px;">
						<!--WINDOW BAR-->
							<div class="form_window_bar30px">
								<div class="closer_button" onClick="ShowHide('#comp_settings_cont<?php echo  self::$uId; ?>','','fade')"></div>
								<div class="component_header">
									<?php
										if(!empty(self::$curr['content'])) {
											if(strlen($title = html_entity_decode(self::$curr['content'],ENT_QUOTES)) > 20)
												echo htmlentities(substr($title,0,20), ENT_QUOTES)."...";
											else
												echo self::$curr['content'];
										}
										else
											echo "COMPONENT SETTINGS"; ?></div>
								</div>
							<!--POP UP WRAPPER-->
							<div class="nondrag" style="background-color: rgba(0,0,0,0.0); background-image: none; width: 100%;">
								<div class="SubMenuPopUp" style=" display: block;<?php if(check_empty(self::$curr,'component_type','image')) { ?>padding-right: 20px;<?php } ?>">
									<div id="test_<?php echo self::$uId; ?>">
										<!-- container to load component data -->
										<div class="component_loader"></div>
									</div>
								</div>
							</div>
						</div><!---->
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="preview_td">
                <div class="previewer">
                    <p class="nbr_preview_toggle">Preview</p>
                    <div class="render_preview"<?php if($settings->is_img) { ?> style="display: block;"<?php } ?>>
                    	<div class="preview_window">
                    		<?php
                            if(!empty(self::$curr['content']))
								echo '<pre style="font-size: 13px; padding: 20px; text-align: left;">'.wordwrap(self::$curr['content'],80,"\n",true).'</pre>';
							else {
								// Check if component is an image
								if($settings->is_img) {
									$path		=	(!empty(self::$curr['file_path']))? self::$curr['file_path'].self::$curr['file_name']:false;
									// If is an image and valid, use preview
									
									if($path != false)
										$img_valid	=	(is_file(NBR_ROOT_DIR.$path))? '<img src="'.$path.'" style="max-width: 200px;" />':"File is missing!";
									else
										$img_valid	=	"No image set";
								}	
								
								$label	=	($settings->is_img)? $img_valid:"Unlablled"; ?>
									<div style="width: 100%;"><?php echo $label; ?></div>
<?php						}
							
                            if($settings->is_img && !empty(self::$curr['file_path'])) {
								if(is_file(NBR_ROOT_DIR.self::$curr['file_path'].self::$curr['file_name'])) { ?>
									<img src="<?php echo site_url().self::$curr['file_path'].self::$curr['file_name']; ?>" style="max-width: 300px;" />
<?php 							}
							}
?>                    	</div>
                    </div>
                </div>
            </td>
		</tr>
	</table>