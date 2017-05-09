<?php
namespace nPlugins\Nubersoft\Password;

class Model extends \Nubersoft\Emailer
	{
		public	function sendPassword()
			{
				# Convert the incoming form values to array
				$formData	=	$this->getHelper('nForm')->deliverToArray();
				# Match the token
				$match		=	$this->getHelper('nToken')->resetTokenOnMatch($formData,'nProcessor','page');
				$registry	=	$this->getDefaultMessaging();
				# If match token
				if($match) {
					$userId		=	$this->validUser($formData['email']);
					//$this->ajaxResponse(array('alert'=>print_r($this->nQuery()->query("select * from users where email = :0",array($formData['email']))->getResults(),1)));
					# If there user is valid
					if($userId) {
						$tempPassword	=	$this->createTempPass();
						$emailer		=	$this->addTo($formData['email'])
											->addFrom(WEBMASTER)
											->addSubject($registry['email']['subject'])
											->useHtml()
											->addMessage(str_replace('~password~',$tempPassword,$registry['email']['message']),'default')
											->send();
						
						if($emailer) {
						
							$this->nQuery()->query("UPDATE `users` SET `reset_password` = :0, `timestamp` = '".date('Y-m-d H:i:s')."' where `ID` = '{$userId}'",array($this->passwordHash($tempPassword),));
						
							if($this->isAjaxRequest()) {
								$this->ajaxResponse(array(
									'alert'=>$registry['browser']['success'],
									'html'=>array(' ','<div class="nbr_success">Password Sent Successfully</div>'),
									'sendto'=>array('#loadspot_modal','.recover_password_request_msg')
								));
							}
							else {
								$this->saveSetting('event',array('recover_password_request'=>true));
							}
						}
						else {
							if($this->isAjaxRequest()) {
								die(json_encode(array(
									'alert'=>$registry['browser']['error'],
									'html'=>array(' '),
									'sendto'=>array('#loadspot_modal')
								)));
							}
							else {
								$this->saveSetting('event',array('recover_password_request'=>$registry['browser']['error']));
								$this->saveError('event',array('recover_password_request'=>false));
							}
						}
					}
					else {
						if($this->isAjaxRequest()) {
							die(json_encode(array(
								'alert'=>$registry['browser']['fail'],
								'html'=>array(' '),
								'sendto'=>array('#loadspot_modal')
							)));
						}
						else {
							$this->saveSetting('event',array('recover_password_request'=>$registry['browser']['fail']));
							$this->saveIncidental('event',array('recover_password_request'=>false));
						}
					}
				}
				else {
					if($this->isAjaxRequest()) {
						$msg	=	'Form token has expired or invalid. Reload page to reset.';
						die(json_encode(array(
							'alert'=>$msg,
							'html'=>array(' '),
							'sendto'=>array('#loadspot_modal')
						)));
					}
					else {
						$this->saveSetting('event',array('recover_password_request'=>$msg));
						$this->saveIncidental('event',array('recover_password_request'=>false));
					}
				}
			}
		
		private	function validUser($email)
			{
				$sql	=	"select `ID` from `users` where `email` = :0 and user_status = 'on'";
				$user	=	$this->nQuery()->query($sql,array($email))->getResults(true);
				
				return	($user != 0)? $user['ID'] : false;
			}
		
		private	function passwordHash($password)
			{
				$PasswordEngine	=	\Nubersoft\PasswordGenerator::Engine(\Nubersoft\PasswordGenerator::USE_DEFAULT);
				return $PasswordEngine->hashPassword($password)->getHash();
			}
		
		public	function getDefaultMessaging()
			{
				$array	=	$this->getMatchedArray(array('messaging','recover_password_request'),'',$this->toArray($this->getDataNode('registry')));
				
				return (!empty($array['recover_password_request'][0]))? $array['recover_password_request'][0] : array(
					'email'=>array(
						'subject'=>'Password Request Reset',
						'message'=>'Your password has been reset using this code: ~password~'
						),
					'browser'=>array(
						'success'=>'A temporary password has been sent to you.',
						'error'=>'There was an error sending your password. Please contact: '.WEBMASTER,
						'fail'=>'There was an error sending your password. You may not be registered.'
						)
					);
			}
		
		public	function createTempPass($passlen = 16)
			{
				return substr(md5(rand(100000,999999).date('YmdHis').rand(100000,999999)),1,$passlen);
			}
	}