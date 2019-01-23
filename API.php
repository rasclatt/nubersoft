<?php
namespace Nubersoft;

class API extends \Nubersoft\nApp
	{
		public	function listen()
			{
				$request	=	$this->toArray($this->getRequest());
				$service	=	(isset($request['service']))? $request['service'] : false;
				$apikey		=	(isset($request['apikey']))? $request['apikey'] : false;
				
				if($this->validApiKey($apikey)) {
					switch($service) {
						case('Verify.Info'):
							$this->getBanned($request);
							break;
						case('Verify.Record'):
							$this->banUser($request);
							break;
					}
				}
				
				$this->ajaxResponse(array("msg"=>"error"));
			}
		/*
		**	@description	Checks to see if someone is banned by IP or other
		*/
		public	function getBanned($POST)
			{
				$ID	=	(isset($POST['dist_id']) && is_numeric($POST['dist_id']))? $POST['dist_id'] : false;
				$IP	=	(!empty($POST['ip']))? $POST['ip'] : false;
				
				if(!$ID && !$IP)
					$this->ajaxResponse(array("msg"=>"error"));
				
				$ipBan	=	
				$idBan	=	0;
				
				if($IP) {
					$ipBan	=	$this->nQuery()
						->query('SELECT dist_id,ip_address FROM `api_mxi_banned_ips` WHERE dist_id = :0 OR ip_address = :1',array($ID,$IP))
						->getResults();
				}
				
				if($ID) {
					$idBan	=	$this->nQuery()
						->query('SELECT dist_id FROM `api_mxi_banned_list` WHERE dist_id = :0',array($ID))
						->getResults();
				}
						
				echo (empty($idBan) && empty($ipBan))? '0' : '1';
				exit;
			}
		/*
		**	@description	Bans user
		*/
		public	function banUser($POST)
			{
				$ID		=	(isset($POST['dist_id']) && is_numeric($POST['dist_id']))? $POST['dist_id'] : false;
				$IP		=	(!empty($POST['ip']))? $POST['ip'] : false;
				$MID	=	(!empty($POST['mage_id']))? $POST['mage_id'] : false;
				
				if(!$ID && !$IP && !$MID)
					$this->ajaxResponse(array("msg"=>"error"));
				
				$def	=	(!empty($IP))? $IP : $MID;
				
				$this->nQuery()->query("INSERT INTO `api_mxi_banned_ips` (`unique_id`,`dist_id`,`ip_address`) VALUES('".$this->fetchUniqueId()."',:0,:1)",array($def,$IP));
				
				$this->nQuery()->query("INSERT INTO `api_mxi_banned_list` (`unique_id`,`dist_id`) VALUES('".$this->fetchUniqueId()."',:0)",array($def));
				$this->ajaxResponse(array('msg'=>'ok'));
			}
			
		public	function validApiKey($key)
			{
				$ID	=	$this->nQuery()->query('select `ID` from api where apikey = :0',array($key))->getResults(true);
				return ($ID != 0);
			}
	}