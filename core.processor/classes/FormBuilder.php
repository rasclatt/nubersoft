<?php
	class	FormBuilder
		{
			public	static	$getCount;
			
			private	$formId;
			private	$formName;
			private	$bttnsUsed;
			private	$errorReporting;
			private	$errMessages;
			private	$form;
			private	$validation;
			private	$jQueryValidation;
			private	$objStatus;
			private	$toHTML;
			private	$validationOpts;
	
			private	static	$singleton;

			const	NO_BUTTON		=	false;
			const	REPORTING_ON	=	'e';

			public	function __construct()
				{
					$args							=	func_get_args();
					// Check for error reporting
					$this->errorReporting			=	(is_array($args) && in_array('e',$args));
					// If errors on
					$this->objStatus['reporting']	=	$this->errorReporting;
					$this->toHTML					=	array();
					$this->form['open_tag']			=	"";
					$this->form['close_tag']		=	"";
				}
			
			private	function resetSettings()
				{
					// Auto-set all the defaults for the class
					$this->errMessages				=	
					$this->bttnsUsed				=
					$this->form						=	
					$this->validation				=	
					self::$getCount					=	
					$this->objStatus				=	array();
				}
			
			public	function init($settings = false)
				{	
					$this->formId	=	(!empty($settings['id']))? $settings['id'] : 'form_'.mt_rand();
					$this->formName	=	(!empty($settings['name']))? $settings['name'] : 'name_'.mt_rand();
					
					// Create presets and defaults for the input
					$formOpen[]	=	'id="'.$this->formId.'"';
					$formOpen[]	=	'name="'.$this->formName.'"';
					$formOpen[]	=	(!empty($settings['class']))? 'class="'.$this->implodeClass($settings['class']).'"' : "";
					$formOpen[]	=	(!empty($settings['action']))? 'action="'.$settings['action'].'"' : 'action=""';
					$formOpen[]	=	(!empty($settings['method']))? 'method="'.$settings['method'].'"' : 'method="post"';
					$formOpen[]	=	(!empty($settings['enctype']))? 'enctype="'.$settings['enctype'].'"' : "";
					$formOpen[]	=	(!empty($settings['script']))? $settings['script'] : "";
					$error		=	false;
					
					// If the error reporting is tured on and the action is filled in
					if($this->errorReporting && !empty($settings['action'])) {
							// Get error message OR false if the value is seemingly a server-side file type (php,asp)
							$error[]	=	$this->errorFeedback($settings['action'],"url_dynamic");
							// Get error message OR false if the url is not present (based on root)
							$error[]	=	$this->errorFeedback($settings['action'],"url_invalid");
							// Run the calculator which records the success and failures of commands
							$this->calculateStats($error);
						}
					// Create html opening form tag
					$this->form['open_tag']		=	'<form '.trim(implode(" ",$formOpen)).'>';
					// Create html close form tag
					$this->form['close_tag']	=	'</form>';
					// Return object for method chaining option
					return $this;
				}
			
			public	function addField($settings = false)
				{
					// Get the type of field to add (text is default)
					$type	=	(!empty($settings['type']))? $settings['type'] : "text";
					// See if there are any field presets/options
					$opts	=	(!empty($settings['options']) && is_array($settings['options']))? $settings['options'] : "";
					
					if($this->errorReporting) {
							if(!empty($opts['id']))
								$this->bttnsUsed['id'][]	=	$opts['id'];
							if(!empty($opts['name']))
								$this->bttnsUsed['name'][]	=	$opts['name'];
						}
						
					// Create a buffer so as to return a compiled layout
					ob_start();
					// If the input is a real file
					if(is_file($input = NBR_RENDER_LIB.'/class.html/FormBuilder/addField/'.$type.'.php'))
						// Include it
						include($input);
					// If this is not a valid field type
					else {
							// If errors are on:
							if($this->errorReporting) {
									// Add and error spot, cacluate the success/fails, etc.
									$this	->calculateStats(1)
											// Save type to errors array
											->setErrorType('no_input_template')
											// Save the error to the queue for output later
											->errorFeedback($type,'no_input_template');
								}
						}
					// Save to mem
					$data	=	ob_get_contents();
					ob_end_clean();
					// Assign html to fields array for later assembly
					if(!empty($opts['name'])) {
							$this->form['inputs'][$opts['name']]		=	$data;
							// Save the used buttons of checks later
							$this->bttnsUsed['type'][$opts['name']]	=	$type;
						}
					else {
							$this->form['inputs'][]		=	$data;
							// Save the used buttons of checks later
							$this->bttnsUsed['type'][]	=	$type;
						}
					// Return object for method chaining
					
					return $this;
				}
			
			public	function compile($autoButton = true)
				{	
					// Stop processing if empty
					if(empty($this->form))
						return false;
						
					// If errors are turned on
					if($this->objStatus['reporting']) {
							// Calculate all the stats
							$stats	=	$this->getStats();
							// If the stats is not empty
							if(is_array($stats)) {
									// Create the error setting fuction
									if(!function_exists("get_dup_warns")) {
											// Create/record duplicate input warnings
											function get_dup_warns($v,$k,$engine)
												{
													// If the count is greater than 1
													if($v > 1) {
															// Get instance of current object
															$report	=	$engine['engine'];
															$prefix	=	$engine['use'];
															// If the input does not indicate array with []
															if(!preg_match("/[\[\]]$/",$k)) {
																	// Get current instance
																	FormBuilder::$getCount[$prefix][$k] =	$v;
																	// Create an error
																	$report	->calculateStats(1)
																			->setErrorType('dup_'.$prefix)
																			->errorFeedback("{$prefix}=\"{$k}\" x{$v}","dup_".$prefix,true);
																}
															else {
																	// If it's the same, but an array designed for multiple, just create a warning
																	$report	->calculateStats(1)
																		//	->setErrorType('dup_input')
																			->errorFeedback($k,'dup_'.$prefix);
																}
														}
												}
										}
									// Get the count for used ids (must be unique)
									$useBtns_id	=	(!empty($this->bttnsUsed['id']))? array_count_values($this->bttnsUsed['id']) : array();
									// Get the count for used names (can be unique)
									$useBtns_nm	=	(!empty($this->bttnsUsed['name']))? array_count_values($this->bttnsUsed['name']) : array();
									// Record any dups
									array_walk($useBtns_id,"get_dup_warns",array("engine"=>$this,"use"=>"id"));
									array_walk($useBtns_nm,"get_dup_warns",array("engine"=>$this,"use"=>"name"));
								}
						}
					// Assemble the html for the errors
					$error		=	($this->errorReporting)? $this->displayMessages() : "";
					// Save layout to assemble array
					$makeForm[]	=	$error.$this->form['open_tag'];
					
					// If ther is no submit button and default to add is on
					if($autoButton && !empty($this->bttnsUsed['type'])) {
							// IF there is not button already set...
							if(!in_array("submit",$this->bttnsUsed['type']) && !in_array("button",$this->bttnsUsed['type']))
								// Add a submit button
								$this->addField(array("type"=>"submit","options"=>array("value"=>"Submit")));
						}
					// Implode the guts of the fields
					$makeForm[]	=	implode(PHP_EOL,$this->form['inputs']);
					// Add the end </forms> 
					$makeForm[]	=	$this->form['close_tag'];
					$this->toHTML[]	=	implode(PHP_EOL,$makeForm);
					// Return the design
					return $this;
				}
			
			public	function toPage()
				{
					return (!empty(array_filter($this->toHTML)))? implode(PHP_EOL,$this->toHTML) : "<!-- NOTHING TO OUTPUT -->";
				}
			
			public	function calculateStats($error)
				{
					// If the error is a numeric
					if(!is_array($error))
						// Just fill an array for stats counting purposes
						$error	=	(is_numeric($error))? array_fill(0,$error,1):array(1);
					// Filter out the array with empty
					$tErrors	=	count(array_filter($error));
					// Assign a sub total
					$this->validation['total_sub'][]	=	count($error);
					// Assign the failed/warnings
					if($tErrors != 0)
						$this->validation['fail_sub'][]	=	$tErrors;
					// Calculate totals
					$this->validation['fail']		=	(!empty($this->validation['fail_sub']) && is_array($this->validation['fail_sub']))? array_sum($this->validation['fail_sub']) : 0;
					$this->validation['total']		=	(!empty($this->validation['total_sub']) && is_array($this->validation['total_sub']))? array_sum($this->validation['total_sub']) : 0;
					$this->validation['success']	=	($this->validation['total'] - $this->validation['fail']);
					// Return method for method chaining
					return $this;
				}
			
			protected	function implodeClass($array)
				{
					return (!empty($array) && is_array($array))? implode(" ",$array) : $array;
				}

			public	function getStats()
				{
					if(!empty($this->validation)) {
							$array['warning']	=	(!empty($this->validation['warning']))? $this->validation['warning'] : 0; 
							$array['error']		=	(!empty($this->validation['error']))? $this->validation['error'] : 0; 
							$array['fail']		=	(!empty($this->validation['fail']))? $this->validation['fail'] : 0; 
							$array['success']	=	(!empty($this->validation['success']))? $this->validation['success'] : 0;
							$array['total']		=	(!empty($this->validation['total']))? $this->validation['total'] : 0; 
							
							return $array;
						}
						
					return  0;
				}
			
			public	function setErrorType($val = 'unknown')
				{
					$this->validation['error'][]	=	$val;
					return $this;
				}
				
			public	function errorFeedback($var,$type = 'unknown',$is_error = false)
				{
					$root	=	$_SERVER['HTTP_HOST']."/";
					switch($type) {
							case('url_invalid') : 
								$offending	=	(!is_file($actFile = str_replace(_DS_._DS_,_DS_,NBR_ROOT_DIR._DS_.$var)))? str_replace(_DS_._DS_,_DS_,$root.str_replace(NBR_ROOT_DIR,"",$actFile)) : false;
								$color		=	"orange";
								if($offending != false)
									$this->validation['warning'][]		=	$type;
								break;
							case('url_dynamic') :
								$offending	=	(preg_match('/\.php$|\.phtml$|\.php3$|\.asp$/i',$var))? false : str_replace(_DS_._DS_,_DS_,$root.$var);
								$color		=	"red";
								if($offending != false)
									$this->validation['error'][]	=	$type;
								break;
							default:
								$color		=	"orange";
								$offending	=	$var;
								if($offending != false)
									$this->validation['warning'][]	=	$type;
						}
					$color		=	($is_error)? "red" : $color;
					$message	=	$this->getError($type);
					$offending	=	(!empty($offending))? " ({$offending})" : false;
					
					if($offending != false) {
							$fullMessage	=	'<div class="nbr_selfWarn" style="font-size: 10px; color: '.$color.'; font-family: Arial, sans-serif;">'.$message.$offending.'</div>';
							
							$this->errMessages[]	=	$fullMessage;
							
							return $fullMessage;
						}
				}
			
			public	function setValidationOpts($settings = false)
				{
					$this->validationOpts['debug']			=	(!empty($settings['debug']));
					$this->validationOpts['add_tags']		=	(!empty($settings['add_tags']));
					$this->validationOpts['add_ready']		=	(!empty($settings['add_ready']));
					$this->validationOpts['validate_hidden']	=	(isset($settings['validate_hidden']))? $settings['validate_hidden'] : true;
					
					return $this;
				}
			
			public	function useValidation($fields = false, $rules = false, $messages = false,$wp_compatable = false,$debug = false)
				{
					if(!is_array($fields) || (is_array($fields) && empty($fields)))
						return $this;
					
					if(!isset($this->validationOpts))
						$this->setValidationOpts();
					
					$jQValidate	=	($wp_compatable)? new \nUberTools\jQueryTools\jQueryValidator(\nUberTools\jQueryTools\jQueryValidator::WP_COMPAT) : new \nUberTools\jQueryTools\jQueryValidator();
					// Find form using the "name" attribute
					$jQValidate->UseForm(array("id"=>$this->formId,"debug"=>$debug));
					// Validate mulitple same-type fields. Let php write the validation code for you
					foreach($fields as $key => $value) {
							$jQValidate->SetAttr(	// Implode mulitiple fields with same type validation
													((is_array($value))? implode(",",$value) : $value),
													// Create the validation rules
													((!empty($rules[$value]) && is_array($rules[$value]))? $rules[$value] : array("required"=>true)),
													// Create the validation message(s)
													((!empty($messages[$value]) && is_array($messages[$value]))? $messages[$value] : array("required"=>'This is a required field!'))
												);
						}
					// Compile with or without <script> wrappers
					$this->jQueryValidation	=	$jQValidate->Compile(array(	"add_tags" => $this->validationOpts['add_tags'],
																			"add_ready" => $this->validationOpts['add_ready'],
																			"validate_hidden"=>$this->validationOpts['validate_hidden']));
					return $this;
				}
			
			public	function displayJQuery()
				{
					return (!empty($this->jQueryValidation))? $this->jQueryValidation : false;
				}
			
			public	function getError($val = 'unknown')
				{
					$message['url_invalid']			=	'URL Warning: Submission destination may not be valid based on root.';
					$message['url_dynamic']			=	'URL Warning: Submission destination may not be dynamic based on file extension.';
					$message['no_input_template']	=	'FIELD Warning: Invalid field type. No template for this type.';
					$message['dup_input']			=	
					$message['dup_name']				=	'FIELD Warning: Multiple fileds with same name.';
					$message['dup_id']				=	'ID Warning: Multiple fileds with same id. Will cause Javascript Errors.';
					$message['unknown']				=	"UNKNOWN Error";
					
					return (isset($message[$val]))? $message[$val] : $message['unknown'];
				}
			
			public	function displayMessages()
				{
					return (!empty($this->errMessages) && is_array($this->errMessages))? implode(PHP_EOL,$this->errMessages) : "";	
				}
			
			public	function getFieldHtml($name = false)
				{
					if(empty($name))
						return false;
					
					if(isset($this->form['inputs'][$name]))
						return $this->form['inputs'][$name];
				}
		}