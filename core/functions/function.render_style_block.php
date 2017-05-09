<?php

function render_style_block()
	{
		if(empty(nApp::getHeaderContent('style')))
			return false;
		
		ob_start();
?>
<style>
<?php echo Safe::decode(nApp::getHeaderContent('style')).PHP_EOL; ?>
</style>
<?php	$data	=	ob_get_contents();
		ob_end_clean();
		
		return $data;
	}