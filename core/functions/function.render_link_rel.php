<?php
function render_link_rel($links = false, $type = "rel", $useLocalUrl = true)
	{
		$localUrl	=	"";
		if($useLocalUrl) {
			AutoloadFunction("site_url");
			$localUrl	=	site_url();
		}
		ob_start();
		if(!empty($links)) {	
			foreach($links as $key => $value) {
?><link <?php echo $type; ?>="<?php echo Safe::decode($key); ?>" href="<?php echo $localUrl.Safe::decode($value); ?>" />
<?php		}
		}
		else {
			AutoloadFunction("site_url");
?><link rel="canonical" href="<?php echo site_url(); ?>" />
<?php	}
		$data	=	ob_get_contents();
		ob_end_clean();
		
		return $data;
	}