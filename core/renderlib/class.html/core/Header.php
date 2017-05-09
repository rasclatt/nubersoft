
                <div id="header">
                	<div id="headerContent">
<?php
	AutoloadFunction("render_site_logo,check_ssl");
	$_img	=	(render_site_logo())? render_site_logo(array("style"=>'max-height:200px; margin: 10px;')) : 'http'.check_ssl().'://www.nubersoft.com/client_assets/images/logo/default.png';
?>						<?php echo $_img; ?>
                    </div>
                </div>