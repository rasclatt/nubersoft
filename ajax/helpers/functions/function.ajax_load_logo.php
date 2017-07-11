<?php
use \Nubersoft\nApp as nApp;

function ajax_load_logo()
	{
		$error		=	"No company logo set.";
		$sitePrefs	=	nApp::call()->getDataNode('preferences');
		
		if(empty($sitePrefs->settings_site->content->companylogo))
			return $error;
		
		$logo	=	str_replace('client_assets','client',nApp::call()->toSingleDs(NBR_ROOT_DIR.DS.str_replace('/',DS,$sitePrefs->settings_site->content->companylogo)));
		
		if(empty($logo) || !is_file($logo))
			return $error;
		
		ob_start();
?>
		<div style="border: 1px solid #FFF; box-shadow: 1px 1px 8px #000; text-align: center;">
			<?php echo nApp::call('nImage')->image($logo,array('style'=>'margin: 5px; max-height: 80px;')); ?>
		</div>
<?php
		$data	=	ob_get_contents();
		ob_end_clean();
		
		return $data;
}
header("Cache-control: max-age=0, must-revalidate");
die(json_encode(array('html'=>array(ajax_load_logo()),'sendto'=>array('#logoList'))));