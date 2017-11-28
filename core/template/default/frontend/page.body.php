<?php $View = $this->getPlugin('\nPlugins\Nubersoft\View') ?>

		<div id="content" class="nbr_wrapper">
			<!-- START MASTHEAD -->
			<?php echo $View->renderMastHead(); // Create a standard head ?>

			<!-- START MENUBAR -->
			<?php echo $View->renderMenuBar(); // Create a standard menu bar ?>

			<!-- START MAIN CONTENT -->
			<div id="maincontent">
				<!-- LOGIN WINDOW --> 

				<?php echo $this->useTemplatePlugin('login_window').PHP_EOL; // Check to see if page requires a login ?>

				<!-- RENDER OBJECT -->
				<?php echo $this->getFunction('render_contentcached').PHP_EOL; // Renders the content. This function will toggle cached-enable pages ?>

				<!-- FORBIDDEN MESSAGE -->
				<?php
				// Renders placent of not-found errors
				if($this->isForbidden())
					echo $this->getTemplateDoc('site.forbidden.php');
				?>

			</div>
		</div>