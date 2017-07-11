<?php $View = $this->getPlugin('\nPlugins\Nubersoft\View') ?>
<div id="content" class="nbr_wrapper">
	<?php echo $View->renderMastHead(); // Create a standard head ?>
	<?php echo $View->renderMenuBar(); // Create a standard menu bar ?>
	<div id="maincontent">
		<?php
		// Check to see if page requires a login
		echo $this->useTemplatePlugin('login_window').PHP_EOL;
		?>
		<?php
		// Renders the content. This function will toggle cached-enable pages
		echo $this->getFunction('render_contentcached').PHP_EOL;
		?>
		<?php
		// Renders placent of not-found errors
		if($this->isForbidden())
			echo $this->forbidden();
		?>
	</div>
</div>