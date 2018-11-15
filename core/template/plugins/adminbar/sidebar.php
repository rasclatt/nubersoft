
<a href="<?php echo $this->localeUrl($this->getPage('full_path')) ?>" class="pointer margin-bottom-lrg"><?php echo $this->getSiteLogo(URL_CORE_IMAGES.'/logo/nubersoft.png') ?></a>
<a href="#" class="sidebar nTrigger<?php if($this->getGet('loadpage') == 'load_settings_page' && $this->getGet('subaction') == 'global') echo ' nListener' ?>" data-instructions='{"DOM":{"sendto":["#admin-content"],"html":["<img src=\"/core/template/default/media/images/ui/loader.gif\" class=\"loader\" />"],"event":["click"]},"action":"load_settings_page","data":{"deliver":{"subaction":"global"}}}'>Global Settings</a>
<a href="#" class="sidebar nTrigger<?php if($this->getGet('loadpage') == 'load_settings_page' && $this->getGet('subaction') == 'header') echo ' nListener' ?>" data-instructions='{"DOM":{"sendto":["#admin-content"],"html":["<img src=\"/core/template/default/media/images/ui/loader.gif\" class=\"loader\" />"],"event":["click"]},"action":"load_settings_page","data":{"deliver":{"subaction":"header"}}}'>Header Settings</a>
<a href="#" class="sidebar nTrigger<?php if($this->getGet('loadpage') == 'load_settings_page' && $this->getGet('subaction') == 'footer') echo ' nListener' ?>" data-instructions='{"DOM":{"sendto":["#admin-content"],"html":["<img src=\"/core/template/default/media/images/ui/loader.gif\" class=\"loader\" />"],"event":["click"]},"action":"load_settings_page","data":{"deliver":{"subaction":"footer"}}}'>Footer Settings</a>
<a href="?table=users&subaction=interface" class="sidebar">Users</a>
<?php
foreach($this->getDataNode('plugins')['paths'] as $path) {
	if(!is_dir($path))
		continue;

	foreach(scandir($path) as $pdir) {
		if(in_array($pdir, ['.','..']))
			continue;

		if(!is_file($ui = $path.DS.$pdir.DS.'admin_sidebar.php'))
			continue;
		
		include($ui);
	}
}