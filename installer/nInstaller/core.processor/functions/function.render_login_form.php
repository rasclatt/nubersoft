<?php
	function render_login_form($settings = false)
		{
			register_use(__FUNCTION__);
			
			$convert	=	function($val) {
					switch($val) {
							case('null') :
								return false;
							case('true') :
								return true;
							case('false') :
								return false;
							default :
								return $val;
						}
				};
			
			$splitValidateRules	=	function($val) {
					$array	=	array_filter(explode('(',rtrim($val,")")));
					
					if(empty($array[1])) 
						return array("name"=>$array[0]);
						
					$new["name"]		=	trim($array[0]);
					$new["rules"]	=	explode("|",$array[1]);
							
					foreach($new["rules"] as $key => $value) {
							if(strpos($value,"_") !== false) {
									$setSubs							=	explode("_",trim($value));
									$setSubsFront						=	array_shift($setSubs);	
									$new["rules"][trim($setSubsFront)]	=	implode("",$setSubs);
									
								}
							else
								$new["rules"][trim($value)]	=	true;
							
							unset($new["rules"][$key]);
						}
					
					return $new;
				};
				
			$splitValidateMess	=	function($val) {
					$array	=	array_filter(explode('(',rtrim($val,")")));
					
					if(empty($array[1])) 
						return array("name"=>trim($array[0]));
						
					$new["name"]			=	trim($array[0]);
					$new["messages"]	=	explode("|",$array[1]);
					
					foreach($new["messages"] as $key => $value) {
							if(strpos($value,"_") !== false) {
									$setSubs							=	explode("_",trim($value));
									$setSubsFront						=	array_shift($setSubs);	
									$new["messages"][trim($setSubsFront)]	=	implode("",$setSubs);
									
								}
							else
								$new["messages"][trim($value)]	=	true;
							
							unset($new["messages"][$key]);
						}
					return $new;
				};
			
			$id				=	(!empty($settings['id']))? $settings['id'] : "loginform";
			$method			=	(!empty($settings['method']))? $settings['method'] : "post";
			$class			=	(!empty($settings['class']))? $settings['class'] : "nbr_form_general";
			$action			=	(!empty($settings['action']))? $settings['action'] : "";
			$enctype		=	(!empty($settings['enctype']))? $settings['enctype'] : "multipart/form-data";
			$redirect		=	(!empty($settings['redirect']))? $settings['redirect'] : "/";
			$types			=	(!empty($settings['types']))? array_map($convert,explode(",",$settings['types'])) : array();
			$vals			=	(!empty($settings['values']))? array_map($convert,explode(",",$settings['values'])) : array();
			$names			=	(!empty($settings['names']))? array_map($convert,explode(",",$settings['names'])) : array();
			$labels			=	(!empty($settings['labels']))? array_map($convert,explode(",",$settings['labels'])) : array();
			$placeholders	=	(!empty($settings['placeholders']))? array_map($convert,explode(",",$settings['placeholders'])) : array();
			$size			=	(!empty($settings['size']))? array_map($convert,explode(",",$settings['size'])) : array();
			$dropdowns		=	(!empty($settings['dropdowns']))? array_map($convert,explode(",",$settings['dropdowns'])) : array();
			
			// Register error if there are no field types
			if(empty($types)) {
					$register	=	new RegisterSetting();
					$register	->UseData('html',array("form"=>"No TYPES specified"))
								->SaveTo('settings');
					return false;
				}
			
			ob_start();
			// Create an empty array based on how many types there are
			$baseArr		=	array_fill(0,count($types),false);
			
			
			$opts	=	array(	
								"form"=>array(	"id"=>$id,
												"class"=>$class,
												"action"=>$action,
												"method"=>$method,
												"enctype"=>$enctype
											),
								"redirect"=>$redirect,
								"types"=>$types,
								"values"=>array_replace($baseArr,$vals),
								"names"=>array_replace($baseArr,$names),
								"labels"=>array_replace($baseArr,$labels),
								"placeholders"=>array_replace($baseArr,$placeholders),
								"size"=>array_replace($baseArr,$size),
								"options"=>array_replace($baseArr,$dropdowns)
							);
			// Create form builder app
			$formEngine	=	new FormBuilder(FormBuilder::REPORTING_ON);
			// Initialize the form
			$formEngine->init($opts['form']);
			// Loop through the types and add fields
			foreach($opts['types'] as $key => $fieldType) {
					$formEngine->addField(array(	"type"=>$fieldType,
												"options"=>array(	'value'=>$opts['values'][$key],
																	'name'=>$opts['names'][$key],
																	'size'=>$opts['size'][$key],
																	'placeholder'=>$opts['placeholders'][$key],
																	'label'=>$opts['labels'][$key]
																)
												));
				}
				
			// If the validate is required, add the jQuery Validtor class
			if(!empty($settings['validate'])) {
							$rules		=	array_map($splitValidateRules,explode(",",$settings['validate_rules']));
							$messages	=	array_map($splitValidateMess,explode(",",$settings['validate_messages']));
							$messages	=	organize($messages,"name");
							$rules		=	organize($rules,"name");
							$fields		=	array_keys($messages);
							
							if(is_array($fields)) {
							
							foreach($fields as  $name) {
									$_rules[$name]		=	$rules[$name]['rules'];
									$_messages[$name]	=	$messages[$name]['messages'];
								}
								
							$formEngine	->setValidationOpts(array("add_tags"=>true))
										->useValidation($fields,$_rules,$_messages);
						}
				}
			
			echo $formEngine	->compile()
							->toPage();
			
			if(!empty($formEngine->displayJQuery()))
				echo $formEngine->displayJQuery();
			
			$data	=	ob_get_contents();
			ob_end_clean();
			return $data;
			
		}