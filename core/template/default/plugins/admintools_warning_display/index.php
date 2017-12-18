<?php
	$errors		=	array_unique($this->getAllAlerts('warnings'));
	if($errors) {
	?>
	<div style="text-align: left; display: none;" class="nbr_warning_wrap">
		<div class="head-button nTrigger" data-instructions='{"FX":{"fx":["slideToggle"],"acton":["next::slideToggle"],"fxspeed":["fast"]}}'>
			<ul>
				<li><?php echo $this->getHelper('nImage')->imageBase64(NBR_MEDIA_IMAGES.DS.'core'.DS.'icn_alert.png',array('style'=>'max-height: 40px;')) ?></li>
				<li>Warnings</li>
			</ul>
		</div>
		<div class="nbr_warning" style="padding: 20px; text-align: left; overflow: auto; box-shadow: none; color: #000; text-shadow: none;">
			<div>-<?php echo implode('</div><br /><div>-',$errors) ?></div>
		</div>
	</div>
	<?php
	}
	?>
</div>
</div>
<script>
$('.nbr_warning_wrap').delay(1000).fadeIn('fast');
</script>