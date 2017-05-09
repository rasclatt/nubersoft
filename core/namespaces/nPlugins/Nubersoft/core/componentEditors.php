<?php
$nImage		=	$this->getHelper('nImage');
$unique_id	=	$this->getPage('unique_id');
$settings	=	$this->getCompSettings($this->curr,$unique_id);
$compIdVal	=	'#comp_settings_cont'.$this->uId;
$spinner	=	'<div class="nbr_loader" style="background-color: #666; border-radius: 4px;"></div>';
$ID			=	(isset($this->curr['ID']))? $this->curr['ID'] : false;
$data		=	array(
	'action'=>'ajax_admintools_component',
	'load_into'=>"#test_".$this->uId,
	'loader'=>$spinner,
	'FX'=>array(
		'acton'=>array(
			$compIdVal
		),
		'fx'=>array(
			'fadeIn'
		)
	),
	'data'=>array(
		'deliver'=>array(
			'send_back'=>"#test_".$this->uId,
			'query_data'=>array_merge(array('ID'=>$ID,'ref_spot'=>'nbr_layout'),$this->toArray($settings->sVars))
			)
		)
	);
?>	<table cellpadding="0" cellspacing="0" border="0" class="preview_tble">
    	<tr>
        	<td<?php if($settings->is_new) { ?> style="border-color: red;"<?php } ?>>
				<div class="componentSetWrap" <?php if($settings->aTag || $settings->bImg) echo 'style="'.$settings->aTag.$settings->bImg.'"'; ?>>
<?php 			if($settings->aNotes) {
?>					<div class="notes_comp">
						<div class="notes_popup">
							<?php echo $settings->admin_notes; ?>
						</div>
					</div>
<?php 			}
				
?>					<div class="nbr_comp_elems"><?php echo implode('<br />'.PHP_EOL,$settings->attr); ?></div>
					<div class="component_block nTrigger" data-instructions='<?php echo json_encode($data) ?>'>
                		<table style=" display: block;" cellpadding="0" cellspacing="0" border="0">
                    		<tr>
<?php 			# If not adding a new component, show the live status button (red / green)
				if(!$this->addNew) {
?>								<td>
									<?php echo $nImage->image($settings->sIcon, array('style'=>'float: left; width: 20px;'),false,false) ?>		
								</td>
<?php 			}
?>								<td style="text-align: center; <?php if($this->addNew) { ?> display: block;<?php } ?>">
<?php 			# If not new component, show kind
				if(!$this->addNew) {
?>									<?php echo $nImage->image($settings->icon, array('style'=>'width: 25px;','alt'=>'test'),false,false) ?>
<?php			}
				# Show plus [+] icon for adding new component
				else {
?>									<?php echo $nImage->image(NBR_MEDIA_IMAGES.DS.'core'.DS.'icn_add.png', array('style'=>'max-height: 30px; margin: 0 auto;')) ?>
<?php 			}
?>								</td>
							</tr>
						</table>
					</div>
					<!--WRAPPER-->
					<div class="dragonit"><!--WRAPPER-->
						<div class="templatePopup" id="comp_settings_cont<?php echo  $this->uId; ?>" style="min-width: 400px;">
						<!--WINDOW BAR-->
							<div class="form_window_bar30px">
								<div class="closer_button nTrigger nodrag" data-instructions='{"FX":{"acton":["<?php echo  $compIdVal; ?>"],"fx":["fadeOut"]}}'></div>
								<div class="component_header">
									<?php echo $this->get3rdPartyHelper('\nPlugins\Nubersoft\TrackEditor',__DIR__)->getComponentHeader() ?>
								</div>
							</div>
							<!--POP UP WRAPPER-->
							<div class="nodrag" style="background-color: rgba(0,0,0,0.0); background-image: none; width: 100%;">
								<div class="SubMenuPopUp" style=" display: block;<?php if($this->checkEmpty($this->curr,'component_type','image')) { ?>padding-right: 20px;<?php } ?>">
									<div id="test_<?php echo $this->uId; ?>">
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
				<?php
				$no_prev	=	array('div','row');
				$comp_type	=	(!empty($this->curr['component_type']))? $this->curr['component_type'] : false;
				if(!in_array($comp_type,$no_prev)) {
				?>
                <div class="previewer">
                    <p class="nbr_preview_toggle nTrigger" data-instructions='{"FX":{"acton":["next::fadeToggle"],"fx":["fadeToggle"],"speed":["200"]}}'>Preview</p>
                    <div class="render_preview"<?php if($settings->is_img) { ?> style="display: block;"<?php } ?>>
                    	<div class="preview_window">
                    		<?php
                            if(!empty($this->curr['content']))
								echo '<pre style="font-size: 13px; padding: 20px; text-align: left;">'.wordwrap($this->curr['content'],80,"\n",true).'</pre>';
							else {
								# Check if component is an image
								if(!empty($this->curr['file_path'])) {
									$path	=	(!empty($this->curr['file_path']))? $this->curr['file_path'].$this->curr['file_name']:false;
									# If is an image and valid, use preview
									if($path)
										$img_valid	=	(is_file($localImg = NBR_ROOT_DIR.$path))? $nImage->image($localImg,array('style'=>'max-width: 200px;')):"File is missing!";
								}
								
								$img_valid	=	(!isset($img_valid))? "No image set" : $img_valid;
								$label		=	($settings->is_img)? $img_valid:"Unlablled";
						?>
									<div style="width: 100%;"><?php echo $label; ?></div>
						<?php
						}
						?>
                    	</div>
                    </div>
                </div>
				<?php
				}
				?>
            </td>
		</tr>
	</table>