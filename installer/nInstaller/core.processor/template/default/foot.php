
	<footer class="nbr_footer">
		Copyright &reg;<?php echo date("Y"); ?> nUbersoft.
<?php
if(nApp::getSocialMedia()) {
?>		<div class="nbr_sm">
<?php
	foreach(nApp::getSocialMedia() as $kind => $value) {
		if((!empty($value->toggle) && $value->toggle == 'on') && !empty($value->value)) {
			echo "\t\t\t".'<a href="'.$value->value.'"><img src="'.site_url().'/core_images/core/sm/'.$kind.'.png" /></a>'.PHP_EOL;
		}
		else {
			if($kind == 'social_media') {
				foreach(nApp::getSocialMedia()->social_media as $cSM) {
					echo "\t\t\t".'<a href="'.$cSM->url.'"><img src="'.$cSM->img.'" /></a>'.PHP_EOL;
				}
			}
		}
	}
?>		</div>
<?php
}
?>	</footer>