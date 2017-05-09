
<div id="header">
	<div id="headerContent">
		<?php
		$View	=	new \nPlugins\Nubersoft\View('renderSiteLogo',array("style"=>'max-height:200px; margin: 10px;'));
		echo ($View->renderSiteLogo())? $View->renderSiteLogo() : $this->siteUrl().'/client/images/logo/default.png';
		?>
	</div>
</div>