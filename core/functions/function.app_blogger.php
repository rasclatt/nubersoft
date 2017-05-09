<?php
	function app_blogger($ref_spot = 'blog', $ref_page = false,$allowSubs = false)
		{
			// Blog identifier default is the page the component is on
			$ref_page	=	(isset($ref_page) && !empty($ref_page))? $ref_page : nApp::getPage('unique_id');
			ob_start();
			$blogger	=	new PostEngine($ref_page,$ref_spot);
			$blogger	->setAttr(array("table"=>"components"))
						->init($allowSubs);
			//echo printpre(NubeData::$settings->pagination);
			echo $blogger->view();
			$data		=	ob_get_contents();
			ob_end_clean();
			
			return $data;
		}