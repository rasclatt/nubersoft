<?php

	function render_site_logo($settings = false)
		{
			if(empty(nApp::getSiteLogo()))
				return '<!-- Empty logo file -->';

			if(!is_file(str_replace(DS.DS,DS,NBR_ROOT_DIR.DS.nApp::getSiteLogo())))
				return '<!-- Logo file does not exist -->';
			
			$opts[]	=	(!empty($settings['style']))? 'style="'.$settings['style'].'"':'';
			$opts[]	=	(!empty($settings['class']))? 'class="'.$settings['class'].'"':'';
			$opts[]	=	(!empty($settings['alt']))? 'alt="'.$settings['alt'].'"':'';
			$opts[]	=	(!empty($settings['id']))? 'id="'.$settings['id'].'"':'';
			$opts[]	=	(!empty($settings['name']))? 'name="'.$settings['id'].'"':'';
			$opts[]	=	(!empty($settings['data']))? 'data-'.$settings['data'][0].'="'.$settings['data'][1].'"':'';
			$opts[]	=	(!empty($settings['link']))? 'onClick="window.location=\''.$settings['link'].'\'"':'';
			$opts[]	=	(!empty($settings['custom']))? $settings['custom']:'';
			
			$opts	=	array_filter($opts);
			AutoloadFunction("site_url");
			$siteLogo	=	nApp::getSiteLogo();
			if(empty($siteLogo))
				$siteLogo	=	'/images/logo/default.png';
			ob_start();
?>			<img src="<?php echo site_url().$siteLogo; ?>" <?php echo implode(" ",$opts);?> />
<?php
			$data	=	ob_get_contents();
			ob_end_clean();
			
			return $data;
		}