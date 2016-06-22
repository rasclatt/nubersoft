<?php
	// Expire session class
	include_once('../../dbconnect.root.php');
	AutoloadFunction('check_emtpy,nQuery');
	if(is_admin()) {
		if(!empty($_GET['isolate'])) { ?>
				<div style="position: absolute; top: 0; right: 0; bottom: 0; left: 0; z-index: 100000000; background-color: #FFF; text-align: center;">
					<div style="padding: 40px;">
						<h2>Editor</h2>
	                	<?php include(RENDER_LIB.'/component.frame.php'); ?>
					</div>
                </div>
	<?php	}
		else {
				$comp_id	=	(!empty($_REQUEST['unique_id']))? Safe::encode($_REQUEST['unique_id']): false;
				$page_id	=	(!empty($_REQUEST['ref_page']))? Safe::encode($_REQUEST['ref_page']): false;
				$component	=	new ComponentEditor($comp_id,$page_id);
				
				// Secure bind statement
				if($page_id != false && !empty($comp_id)) {
						$nubquery	=	nQuery();
						$data		=	$nubquery	->select()
													->from("components")
													->where(array("unique_id"=>$comp_id))
													->fetch();
						
						if(isset($data[0]))
							$data	=	$data[0]; 
					}
				else
					$data	=	array();
				
				$component->Display($data);
			}
		}
	else { ?>
		<span style="color: #666666;">You must be logged in and an Administrator to view this content.</span><?php
		} ?>