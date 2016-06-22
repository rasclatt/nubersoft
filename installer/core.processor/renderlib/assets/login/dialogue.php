<?php
// exit on direct linking
if(!function_exists('AutoloadFunction'))
	exit;

if(isset($layout) && $layout)
	core::execute(NBR_ROOT_DIR);
?>
	<div id="forgot-pass" class="nbr_modal"></div>
	<div class="cancel-click nbr_modal_cancel">
		 <div class="nbr_login_window">
			<?php AutoloadFunction("render_site_logo"); echo render_site_logo(array("id"=>"nbr_login_logo")); ?>
			<?php echo FormLogin::BuildForm(array('link'=>'/','name'=>'home page')); ?>
			<div class="nbr_contain">
				<div id="forgot-pass-btn" class="nbr_fine_print" onClick="ScreenPop();AjaxSimpleCall('forgot-pass','/core.ajax/send.password.php')">Forgot password?</div>
			</div>
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