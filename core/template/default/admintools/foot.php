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
	<footer>
		Copyright &reg;<?php echo date("Y"); ?> <a href="http://www.nubersoft.com/" target="_blank">nUbersoft</a>.
	</footer>