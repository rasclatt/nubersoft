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
		Copyright &reg;<?php echo date("Y"); ?> nUbersoft.
	</footer>