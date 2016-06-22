<?php
/*Title: FetchUniqueId()*/
/*Description: This function fetches a random 50-digit number based on the date and time. The number can be salted.*/
/*Example: 
`echo FetchUniqueId();`
*/
	function FetchUniqueId($salt = false,$shuffle = false)
		{
			register_use(__FUNCTION__);
			$number		=	substr(date("YmdHis").preg_replace("/[^0-9]/","",uniqid($salt)),0,49);
			
			if($shuffle == true) {
					$number	=	str_split($number);
					shuffle($number);
					$number	=	implode("",$number);
				}
			// Save a quick unique_id
			return	$number;
		}
?>