<?php
	$incdentals	=	$this->toArray($this->getIncidental());
	if(isset($incdentals['nquery']))
		unset($incdentals['nquery']);
	
	if(!empty($incdentals)) {
?>
<script>
$(document).ready(function() {
	$('.nbr_error_msg').fadeIn('fast').delay(3000).slideUp('fast');
});
</script>
<?php
	}
?>
	<footer class="nbr_footer">
		Copyright &reg;<?php echo date("Y"); ?> nUbersoft.
<?php
if($this->getSocialMedia()) {
?>		<div class="nbr_sm">
<?php
	foreach($this->getSocialMedia() as $kind => $value) {
		if((!empty($value->toggle) && $value->toggle == 'on') && !empty($value->value)) {
			echo "\t\t\t".'<a href="'.$value->value.'"><img src="'.site_url().'/images/core/sm/'.$kind.'.png" /></a>'.PHP_EOL;
		}
		else {
			if($kind == 'social_media') {
				foreach($this->getSocialMedia()->social_media as $cSM) {
					echo "\t\t\t".'<a href="'.$cSM->url.'"><img src="'.$cSM->img.'" /></a>'.PHP_EOL;
				}
			}
		}
	}
?>		</div>
<?php
}
?>	</footer>