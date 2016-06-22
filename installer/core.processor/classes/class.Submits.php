<?php
	class Submits
		{	
			public		$charset;
			protected	$filter;
			private		static $singleton;
			
			public	function __construct()
				{
					if(!isset(self::$singleton)) {
							// Fetches the < > & " characters
							$html					=	get_html_translation_table();
							// Gets all the utf-8 characters
							$hentities				=	get_html_translation_table(HTML_ENTITIES,ENT_NOQUOTES,'UTF-8');
							// Gets all the utf-8 characters
							$hspecial				=	get_html_translation_table(HTML_SPECIALCHARS,ENT_NOQUOTES,'UTF-8');
							// Combine them all for a super replacer
							$allent					=	array_merge($hspecial,$hentities);
							// Remove the HTML characters from the mix
							self::$singleton		=	array_diff($allent,$html);
						}
					// Assign the characters
					$this->charset	=	self::$singleton;
				}
			
			protected	function RecurseArray($value,$key='')
				{
					register_use(__METHOD__);
					// Isolate the searchable
					$rawchars	=	array_keys(self::$singleton);
					// Isolate the replaceable
					$repchars	=	array_values(self::$singleton);
					// Replace the special characters
					$value		=	str_replace($rawchars,$repchars,$value);
					// Now double encode the html and special chars
					if(!is_array($value)) {
							switch ($this->filter) {
									case ('strip') :
										$val	= 	(is_numeric($value))? (string) $value: strip_tags($value);
										break;
									case ('specialchars') :
										$val	= 	(is_numeric($value))? (string) $value: htmlspecialchars($value,ENT_QUOTES,"UTF-8",true);
										break;
									default:
										$val	= 	(is_numeric($value))? (string) $value: htmlentities($value, ENT_QUOTES, 'UTF-8');
								}
					
							return $val;		
						}
							
					if(is_array($value) && !empty($value)) {
							foreach($value as $keys => $values) {
									$val[$keys]	=	$this->RecurseArray($values,$keys);
								}
							
							return $val;
						}
						
				}
				
			public	function sanitize($filter = false)
				{
					register_use(__METHOD__);
					
					$this->filter	=	$filter;
					$register	=	new RegisterSetting();
					
					if((isset($_REQUEST) && !empty($_REQUEST))) {
							$array		=	$this->RecurseArray($_REQUEST);
							$_REQUEST	=	$array;
							$register->UseData("_REQUEST",$array)->SaveTo("settings");
						}
					
					if((isset($_POST) && !empty($_POST))) {
							$array		=	$this->RecurseArray($_POST);
							$_POST		=	$array;
							$register->UseData("_POST",$array)->SaveTo("settings");
						}
					
					if((isset($_GET) && !empty($_GET))) {
							$array		=	$this->RecurseArray($_GET);
							$_GET		=	$array;
							$register->UseData("_GET",$array)->SaveTo("settings");
						}
						
					return (isset($array))? true:false;
				}
		} ?>