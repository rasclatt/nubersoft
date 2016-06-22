<?php
if(!function_exists('AutoloadFunction')) return;
// Check session for toggle editor
AutoloadFunction('get_edit_status,site_url');
?>
    <nav id="menuWrap">
        <ul id="menuContainer">
<?php
	NubeData::$settings->gmenu	=	Safe::to_object(NubeData::$settings->gmenu);
if(!empty($_menu_on)) {
	$page_id	=	NubeData::$settings->page_prefs->unique_id;
	$useid		=	false;
	foreach(NubeData::$settings->gmenu as $key => $array) {
		$submenu	=	array();
		if(isset(NubeData::$settings->gsub->{$key}))
			$submenu	=	NubeData::$settings->gsub->{$key};
			
		if($array->in_menubar == 'on') {
			if(isset(NubeData::$settings->gsub->{$array->unique_id}))
				$useid	=	NubeData::$settings->gsub->{$array->unique_id};
				
			$_button['sub_set']		=	($useid && $useid->parent_id == $array->unique_id);
			$_button['sub_live']	=	(!empty($useid->page_live) && $useid->page_live == 'on');
			// Assign refpage
			if(get_edit_status()) {
				$this->component->ref_page	=	$key;
				$this->component->page_id	=	$page_id;
			}
?><li class="nbr_button_cont <?php echo $array->unique_id; ?>" id="btnctn<?php echo $key; ?>">
				<a class="mainMenu<?php echo ($key == $page_id)? "_on":''; ?>" href="<?php echo site_url().$array->full_path; ?>" id="btn<?php echo $key; ?>"><?php echo ucwords(Safe::decode($array->menu_name)); ?></a>
<?php				if(get_edit_status() || ($_button['sub_set'] && $_button['sub_live'])) {
?>
				<div class="nbr_menu_subpop<?php if(get_edit_status()) { ?> stick_it_<?php echo $array->unique_id; } ?>">
<?php								if(get_edit_status()) {
?>					<div class="click_stick" data-stick="stick_it_<?php echo $array->unique_id; ?>">STICK MENU</div>
<?php
											$_data	=	$submenu;
											$this->Component($_data,$array->unique_id);
										}
									else {
											AutoloadFunction('use_markup');
											if(isset($submenu->content) && $submenu->page_live == 'on')
												echo Safe::decode(use_markup($submenu->content,true));
										}
?>				</div>
<?php							}
?></li><?php			}
				} 
		}
						
	if(($allowBypass == 0 && !empty($_bypass)) && (is_admin())) {
			global $_error;
			$_error['bypass'][]	=	'Menu: File not found.';
		}
?>					
		</ul>
	</nav>