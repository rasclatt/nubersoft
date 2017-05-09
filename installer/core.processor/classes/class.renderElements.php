<?php
	class renderElements
		{
			public	$styles;
			public	$pref_table_array;
			public	$filterArray;
			public	$applyFilter;
			public	$finalStyle;		
			public	$payload;
			public	$returnFinal;
			public	$render;
			public	$applyURL;
			public	$renderURL;
			
			private	static	$singleton;
			private	$style;
			
			public	function __construct()
				{
					if(empty(self::$singleton))
						self::$singleton	=	$this;
						
					return self::$singleton;
				}
				
			// Array required
			public function generateStyles($pref_table_array, $_filter	=	array('ID','unique_id','parent_id','ref_anchor','ref_page','component_type','content','_id','a_href','login_view','login_permission','page_order','page_live','admin_tag','admin_notes','email_id','class','file_path','file_name','file_size','file','admin_lock'))
				{
					$this->pref_table_array	=	(is_array($pref_table_array))? $pref_table_array:array();
					$this->styles			=	array(0=>'');
					
					// Filter out the variables for this element and build a styles string  
					foreach($this->pref_table_array as $keys => $values) {
						$filterit	=	(in_array($keys, $_filter))? true:false;
						
						// Delete all styles unrelated to the styles string
						if(!empty($values) && $filterit !== true) {
							// Trim off front underscore
							$keys			=	ltrim($keys,"_");
							// Replace underscores with dashes for web-friendly style use
							$keys			=	str_replace('_', '-', $keys);
							// Fix box shadow element
							if($keys == 'box-shadow') {
								$compatibility	=	array('box-shadow', '-moz-box-shadow', '-webkit-box-shadow');
								for($bs = 0; $bs <= 2; $bs++) {
									$this->styles[0]	.=	" $compatibility[$bs]: " . $values . ";";
								}
							}
							else {
								if(!is_array($values) && strtolower($values) != 'null')
									$this->styles[0]	.=	$keys.': '.$values.';';
							}
						}
					}
					
					$this->finalStyle	= implode("", $this->styles);
					
					return $this->finalStyle;
				}
			
			public	function renderURLElement($applyURL)
				{
					$this->renderURL	=	array();
					$this->applyURL		=	$applyURL;
					
					// Set rules for a URL	
					if(!empty($this->applyURL)) {
						$this->renderURL[0]			=	'<a href="' . Safe::decode($this->applyURL) . '" title="' . Safe::decode($this->applyURL) . '">';
						$this->renderURL[1]			=	'</a>';
					}
				}
			
			public	function renderContentElements($payload, $filterArray)
				{
					if(!function_exists("nQuery"))
						AutoloadFunction('nQuery');
					
					$unique_id	=	NubeData::$settings->page_prefs->unique_id;
					$nubquery	=	nQuery();
					
					// Sets the array var
					$this->payload					=	$payload;

					$this->filterArray				=	$filterArray;
					
					// Generate styles from the above function
					$this->generateStyles($this->payload/*, $this->filterArray*/);
					
					// Creates an array to return in final assembly
					$this->returnFinal				=	array();
					
					// Set class
					if(!empty($this->payload['class']))
						$this->payload['class']		=	' class="' . $this->payload['class'] . '"';
						
					// Set content
					if(!empty($this->payload['content']))
						$this->payload['content']	=	Safe::decode($this->payload['content']);
					
					// Set Class to set or empty
					$this->payload['class']			=	(isset($this->payload['class']))? $this->payload['class']: '';
					
					// Assign content just incase it's empty
					$this->payload['content']		=	(isset($this->payload['content']))? $this->payload['content']: '';
						
					// Set rules for TEXT INPUT
					if(isset($this->payload['component_type']) && $this->payload['component_type']	== 'text') {
							$setStyle	=	(empty($this->finalStyle))? 'span': 'div';
							$this->returnFinal[0]		=	"<$setStyle " . $this->payload['class'] . ' style="' . $this->finalStyle . '">' . Safe::decode($this->payload['content']) . "</$setStyle>";
						}
					// Set rules for CODE INPUT
					elseif(isset($this->payload['component_type']) && $this->payload['component_type']	== 'code') {
							$this->returnFinal[0]		=	'<div ' . $this->payload['class'] . ' style="' . $this->finalStyle . '">' . $this->payload['content'] . '</div>';
						}
					// Set rules for IMAGE INPUT
					elseif(isset($this->payload['component_type']) && $this->payload['component_type']	== 'image') {
						if(isset($this->payload['file_path']) && !empty($this->payload['file_path'])) {
							$this->returnFinal[0]		=	'<img src="' . $this->payload['file_path'] . $this->payload['file_name'] . '"' . $this->payload['class'] . ' style="' . $this->finalStyle . '" />'; 
						}
						else {
							$file_check					=	$nubquery	->select(array("file","file_path"))
																		->from("image_bucket")
																		->where(array("ref_page"=>$unique_id,"ID"=>$this->payload['ID']))
																		->fetch();
																		
							$file_check_res				=	$file_check[0];
							$file_check_dir				=	($file_check !== 0)? str_replace($_SERVER['DOCUMENT_ROOT'], "", $file_check_res['file_path']): '/client_assets/images/default/';
							$this->returnFinal[0]		=	'<img src="' . $file_check_dir . $file_check_res['file'] . '"' . $this->payload['class'] . ' style="' . $this->finalStyle . '" />';
						}
					}
					// Set rules for BUTTON INPUT
					elseif(check_empty($this->payload,'component_type','button'))
							$this->returnFinal[0]		=	'<a ' . $this->payload['class'] . ' href="' . Safe::decode($this->payload['a_href']) . '" style="' . $this->finalStyle . '">' . $this->payload['content'] . '</a>';
							
					// Set rules for EMAIL INPUT
					elseif(check_empty($this->payload,'component_type','form_email')) { 
						$this->returnFinal[0]		=	'<div ' . $this->payload['class'] . ' style="' . $this->finalStyle . '">' . $this->SimpleEmailer() . '</div>';
					}
					
					if(isset($this->payload['component_type']) && (!check_empty($this->payload,'login_view','on') || !isset($this->payload['login_view']))) {
						$renderSet		=	true;
						$setStage		=	'1';
					}
					else {
						$perms	=	(isset($this->payload['login_permission']))? $this->payload['login_permission']:'';
						
						if(empty($perms)) {
							$renderSet		=	true;
							$setStage		=	'2';
						}
						else {
							$renderSet		=	allow_if($this->payload['login_permission']);
							//((isset($_SESSION['usergroup']) && ($this->payload['login_permission'] >= $_SESSION['usergroup'])));
							$setStage		=	'3';
						}
					}
					
					$this->render	=	($renderSet)? implode("", $this->returnFinal): '';
					
					return $this->render;

				}
			
			public	function Render($payload, $style)
				{
					global $unique_id;
					global $con;
					global $nubsql;
					AutoloadFunction("check_empty");
					
					// Sets the array var
					$this->payload			=	$payload;
					$this->style			=	$style;
					
					// Creates an array to return in final assembly
					$this->returnFinal				=	array();
					
					// Set class
					if(!empty($this->payload['class']))
						$this->payload['class']		=	' class="' . $this->payload['class'] . '"';
						
					// Set content
					if(!empty($this->payload['content']))
						$this->payload['content']	=	Safe::decode($this->payload['content']);
					
					// Set Class to set or empty
					$this->payload['class']			=	(isset($this->payload['class']))? $this->payload['class']: '';
						
					// Set rules for TEXT INPUT
					if(check_empty($this->payload,'component_type','text')) {
						$setStyle	=	(empty($this->style))? 'span': 'div';
						$this->returnFinal[0]		=	"<$setStyle " . $this->payload['class'] . ' style="' . $this->style . '">' . Safe::decode($this->payload['content']) . "</$setStyle>";
					}
					// Set rules for CODE INPUT
					elseif(check_empty($this->payload,'component_type','code')) {
						$this->returnFinal[0]		=	'<div ' . $this->payload['class'] . ' style="' . $this->style . '">' . $this->payload['content'] . '</div>';
					}
					// Set rules for IMAGE INPUT
					elseif(check_empty($this->payload,'component_type','image')) {
						if(!empty($this->payload['file_path'])) {
							$this->returnFinal[0]		=	'<img src="'.$this->payload['file_path'].$this->payload['file_name'].'"'.$this->payload['class'].' style="'.$this->style.'" />'; 
						}
						else {
							$file_check					=	nQuery()	->select(array("file", "file_path"))
																		->from("image_bucket")
																		->where(array("ref_page"=>$unique_id,"div_id"=>$this->payload['ID']))
																		->fetch();
																		
							$file_check_res				=	$file_check[0];
							$file_check_dir				=	($file_check !== 0)? str_replace(NBR_ROOT_DIR, "", $file_check_res['file_path']): '/client_assets/images/default/';
							$this->returnFinal[0]		=	'<img src="' . $file_check_dir . $file_check_res['file'] . '"' . $this->payload['class'] . ' style="' . $this->style . '" />';
						}
					}
					// Set rules for BUTTON INPUT
					elseif(check_empty($this->payload,'component_type','button'))
							$this->returnFinal[0]		=	'<a ' . $this->payload['class'] . ' href="' . Safe::decode($this->payload['a_href']) . '" style="' . $this->style . '">' . $this->payload['content'] . '</a>';
							
					// Set rules for EMAIL INPUT
					elseif(check_empty($this->payload,'component_type','form_email')) { 
						$this->returnFinal[0]		=	'<div ' . $this->payload['class'] . ' style="' . $this->style . '">' . $this->SimpleEmailer() . '</div>';
					}
					
					if(isset($this->payload->component_type) && $this->payload->login_view !== 'on') {
						$renderSet		=	true;
						$setStage		=	'1';
					}
					else {
						$perms	=	(isset($this->payload['login_permission']))? $this->payload['login_permission']:'';
						
						if(empty($perms)) {
							$renderSet		=	true;
							$setStage		=	'2';
						}
						else {
							$renderSet		=	allow_if($this->payload['login_permission']);
							$setStage		=	'3';
						}
					}
					
					$this->render	=	($renderSet)? implode("", $this->returnFinal): '';
					
					return $this->render;

				}
				
			public	function SimpleEmailer()
				{
					if(check_empty($this->payload,'page_live','on')) {
						$email_id	=	(isset($this->payload['email_id']) && !empty($this->payload['email_id']))? ($this->payload['email_id']): 'default';
						
						if (isset($_REQUEST['email']) && $this->payload['unique_id'] == $_REQUEST['component_id']) {
								
								$email_new[$this->payload['unique_id']]	=	new sendEmailEngine;
								//if "email" is filled out, proceed / check if the email address is invalid
								$mailcheck	=	$email_new[$this->payload['unique_id']]->spamcheck($_REQUEST['email']);
								
								if ($mailcheck==FALSE) {
										$render_array = '
		<h2 style="font-size: 25px; color: #C00;">Invalid/missing input!</h2>
        <form method="post" action="#" enctype="multipart/form-data">
		<div class="email_form_fields"><input name="email" type="text" onFocus="this.value=\'\'" value="Your email address"  style="width: 96%;"><br /></div>
        <div class="email_form_ta"><textarea name="question" type="text" style="height: inherit; width: 96%;"></textarea></div>
        <input type="hidden" name="component_id" value="' . $this->payload['unique_id'] . '" />
        <input type="hidden" name="email_id" value="'.$email_id.'" />
		<div class="formButton"><input disabled="disabled" type="submit" value="SEND"></div>
	</form>';
									}
                                  else {
                                        $defEmail	=	$nubsql->fetch("select * from emailer where email_id = '$email_id' and page_live ='on'");
                                        if($defEmail !== 0) {
                                                include_once($_SERVER['DOCUMENT_ROOT'] . "/core/includes/classes/browser.commands/string.include.php");
                                                $result		=	$defEmail[0];
                                                $messageObj	=	strFilter::decode($result['content'], "~", "::");
                                                
                                                // Iterate through the returned ArrayObject to make a normalized array
                                                $iterator 	=	new RecursiveIteratorIterator(new RecursiveArrayIterator($messageObj));
                                                
                                                // Start array
                                                $message	=	'';
                                                // Build array
                                                foreach($iterator as $key=>$value) {
                                                        $message .= $value;
                                                    }
                                                
                                                $return_copy	=	(isset($result['return_copy']) && $result['return_copy'] == 'on')? true: false;
                                                $return_address	=	(isset($result['return_address']) && !empty($result['return_address']))? $result['return_address']: $this->payload['a_href'];
                                            }
                                        else {
                                                $return_copy	=	false;
                                                $return_address	=	$this->payload['a_href'];
                                                $message		=	Safe::encode($_REQUEST['question']);
                                            }
                                            
                                       
                                        //send email
                                        $header		=	'MIME-Version: 1.0' . "\r\n";
                                        $header		.=	'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                                        $header		.=	"From: $return_address \r\n";
                                        $email		=	$_REQUEST['email'];
                                        $to			=	$this->payload['a_href'];
                                        
                                        mail($to, "Subject: Online Question Submission", $message, $header );
                                        mail($email, "Subject: Online Question Submission", $message, $header );
                                        
                                       $render_array = (isset($result['return_response']) && !empty($result['return_response']))? Safe::decode($result['return_response']): "<h2>Thank you for using our mail form</h2>";
                                    }
                                }
                            else {
								$render_array = '
	<form method="post" action="#" enctype="multipart/form-data">
		<div class="email_form_fields"><input name="email" type="text" value="Your email address" onFocus="this.value=\'\'" style="width: 96%;"><br /></div>
        <div class="email_form_ta"><textarea name="question" type="text" cols="30" rows="5" onFocus="this.value=\'\'" style="height: inherit; width: 96%;"></textarea></div>
        <input type="hidden" name="component_id" value="'.$this->payload['unique_id'].'" />
        <input type="hidden" name="email_id" value="'. $email_id.'" />
		<div class="formButton"><input disabled="disabled" type="submit" value="SEND"></div>
	</form>';			
								}
					}
					
					if(isset($render_array))
						return $render_array;
				}
		}

?>