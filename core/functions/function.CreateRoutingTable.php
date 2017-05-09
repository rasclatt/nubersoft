<?php
	function CreateRoutingTable($table = false)
		{
			if(!empty($table)) {
				if(nApp::getColumns($table)) {
					AutoloadFunction('FetchUniqueId,TableIdStatus');
					$num	=	FetchUniqueId(rand(100,999),true);
					TableIdStatus($num,$table);
				}
			}
			
			return false;
		}