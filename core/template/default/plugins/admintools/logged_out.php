<?php
if($this->isAdmin())
	return false;
?>
<div class="col-count-3 offset admin-login-wrap med-1">
	<div class="col-2 admin-login-container push-col-3 medium">
		<?php
		if($this->siteValid()) {
			# If there are already users
			if($this->userCount() > 0)
				echo $this->useTemplatePlugin('login_window','login'.DS.'admin_form.php');
		}
		?>
	</div>
	<div class="col-2 push-col-3 medium">
		<?php echo $this->getHelper('nHtml')->a($this->siteUrl(),$this->siteUrl(),['class'=>'nbr_button small coolset','style'=>'padding: 5px; margin-bottom: 5px;'],false,false) ?>
	</div>
	<div class="col-2 push-col-3 medium">
		Log in above or go to <a href="<?php echo $this->siteUrl() ?>">Home.</a>
	</div>
	<div class="col-2 push-col-3 medium">
		<?php echo $this->render($this->getBackEnd('foot.php')) ?>
	</div>
</div>