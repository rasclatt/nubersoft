<div style="display: table; width: 100%; height: 100%;">
	<div style="display: table-cell; width: inherit; text-align: center;vertical-align: middle; height: inherit; width: inherit; bottom: 0; position: relative;">
		<div style="width: 100%; max-width: 300px; display: inline-block; margin: 0 auto;">
		<?php
		if($this->siteValid()) {
			# If there are already users
			if($this->userCount() > 0) {
				echo $this->useTemplatePlugin('login_window','login'.DS.'admin_form.php');
			}
		}
		?>
		</div>
	</div>
</div>