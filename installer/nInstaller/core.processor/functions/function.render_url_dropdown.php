<?php
	function render_url_dropdown($unique_id = false)
		{
			register_use(__FUNCTION__);
			
			if(!isset(NubeData::$settings))
				return;
				
			// This page id
			$unique_id			=	(!empty($unique_id))? $unique_id:NubeData::$settings->page_prefs->unique_id;
			// This page's prefs
			$curr_page			=	(array) NubeData::$settings->page_prefs;
			// Get the menus
			$checkRows			=	(isset(NubeData::$settings->menu_data))? Safe::to_array(NubeData::$settings->menu_data): array();
			$parent				=	(isset($checkRows[$unique_id]['parent_id']))? $checkRows[$unique_id]['parent_id']:"";
			// Set a start for the build
			$default_container	=	(isset($curr_page['parent_id']) && !empty($curr_page['parent_id']))? $curr_page['parent_id']: '';
			$default_cont_name	=	(isset($curr_page['parent_id']) && !empty($curr_page['parent_id']))? $curr_page['full_path']: '';
			$default_disp		=	(isset($default_container) && !empty($default_container))? $default_cont_name: 'Select Parent Directory';
			
			ob_start(); ?>
		<div class="nbr_parent_wrap">
			<select name="parent_id">
				<option value="">No Parent Directory</option><?php
				
			foreach($checkRows as $options) {
					// Check if the button is itself
					$itself		=	($options['unique_id'] == $unique_id)? true:false;
					// Check if it's the parent of this menu
					$itsParent	=	($options['unique_id'] == $parent)? true:false;
					
					if(!$itself) {
?>

				<option<?php if($itsParent) { ?> selected="selected"<?php } ?> value="<?php echo $options['unique_id']; ?>"><?php echo substr(Safe::decode($options['menu_name']), 0, 20); ?></option>
<?php					}
				}
			
			// Check for components (divs/containers) for this page ?>
			</select>
		</div>
			<?php
			$data	=	ob_get_contents();
			ob_end_clean();
			
			return $data;
		}
?>