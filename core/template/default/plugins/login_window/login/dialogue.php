
	<div id="forgot-pass" class="nbr_modal"></div>
	<div class="cancel-click nbr_modal_cancel">
		 <div class="nbr_login_window">
		 	<?php include(__DIR__.DS.'form.php'); ?>
			<div class="nbr_contain col-count-3">
				<div id="forgot-pass-btn" class="nbr_fine_print nbr_click_button nTrigger col-2 push-col-3 medium" data-instructions='<?php echo json_encode(array("action"=>"nbr_open_modal",'FX'=>array('fx'=>array('opacity'),'acton'=>array('body')),"data"=>array("deliver"=>array('action'=>'forgot_password')))) ?>'>Forgot password?</div>
			</div>
		</div>
	</div>

<script>
<?php echo $this->useTemplatePlugin('login_window','login'.DS.'validation.php') ?>

$(document).keyup(function(e){
	if (e.keyCode == 27)
		$("#loadspot_modal").html('').removeClass('visible');
});
</script>