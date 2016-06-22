<?php
/*Title: get_user_roles()*/
/*Description: This function is used in the `set.permissions.php` page which is ajax loaded. UNDER CONSTRUCTION!*/
	function get_user_roles()
		{
			register_use(__FUNCTION__);
			$count	=	func_num_args();
			
			if($count > 0) {
				$args	=	func_get_args();
				
				for($i = 0; $i < $count; $i++) {
					if(is_array($args[$i]))
						$columns	=	$args[$i];
					elseif(is_object($args[$i]))
						$nuber		=	$args[$i];
				}
			}
			
			AutoloadFunction('nQuery');
			$query		=	nQuery();
			$columns	=	(isset($columns))? $columns:array("ID","unique_id","name","content","usergroup");
			$users		=	$query->select($columns)->from("system_settings")->where(array("name"=>"permissions"))/*->DecodeColumns(array("content"))*/->fetch();
			/*
			
				
			public	function EncodeColumns($array = false)
				{
					register_use(__METHOD__);
					
					$this->serial	=	false;
					
					if($array != false) {
							if(is_array($array) && !empty($array)) {
									$this->serial	=	$array;
								}
						}
						
					return $this;
				}
				
			public	function DecodeColumns($array = false)
				{
					register_use(__METHOD__);
					
					$this->unserial	=	false;
					
					if($array != false) {
							if(is_array($array) && !empty($array)) {
									$this->unserial	=	$array;
								}
						}
						
					return $this;
				}
			*/
			return $users;
		}