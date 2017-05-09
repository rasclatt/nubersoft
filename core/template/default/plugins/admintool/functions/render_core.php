<?php
use Nubersoft\nApp as nApp;

function render_core($nuber = false)
	{
		ob_start(); ?>
	<div id="maincontent">
		<?php echo render_contentcached(); // Renders the content. This function will toggle cached-enable pages ?>
		<?php echo render_error(array("display"=>nApp::call()->getDataNode('site')->error_404)); // Renders placent of not-found errors ?>
	</div>
		<?php
		$data	=	ob_get_contents();
		ob_end_clean();
		
		return $data;
	}