<?php
	function process_requests_custom($array = array(),$login = true)
		{
			register_use(__FUNCTION__);
			$header	=	new HeadProcessor();
			// Process any actions
			$header->Process($array);
			
			// Process login action
			if($login == true)
				$header->Login();
			
			return (isset($header->reset) && !empty($header->reset))? array("reset"=>$header->reset,"data"=>$header->data) : array("reset"=>false,"data"=>array());
		}
?>