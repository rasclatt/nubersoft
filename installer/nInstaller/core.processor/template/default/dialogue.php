<?php
// exit on direct linking
if(!function_exists('AutoloadFunction'))
	exit;
	
if(isset($layout) && $layout)
	core::execute(ROOT_DIR);
?>
	<div id="forgot-pass" class="nbr_modal"></div>
	<div class="cancel-click nbr_modal_cancel">
		 <div class="nbr_login_window">
			<?php AutoloadFunction("render_site_logo"); echo render_site_logo(array("id"=>"nbr_login_logo")); ?>
<?php		FormLogin::addAttr(array("validation"=>__DIR__."/validation.php","form"=>__DIR__."/form.php"));
			echo FormLogin::BuildForm(array('link'=>'/','name'=>'home page'));
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