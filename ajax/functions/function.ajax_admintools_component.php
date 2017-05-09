<?php
function ajax_admintools_component()
	{
		ob_start();
		AutoloadFunction('check_empty,nQuery');
		if(!is_admin()) {
?>	<span style="color: #666666;">You must be logged in and an Administrator to view this content.</span><?php
			$data	=	ob_get_contents();
			ob_end_clean();
			
			echo $data;
		}

		if(!empty($_GET['isolate'])) {
?>	<div style="position: absolute; top: 0; right: 0; bottom: 0; left: 0; z-index: 100000000; background-color: #FFF; text-align: center;">
		<div style="padding: 40px;">
			<h2>Editor</h2>
			<?php include(NBR_ROOT_DIR.'/core/ajax.engine/component/frame.php'); ?>
		</div>
	</div>
<?php		}
		else {
			
			$validPg	=	function($arr,$key) {
				
				if(!empty($arr) && !empty($arr->{$key})) {
					return (is_numeric($arr->{$key}))? $arr->{$key} : false;
				}
			};
			
			if(strpos(nApp::getRequest('use'),"component") !== false)
				nApp::resetTableAttr("components");
			
			$comp_id	=	$validPg(nApp::getPost('vars'),'unique_id');
			$page_id	=	$validPg(nApp::getPost('vars'),'ref_page');
			$component	=	new ComponentEditor($comp_id,$page_id);
			$data		=	array();
			
			// Secure bind statement
			if($comp_id) {
				$data	=	nQuery()	->select()
										->from("components")
										->where(array("unique_id"=>$comp_id))
										->fetch();

				if(isset($data[0])) {
					if(!empty($data[0]['css'])) {
						$cssArr	=	json_decode($data[0]['css'],true);
						$css	=	(is_array($cssArr))? array_filter($cssArr):array();
						foreach($css as $cssKey => $cssVal)
							$data[0]["css[".$cssKey."]"]	=	$cssVal;
					}
						
					$data	=	$data[0]; 
				}
			}
			
			$component->Display($data);
		}
			
		$data	=	ob_get_contents();
		ob_end_clean();
		
		echo $data;
	}