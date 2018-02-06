<?php
namespace nPlugins\Nubersoft;

if(!empty($this->getDataNode('connection')->health)):
	# New track/render instance
	$track				=	new TrackEditor(NBR_ROOT_DIR);
	# Retrieve settings
	$prefs				=	$this->getPage();
	$user				=	$this->getUser();
	$unique_id			=	(!empty($prefs->unique_id))? $prefs->unique_id : false;
	# Insert Default CSS
	$track->DefaultCSS();
	# Run a query to check if there are rows for this page
	$_content	=	$this->nQuery()
		->select()
		->from("components")
		->where(array("ref_page"=>$unique_id,"parent_id"=>$unique_id),"OR",false,true)
		->addCustom("AND `ref_spot` = 'nbr_layout'")
		->orderBy(array("page_order"=>"ASC"))
		->fetch();

	# Saves default state for IDs as unrestricted
	$localeRestrict	=	true;
	# Fetch current
	$lList	=	$this->storeLocalesList($_content);
	# Save the list for use in the iterator
	$this->saveLocales();
	if($lList->getLocaleCount() > 0)
		$localeRestrict	=	true;
	# If there are rows found for page, continue on	
	if($_content !== 0):
		# See if the editor is turned on
		$turnedOnEdit		=	($this->getEditStatus())? '': "and page_live = 'on'";
		# Loop through rows, save all component unique_ids to filtered array
		foreach($_content as $object) {
			if(empty($object['unique_id']))
				continue;
			$css	=	array();	
			# Apply css to object
			if(isset($object['c_options']))
				unset($object['c_options']);

			$object	=	array_merge($object,$css);
			# Assign final array
			$this->info[$object['unique_id']]	=	$object;
			# Filter the array out
			$this->info[$object['unique_id']]	=	array_filter($this->info[$object['unique_id']]);
			# Remove $css so it doesn't persist to the next set-up
			unset($css);
		}
		# Save to object for output to normal array
		$struc	=	new \ArrayObject($this->getTreeStructure($this->info, $parent = 0));
		# Save to an easily recursable array
		foreach($struc as $keys => $values) {
			$struct[$keys]	=	$values;
		}

		if($this->getEditStatus() && $this->isAdmin()) {
			$nQuery				=	$this->nQuery();
			# Run a query to check if there are rows for this page
			$page_components	=	$nQuery
				->select("COUNT(*) as count")
				->from("components")
				->where(array(
					"ref_page"=>$unique_id,
					"parent_id"=>$unique_id
				),"OR",false,true)
				->addCustom("AND `ref_spot` = 'nbr_layout'")
				->addCustom($turnedOnEdit)
				->fetch();

			if($page_components[0]['count'] > 0) {
				# Render out the page
				$track->track($struct,$this->info);
			}
		}
		else {
			# If page is turned on
			$_page_live	=	(isset($prefs->page_live) && $prefs->page_live == 'on');
			# if Login is required to access
			$_login_req	=	(isset($prefs->session_status) && $prefs->session_status == 'on');
			# If user is logged in
			$_logged_in	=	($this->isLoggedIn());
			# If page on
			if($_page_live || (!$_page_live && $this->isAdmin())) {
				# If login required and loggedin, or login is not required
				if(($_login_req && $_logged_in) || !$_login_req) {
					foreach($struct as $key => $array) {
						# Render engine
						$this->renderIterator($array,$key);
					}
				}
			}
		}
	else:
		# If the page is empty, and the edit is toggled, show an empty component
		if($this->getEditStatus()): ?>

<div class="componentWrap">
	<?php echo $this->componentEditors($unique_id); ?>
</div>

		<?php
		endif;
	endif;
endif;