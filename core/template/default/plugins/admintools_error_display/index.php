<?php
	$errors		=	array_unique($this->getAllAlerts('errors'));
	if($errors) {
	?>
	<div style="text-align: left; display: none;" class="nbr_errors_wrap">
		<div class="head-button nTrigger" data-instructions='{"FX":{"fx":["slideToggle"],"acton":["next::slideToggle"],"fxspeed":["fast"]}}'>
			<ul>
				<li><?php echo $this->getHelper('nImage')->imageBase64(NBR_MEDIA_IMAGES.DS.'core'.DS.'icn_alert.png',array('style'=>'max-height: 40px;')) ?></li>
				<li>Errors</li>
			</ul>
		</div>
		<div style="padding: 20px; background-color: red; color: #FFF; text-shadow: 1px 1px 3px #000; text-align: left; overflow: auto;">
		<div class="nbr_error"><?php echo implode('</div><br /><div class="nbr_error">',$errors) ?></div>
		</div>
	</div>
	<?php
	}
	?>
</div>
</div>
<script>
$('.nbr_errors_wrap').delay(1000).fadeIn('fast');
</script>