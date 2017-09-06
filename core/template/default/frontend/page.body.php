<?php $View = $this->getPlugin('\nPlugins\Nubersoft\View') ?>
<div id="content" class="nbr_wrapper">
	<?php echo $View->renderMastHead(); // Create a standard head ?>
	<?php echo $View->renderMenuBar(); // Create a standard menu bar ?>
	<div id="maincontent">
		<?php echo $this->useTemplatePlugin('login_window').PHP_EOL; // Check to see if page requires a login ?>
		<?php echo $this->getFunction('render_contentcached').PHP_EOL; // Renders the content. This function will toggle cached-enable pages ?>
		<?php
		// Renders placent of not-found errors
		if($this->isForbidden())
			echo $this->getTemplateDoc('site.forbidden.php');
		?>
	</div>
</div>