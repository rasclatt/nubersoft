<div class="templateWrap compWrapper">
	<div class="component_block compblockmod" id="<?php echo $this->_id; ?>" onclick="ShowHide('#<?php echo $this->_id; ?>_panel','','fade'); AjaxSimpleCall('comp_<?php echo $this->_id; ?>','/ajax/component.menu.php?parent_id=<?php echo $parent_id; ?>')" >
		<table style="margin-left: 5px;">
			<tr>
				<td>
					<?php if($_unique) { ?>
					<img src="/images/core/led_<?php echo ($_live == true)? 'green': 'red'; ?>.png" style="float: left; width: 20px;" />
					<?php } ?>
				</td>
				<td>
					<?php if($_locked == true) { ?><img src="/images/core/lock.png" style="width: 25px;" /><?php } ?>
				</td>
			</tr>
		</table>
		<div style="width: 100%; overflow: hidden;">
			<center>
				<?php if($_file == true) { ?><img src="<?php echo $_payload['file_path'].$_payload['file_name']; ?>" style="width: 96%; float: right; margin: 0 2%;" /><?php } ?>
				<div class="component_desc">
					<?php
						if($_unique)
							echo (!empty($stripText))? substr($stripText, 0, 50): $addComponent;
						else { ?><img src="/images/core/icn_add.png" /><?php } ?>
				</div>
			</center>
		</div>
	</div>
	<!--WRAPPER-->
	<div class="dragonit">
		<!--WRAPPER-->
		<div class="templatePopup" id="<?php echo $this->_id; ?>_panel">
			<!--WINDOW BAR-->
			<div class="form_window_bar30px" style="width: 100%; text-align: center; position: relative;" >
				<div class="closer_button nCloser" data-closewhat="#<?php echo $this->_id; ?>_panel"></div>
				<div class="component_header"><?php echo $addComponent; ?></div>
			</div>
			<!--POP UP WRAPPER-->
			<div class="nondrag" style="background-color: rgba(0,0,0,0.0); background-image: none; ">
				<div class="SubMenuPopUp" style=" <?php if(isset($curr['component_type']) && $curr['component_type'] == 'image') { ?>padding-right: 20px;<?php } ?>">
					<div id="test_<?php echo $this->_id; ?>">
						<?php
							$comp_id	=	(isset($_unique))? $_unique: false;
							$page_id	=	(isset($_unique))? $_unique: false;
							
							// Secure bind statement
							if($page_id != false) {
									$data	=	nQuery()	->select()
															->from("menu_display")
															->where(array("unique_id"=>$comp_id))
															->fetch();
									if(isset($data[0]))
										$data	=	$data[0]; 
								}
							else
								$data	=	0; ?>
						<div id="comp_<?php echo $this->_id; ?>"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>