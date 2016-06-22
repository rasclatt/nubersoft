<?php
/*Title: get_email_receipts()*/
/*Description: This function loads the email receipts from the `components` table.*/

	function get_email_receipts($settings = false)
		{
			register_use(__FUNCTION__);
			AutoloadFunction('check_empty,nQuery');
			$array		=	(!empty($settings['id']))? array("ID"=>$settings['id'],"ref_spot"=>"emailer"): array("ref_spot"=>"emailer");
			$emails		=	nQuery()	->select("content")
										->from("components")
										->where($array)
										->fetch();
			if($emails == 0)
				return false;
			$emailCnt	=	count($emails);
			for($i=0; $i < $emailCnt; $i++) {
					$emails[$i]['content']	=	unserialize(Safe::decode($emails[$i]['content']));
				}
			
			return $emails;
		}