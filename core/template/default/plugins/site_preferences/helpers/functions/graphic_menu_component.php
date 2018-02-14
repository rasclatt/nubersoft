<?php
use \Nubersoft\nApp as nApp;

function graphic_menu_component(\Nubersoft\nApp $nApp)
	{
		ob_start();
		
		$POST		=	$nApp->getPost();
		$deliver	=	$POST->deliver;
		$qData		=	$deliver->query_data;
		$page_id	=	(isset($qData->ref_page))? $qData->ref_page : false;
		$comp_id	=	(isset($qData->unique_id))? $qData->unique_id : false;
		$sendTo		=	(isset($deliver->send_back))? $deliver->send_back : false;
		$ID			=	(isset($qData->ID) && is_numeric($qData->ID))? $qData->ID : false;

		if(!$nApp->isAdmin()) {
			die(json_encode(array('html'=>array('<script>alert(\'You must be logged in and an Administrator to view this content.\';</script>'),'sendto'=>array('body'))));
		}

		$validPg	=	function($arr,$key) {
			
			if(!empty($arr) && !empty($arr->{$key})) {
				return (is_numeric($arr->{$key}))? $arr->{$key} : false;
			}
		};
		
		$component	=	new \nPlugins\Nubersoft\AdminToolsComponentEditor($comp_id,$page_id);
		$data		=	array();
		
		# Secure bind statement
		if($ID) {
			$data	=	$nApp->nQuery()
							->select()
							->from("components")
							->where(array("ID"=>$ID,'category_id'=>'sub_menu'))
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
		else
			$data	=	$nApp->toArray($qData);
		# Tells the system to use the components table
		echo $component->useTable('components')
			# Tells the system to look up the component layout for the sub_menu components
			->useComponentMap('menu_display')
			# Tells the system to use the MenuButton namespace for the layout
			->setDisplayLayout('MenuButton')->display($data);
		
		$data	=	ob_get_contents();
		ob_end_clean();
		
		die(json_encode(array(
			'html'=>array($data),
			'sendto'=>array($nApp->getPost('deliver')->send_back),
			'fx'=>array('fadeIn'),
			'acton'=>array($nApp->getPost('deliver')->send_back)
		)));
	}