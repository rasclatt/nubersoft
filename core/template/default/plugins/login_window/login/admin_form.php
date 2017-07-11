<?php $prefs	=	$this->getDataNode(); ?>
<div class="login_container">
	<div class="admin_login_wrapper">
		<div class="login_bkg">
			<?php echo $this->renderAdminLogo(array("id"=>"nbr_login_logo",'style'=>'max-width: 90%; max-height: 100px; margin: 30px auto;'),'u.png') ?>
			<div id="login">
				<form method="post" action="#" id="loginForm">
					<div class="login_fields"><input type="text"  id="username" name="username" autocomplete="off" required placeholder="Username" value="<?php echo $this->getPost('username') ?>" /></div>
					<div class="login_fields"><input type="password" id="password" name="password" autocomplete="off" required placeholder="Password" /></div>
					<input type="hidden" id="action" name="action" value="login" />
					<input type="hidden" name="jumppage" value="<?php echo $this->setJumpPage('/AdminTools/') ?>" />
					<input type="hidden" name="token[login]" />
					<div class="nbr_contain">
							<div class="nbr_button"><input disabled="disabled" type="submit" name="login" value="Login" id="loginsubmit" class="disabled-submit" /></div>
					</div>
				</form>
				<div style="display: inline-block; width: 100%; max-width: 200px; margin: 0 auto;">
					<?php echo $this->useTemplatePlugin('incidentals','login_window.php') ?>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function() {
	$('.nbr_error_msg_block').fadeIn('slow').delay(5000).fadeOut();
	$(this).keyup(function(e){
		if (e.keyCode == 27)
			$("#loadspot_modal").html('');
	});
});
<?php echo $this->useTemplatePlugin('login_window','login'.DS.'validation.php') ?>
</script>