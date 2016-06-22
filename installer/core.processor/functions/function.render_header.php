<?php
	function render_header($settings = false)
		{
			$skiplocal	=	(!empty($settings['skip_meta']));
			$skipadmin	=	(!empty($settings['skip_admin']));
			$dbHeader	=	"";
			
			register_use(__FUNCTION__);
			AutoloadFunction('CheckTaskStatus,use_markup,fetch_plugins,include_metas');
			// Get the site and header options
			if(!isset($settings['site'])) {
					AutoloadFunction('get_site_options,get_header_options');
					$settings['site']	=	get_site_options();
					$settings['header']	=	get_header_options();
				}

			ob_start();
			if(isset($settings['site']->head) && !empty($settings['site']->head)) {
					if(is_file($include = NBR_CLIENT_DIR.$settings['site']->head))
						include_once($include);
				}
			else {
					if(DatabaseConfig::$con != false) {
							ob_start();
							if(!empty($settings['header']->favicons))
								echo Safe::decode($settings['header']->favicons);
							if(!empty($settings['header']->css))
								echo Safe::decode($settings['header']->css);
							if(!empty($settings['header']->javascript)) {
?>

<?php echo Safe::decode($settings['header']->javascript); ?>

<?php 							}
							
							if(!empty($settings['header']->tinymce))
								echo Safe::decode(CheckTaskStatus($settings['header']->tinymce));
							
							
							if(!empty($settings['header']->style)) { ?>

<style>

<?php echo Safe::decode($settings['header']->style); ?>

</style>

<?php							}

							$dbHeader	=	ob_get_contents();
							ob_end_clean();
						}
				}
				
			if(!is_file(NBR_ROOT_DIR.'/.htaccess')) {
					AutoloadFunction('get_default_htaccess');
					get_default_htaccess(array("write"=>true));
				}
			
			if(!$skiplocal) {
					$meta					=	include_metas();
					// Fetch plugins from client_assets/settings/plugins/
					$plugins				=	fetch_plugins();
					
					$meta['user_local']		=	(isset($meta['user_local']))? $meta['user_local']:array();
					$plugins['user_local']	=	(isset($plugins['user_local']))? $plugins['user_local']:array();
					$array					=	array_merge($meta['user_local'],$plugins['user_local']);
					
					// Loop through the array if there are plugins or javascripts
					if(!empty($array)) {
							foreach($array as $hincludes) {
									if(preg_match('/\.css$/',$hincludes)) {
?>

<link rel="stylesheet" href="<?php echo $hincludes; ?>" />
<?php 									}
									elseif(preg_match('/\.js$/',$hincludes)) { ?>

<script src="<?php echo $hincludes; ?>"></script>
<?php									}
								}
						}
				}
				
			if(is_admin() && !$skipadmin) {
?>
<script src="/js/admintools.js"></script>
<link type="text/css" rel="stylesheet" href="/css/admintools.css"/>
<link type="text/css" rel="stylesheet" href="/css/components.css"/>
<?php 			}
			if(!empty($dbHeader)) {
?>
<?php echo $dbHeader;
?>
<?php			}

			$data	=	ob_get_contents();
			ob_end_clean();
			
			$organize	=	explode(PHP_EOL,$data);
			
			if(!empty($organize)) {
					foreach($organize as $key => $val) {
							
							if(str_replace(array("\s","\t"),"",$val) == '<style>')
								$styleset	=	true;
							elseif(str_replace(array("\s","\t"),"",$val) == '</style>')
								unset($styleset);
							
							$organize[$key]	=	(!isset($styleset))? trim(preg_replace('/\t+/', '', $val)) : $val;
						}
				}

			$organize	=	array_filter($organize);
			
			return implode(PHP_EOL,$organize);
		}
?>