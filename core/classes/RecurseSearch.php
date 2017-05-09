<?php
	
	class	RecurseSearch
		{
			public	$data;
			public	$compare;
			public	$item;
			
			public	function Find($array = '',$find,$recursive = true)
				{
					$find	=	(is_array($find))? implode("|",$find):$find;
					if(is_array($array)) {
						foreach($array as $key => $value) {
						if(preg_match("/$find/",$key))
							$this->compare[$key]	=	$value;
								if($recursive) {
									if(!is_array($value)) {
										if(preg_match("/$find/",$key)) {
											$this->data[$key][]	=	$value;
											$this->item			=	$value;
										}
										
										$array[$key]	=	$value;
									}
									else {
										if(preg_match("/$find/",$key))
											$this->data[$key][]	=	$this->Find($value,$find);
										
										$array[$key]	=	$this->Find($value,$find);
									}
								}
								else {
									if(preg_match("/$find/",$key))
										$this->data[$key]	=	$value;
								}
							}
						
						$this->data	=	(isset($this->data))? $this->data : false;
						
						return ($this->data != false)? $this : false;
					}
				}
		}