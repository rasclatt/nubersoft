<?php
	// Expire session class
	include_once('../../../dbconnect.root.php');
	if(is_admin()) {
			$comp_id	=	(isset($_REQUEST['unique_id']))? $_REQUEST['unique_id']: false;
			$page_id	=	(isset($_REQUEST['ref_page']))? $_REQUEST['ref_page']: false;
			AutoloadFunction('create_track_component');
			create_track_component(array('unique_id'=>$comp_id,"ref_page"=>$page_id),$nuber);
		}
	else { ?>
		<span style="color: #666666;">You must be logged in and an Administrator to view this content.</span><?php
		} ?>