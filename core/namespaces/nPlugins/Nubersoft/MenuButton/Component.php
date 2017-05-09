<?php
$baseImg	=	NBR_MEDIA_IMAGES.DS.'core'.DS;
$nImage		=	$this->getHelper('nImage');
$bOpts		=	array(
				'action'=>'nbr_menu_button_edit',
				'loader'=>'<div class="nbr_loader" style="background-color: #666; border-radius: 4px;"></div>',
				'load_into'=>'#comp_'.$this->_id,
				'data'=>array(
					'deliver'=>array(
						'query_data'=>array(
							'ID'=>((!empty($_payload->ID))? $_payload->ID : false),
							'parent_id'=>$parent_id,
							'ref_spot'=>'sub_menu'
						),
						'send_back'=>'#comp_'.$this->_id
					)
				),
				'FX'=>array(
					'acton'=>array(
						'.menu_set_pop',
						'#'.$this->_id.'_panel',
						'#stick_it_'.$parent_id.'_panel'
					),
					'fx'=>array(
						'hide',
						'fadeIn',
						'addClass'
					)
				)
			);

# Used to show the live-status of component
$actBtn	=	($_unique)? $nImage->image(
							$baseImg.'led_'.(($_live)? 'green': 'red').'.png',
							array('style'=>'max-width: 20px; margin: -5px 0px -5px -5px; position: relative; top: -2px;')
						) : '';
# Lets user know if the component is available to them
$lockBtn	=	($_locked)? $nImage->image($baseImg.'lock.png') : '';

?>
<div class="templateWrap compWrapper">
	<div>
		<div style="background-color: rgba(0,0,0,0.8); padding: 20px;">
			
			<?php echo $lockBtn ?>
			<?php if($_unique) { ?>
				<a class="nbr_button nTrigger" href="#" id="<?php echo $this->_id; ?>" data-instructions='<?php echo json_encode($bOpts) ?>'><?php echo $actBtn ?>EDIT</a>
			<?php } ?>
	<?php
	if($_unique)
		echo (!empty($stripText))? '<br /><span style="color: #FFF;">'.substr($stripText, 0, 50).'</span>': $addComponent;
	else {
	?>
	<a class="nTrigger" href="#" id="<?php echo $this->_id; ?>" data-instructions='<?php echo json_encode($bOpts) ?>'><?php echo $actBtn ?><?php	echo $nImage->image(NBR_MEDIA_IMAGES.DS.'core'.DS.'icn_add.png') ?></a>
	<?php
	}
	?>
		</div>
	</div>
	<!--WRAPPER-->
	<div class="dragonit">
		<!--WRAPPER-->
		<div class="templatePopup menu_set_pop" id="<?php echo $this->_id; ?>_panel">
			<!--WINDOW BAR-->
			<div class="form_window_bar30px" style="width: 100%; text-align: center; position: relative;" >
				<div class="closer_button nTrigger" data-instructions='{"FX":{"fx":["fadeOut","removeClass"],"acton":["#<?php echo $this->_id; ?>_panel","<?php echo "#stick_it_{$parent_id}_panel" ?>"]}}'></div>
				<div class="component_header">MENU COMPONENT</div>
			</div>
			<!--POP UP WRAPPER-->
			<div class="nodrag" style="background-color: rgba(0,0,0,0.0); background-image: none; ">
				<div class="SubMenuPopUp" style=" <?php if(isset($curr['component_type']) && $curr['component_type'] == 'image') { ?>padding-right: 20px;<?php } ?>">
					<div id="test_<?php echo $this->_id; ?>">
						<?php
							$comp_id	=	(isset($_unique))? $_unique: false;
							$page_id	=	(isset($_unique))? $_unique: false;
							
							# Secure bind statement
							if($page_id != false) {
								$data	=	$this->nQuery()
												->select()
												->from("components")
												->where(array("unique_id"=>$comp_id))
												->fetch();
								if(isset($data[0]))
									$data	=	$data[0]; 
							}
							else
								$data	=	0;
						?>
						<div id="comp_<?php echo $this->_id; ?>"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>