<?php
// exit on direct linking
if(!function_exists('AutoloadFunction'))
	exit;
	
if(isset($layout) && $layout)
	core::execute(NBR_ROOT_DIR);

AutoloadFunction("render_site_logo");
?>
	<div id="forgot-pass" class="nbr_modal"></div>
	<div class="cancel-click nbr_modal_cancel">
		 <div class="nbr_login_window">
<?php 
	echo render_site_logo(array("id"=>"nbr_login_logo")); echo PHP_EOL;
	FormLogin::addAttr(array("validation"=>__DIR__.DS."validation.php","form"=>__DIR__.DS."form.php")); echo PHP_EOL;
	echo FormLogin::buildForm(array('link'=>'/','name'=>'home page')).PHP_EOL;
?>
		</div>
	</div>
<script>
$("#forgot-pass-btn").click(function() {
	$("#forgot-pass").fadeIn();
});

$(document).keyup(function(e){
	if (e.keyCode == 27)
		$("#forgot-pass").fadeOut();
});
</script>