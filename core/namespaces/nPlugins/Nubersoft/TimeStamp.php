<?php
namespace nPlugins\Nubersoft;

class TimeStamp extends \Nubersoft\nApp
	{
		protected	$db;

		public		$row_count,
					$_time,
					$users;
		
		private		$run;
		
		public	function __construct($_time = 80)
			{
				# Interval
				$this->_time	=	$_time;
				# Save and open database
				$this->initialize();
				return parent::__construct();
			}
		
		public	function initialize()
			{
				if(!empty($this->getDataNode('_SESSION')->username)) {
					# Record into db
					$this->record($this->getDataNode('_SESSION')->username);
					# Check db, print users
					//$this->users	=	$this->getResults($this->getDataNode('_SESSION')->username);
				}
			}
		
		public	function setRunState($run)
			{
				$this->run	=	$this->getBoolVal($run);
				return $this;
			}
		
		public	function setRunStateTrue()
			{
				$this->setRunState(true);
				return $this;
			}
		
		public	function adminTools()
			{
				if($this->isAdmin()) { 
					require_once(__DIR__.DS.'Timestamp'.DS.'adminTools.php');
				}	
			}
		
		# This should grab all users connected
		public	function fetch()
			{
				if(empty($this->run))
					return false;
					
				$interval = $this->_time;
				
				# This is just checking a time range and collecting names
				# You may want to make a new function that will then take the return list and query your user info table to get the user info
				$now	=	date("Y-m-d H:i:s",strtotime("now"));
				$query	=	$this->nQuery();
						
				if(!$query)
					return false;
				
				$users	=	$query	->select()
									->from("members_connected")
									->addCustom("where timestamp > DATE_SUB('$now', INTERVAL $interval MINUTE)")
									->orderBy(array("timestamp"=>"DESC"))
									->fetch();
				
				# This should get the count
				$this->row_count	=	(isset($users))? count($users):0;
				
				# Return if users are available
				return (isset($users))? $users:0;
			}
		
		public	function record($_user)
			{
				if(!empty($this->getDataNode('timestamp_logged')))
					return $this->getDataNode('timestamp_logged');
				
				$ip	=	$this->getDataNode('_SERVER')->REMOTE_ADDR;
				
				$payload["timestamp"]	=	date("Y-m-d H:i:s",strtotime("now"));
				$payload["unique_id"]	=	$this->fetchUniqueId();
				$payload["ip_address"]	=	$ip;
				$payload["username"]	=	(!empty($this->getDataNode('_SESSION')->username))? $this->getDataNode('_SESSION')->username : $ip;
				try {
					$query			=	$this->nQuery();
					
					if(!$query)
						return false;
						
					$connected		=	$query	->query("INSERT into `members_connected` (`unique_id`,`username`,`timestamp`,`ip_address`) VALUES (:0,:1,:2,:3) ON DUPLICATE KEY UPDATE `timestamp` = :4, `ip_address` = :5",array(
						$payload["unique_id"],
						$payload["username"],
						$payload['timestamp'],
						$payload["ip_address"],
						$payload['timestamp'],
						$payload["ip_address"]
					));
					
					$this->saveSetting('timestamp_logged',true);
				}
				catch (Exception $e){
					$this->autoload(array('create_default_timestamp'));
					create_default_timestamp(array("create"=>true));
					$this->saveSetting('timestamp_logged',true);
				}
			}
	}