<?php
	if(!function_exists('AutoloadFunction'))
		return;
		
	AutoloadFunction('is_admin');
	// If edit is set, insert the loader divs for prefs and such
	if(is_admin()) {
?>	<div id="ajax_admindrop"></div>
	<?php InspectorPallet::execute($settings); // Tool pallet, provide the editor is turned on ?>
<?php	}
?>	<div id="ajax_loadwindow"></div>
