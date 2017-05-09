
					
	<div id="footer">
		<div id="footerContent">
			<div style="margin-top: 20px; display: inline-block; float: left; color: #FFF; text-shadow: 1px 1px 2px #000;">&copy; Copyright <?php echo date('Y'); ?>.All rights reserved.</div>
                    <?php	if(!empty($settings['sm'])) {
									foreach($settings['sm'] as $type) {
											if($type['toggle'] == 'on') { ?>
			
			<div id="SMWrap">
				<?php echo Safe::decode($type['value']); ?>
			</div>						<?php	}
										}
								} ?>
		</div>
	</div>