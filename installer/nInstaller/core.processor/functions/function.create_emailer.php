<?php
/*Title: create_emailer()*/
/*Description: This function creates and emailer application. It can be accessed using standard `PHP` code. Use `app_emailer()` for a markup version.*/
/*Example: 
`create_emailer(array("info"=>array("from"=>"me@example.com")));`
*/

	function create_emailer($settings = array('class'=>'emailer', "attributes"=>"","info"=>array(),"layout"=>false))
		{
			register_use(__FUNCTION__);
			// Autoload required functions
			AutoloadFunction('check_empty,ValidateToken,render_alert,nQuery');
			$nubquery	=	nQuery();
					
			/*********************************/
			/********* POST VALUES *********/
			/*********************************/
		
			$REQUEST['email_id']			=	(!empty($_POST['email_id']))? $_POST['email_id']:false; // Email ID
			$REQUEST['from']				=	(!empty($_POST['email']))? $_POST['email']:false; // Submitter Email Address
			$REQUEST['subject']				=	(!empty($_POST['subject']))? $_POST['subject']:"Online Submission"; // Submitter Subject
			$REQUEST['question']			=	(!empty($_POST['question']))? $_POST['question']:false; // Submitter Message
			$token							=	(!empty($_POST['token']) && !empty($_POST['token']['email']))?	$_POST['token']['email'] : false; // Form token generated everytime the form is rendered to page
			$command						=	check_empty($_POST,'command','emailer'); // Checks if the email process trigger is set

			/*********************************/
			/********* INPUT SETTINGS ********/
			/*********************************/
			
			$settings['info']					=	(!empty($settings['info']))? array_filter($settings['info']):array(); // Form/Processing Settings
			$settings['info']['email_id']		=	(!empty($settings['info']['email_id']))? $settings['info']['email_id']:false; // Check if this has id
			$settings['info']['a_href']			=	(!empty($settings['info']['a_href']))? $settings['info']['a_href']:WEBMASTER; // Default owner address
			$settings['info']['subject']		=	(!empty($settings['info']['subject']))? $settings['info']['subject']:"Online Submission"; // Default subject
			$settings['info']['layout']			=	(!empty($settings['info']['layout']))? $settings['info']['layout']:false; // File set for layout
			$settings['info']['resp_subj']		=	(!empty($settings['info']['resp_subj']))? $settings['info']['resp_subj']:"Thank You!"; // Default response message
			$settings['info']['resp_mess']		=	(!empty($settings['info']['resp_mess']))? $settings['info']['resp_mess']:"Your email has been sent. A representative will contact you shortly."; // Default response message
			$settings['info']['reply']			=	(check_empty($settings['info'],'reply','1'))? true:false; // Force send reply if true
			$settings['info']['html']			=	(!empty($settings['info']['html']))? $settings['info']['html']:false; // Force send reply if true
			$settings['attributes']				=	(!empty($settings['attributes']))? $settings['attributes']:false; // Force send reply if true
			$settings['info']['html_id']		=	(!empty($settings['info']['html_id']))? ' id="'.$settings['info']['html_id']."'":false; // Assign Id
			$settings['info']['html_class']		=	(!empty($settings['info']['html_class']))? ' class="'.$settings['info']['html_class']."'" : false; // Assign CSS Class
			$settings['info']['html_data_n']	=	(!empty($settings['info']['html_data_n']))? $settings['info']['html_data_n']: false; // data attribute name
			$settings['info']['html_data_v']	=	(!empty($settings['info']['html_data_v']))? $settings['info']['html_data_v']: false;
			
			$settings['info']['html_data']		=	($settings['info']['html_data_n'] != false)? ' data-'.$settings['info']['html_data_n'].'="'.$settings['info']['html_data_v'].'"':false; // data attribute value
			$settings['info']['unique_id']		=	(!empty($settings['info']['unique_id']))? $settings['info']['unique_id']:false;
			
			/*********************************/
			/****** SUCCESS MESSAGING ********/
			/*********************************/
			
			// Messages displayed on the success page
			$settings['info']['success_h']	=	(check_empty($settings['info'],'success_h'))? $settings['info']['success_h']:"Thank you for submitting!";
			$settings['info']['success_b']	=	(check_empty($settings['info'],'success_b'))? $settings['info']['success_b']:"Your message has been submitted, you will be contacted shortly.";
			
			$settings['info']['error_h']	=	(check_empty($settings['info'],'error_h'))? $settings['info']['error_h']:"Whoops!";
			$settings['info']['error_b']	=	(check_empty($settings['info'],'error_b'))? $settings['info']['error_b']:"Invalid or required information was supplied.";
			
			$settings['info']['invalid_h']	=	(check_empty($settings['info'],'invalid_h'))? $settings['info']['invalid_h']:"Program Error!";
			$settings['info']['invalid_b']	=	(check_empty($settings['info'],'invalid_b'))? $settings['info']['invalid_b']:"Please check your installation of the nUberSoft Framework. This module is not working properly.";

			// If the command is true
			// If email-from is filled
			// If question is filled
			// If token is valid
			if($command && ($REQUEST['from'] != false) && ($REQUEST['question'] != false) && (ValidateToken('email',$token))) {
					
					// Load the email library tool
					$Emailer	=	new Emailer();
					// Call response so as not to draw error
					$response	=	false;
					// If there is an email id to load
					if($settings['info']['email_id'] != false) {
							// Load email
							$response	=	$nubquery	->select(array("return_address","return_response","content_back","return_copy"))
														->from("emailer")
														->where(array("email_id"=>$settings['info']['email_id']))
														->fetch();
							$response	=	($response != 0)? $response[0]:false;
						}

					// Assign return email data
					$return['to']			=	(!empty($response['return_address']))? $response['return_address']:false;
					$return['success']		=	(!empty($response['return_response']))? $response['return_response']:"Thank you for submitting!";
					$return['content_back']	=	(!empty($response['content_back']))? $response['content_back']:false;
					$return['return_copy']	=	(check_empty($response,'return_copy',true))? true:false;

					// Run the emailer
					$Emailer	->AddTo(array($settings['info']['a_href']))
								->AddFrom(array($REQUEST['from']))
								->AddSubject($settings['info']['subject'])
								->AddMessage(array("message"=>$REQUEST['question']));
					
					$set_reply	=	($return['return_copy'] == 'on' || $settings['info']['reply'] == true)? true:false;
					
					// Send a reply
					if($set_reply) {
							$ret_resp['to']			=	$REQUEST['email'];
							$ret_resp['subject']	=	$settings['info']['resp_subj'];
							$ret_resp['message']	=	$settings['info']['resp_mess'];
							$Emailer->ReturnMessage($ret_resp);
						}
					
					$Emailer	->Compile()
								->Send($set_reply);
			
					// Check if the sent is successful
					if(isset($Emailer->response['primary']['sent']))
						$sent	=	$Emailer->response['primary']['sent'];
					// Assign a try variable
					$try	=	true;
				}
			
			if($command)
				$try	=	true;
			
			$sent		=	(isset($sent))? $sent:false;
			$format		=	!empty($settings['layout']);
			$includer	=	($settings['info']['layout'] != false && is_file($inc_file = ROOT_DIR.$settings['info']['layout']))? $inc_file : RENDER_LIB.'/assets/html/template.email.standard.php';
			
			if($sent) {
					
					$REQUEST['transmit']	=	"Sent Successfully.";
					$REQUEST['timestamp']	=	date("Y-m-d H:i:s");
					$REQUEST['template']	=	str_replace(ROOT_DIR,"",$includer);
					$REQUEST['user_agent']	=	$_SERVER['HTTP_USER_AGENT'];
					$REQUEST['user_ip']		=	$_SERVER['REMOTE_ADDR'];
					$nubquery	->insert("components")
								->columnsValues(array("content","ref_spot"),array("content"=>json_encode($REQUEST),"ref_spot"=>"emailer"))
								->write();
				}
			
			ob_start(); ?>
			<div<?php echo " ".$settings['attributes']; echo $settings['info']['html_id']; echo $settings['info']['html_class']; echo $settings['info']['html_data']; ?>>
				<?php
				// See if html output is specified
				if($settings['info']['html'] != false)
					echo Safe::decode($settings['info']['html']);
				// Check once more before adding
				elseif(is_file($includer))
					include($includer);
				else { ?>
				<h2>{invalid_head}</h2>
				<p>{invalid_body}</p>
				<?php } ?>
			</div>
			<?php 
			$data	=	ob_get_contents();
			ob_end_clean();
			
			// Preg for {message} markup
			$messaging["success_head"]	=	$settings['info']['success_h'];
			$messaging["success_body"]	=	$settings['info']['success_b'];
			
			$messaging["error_head"]	=	$settings['info']['error_h'];
			$messaging["error_body"]	=	$settings['info']['error_b'];
			
			$messaging["invalid_head"]	=	$settings['info']['invalid_h'];
			$messaging["invalid_body"]	=	$settings['info']['invalid_b'];
			
			return render_alert($data,$messaging);
		} ?>