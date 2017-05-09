<?php
if(!function_exists("ajax_load_favicons")) {
	function ajax_load_logo()
		{
			$error		=	"No company logo set.";
			$sitePrefs	=	nApp::getHead();
			
			if(empty($sitePrefs->site->content))
				return $error;
			
			$logo	=	Safe::to_array($sitePrefs->site->content);
			
			if(empty($logo['companylogo']))
				return $error;
				
			AutoloadFunction("site_url,version_from_file");
?>
			<div style="border: 1px solid #FFF; box-shadow: 1px 1px 8px #000; text-align: center;">
				<img src="<?php echo site_url().$logo['companylogo'].version_from_file(NBR_ROOT_DIR.$logo['companylogo']); ?>" style="margin: 5px; max-height: 80px;" />
			</div>
<?php
		}
}
header("Cache-control: max-age=0, must-revalidate");
echo ajax_load_logo();