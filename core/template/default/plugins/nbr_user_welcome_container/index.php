<div class="nbr_popwrap">
	<?php echo $this->useTemplatePlugin('nbr_welcome_block') ?>
</div>
<script>
$(document).ready(function(){
	var getCont		=	$('.cart_mini_dropspot').html();
	var	getPopCont	=	$('.nbr_popup');
	$('.nbr_popwrap').hover(function() {
		if(empty(getCont))
			getPopCont.slideDown('fast');
	},
	function() {
		if(empty(getCont))
			getPopCont.slideUp('fast');
	});
});
</script>