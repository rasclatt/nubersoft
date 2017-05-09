<?php
namespace nPlugins\Magento;

class API extends \Nubersoft\nApp
	{
		protected	$endpoint,
					$apikey,
					$apiuser,
					$apisession,
					$SOAP;
		
		public	function startApp($url,$apiuser,$apikey)
			{
				$this->endpoint		=	$url;
				$this->apikey		=	$apikey;
				$this->apiuser		=	$apiuser;
				$this->SOAP			=	new \SoapClient($this->endpoint);
				$this->apisession	=	$this->SOAP->login($this->apiuser,$this->apikey);
				
				return $this;
			}
			
		public	function getSessionKey()
			{
				return $this->apisession;
			}
			
		public	function getConnection()
			{
				return $this->SOAP;
			}
		
		public	function getUserList()
			{
				return $this->doService('customer.list');
			}
		
		public	function doService($service,$inject=false)
			{
				return $this->SOAP->call($this->getSessionKey(),$service,$inject);
			}
			
		public	function endSession()
			{
				$this->SOAP->endSession($this->apisession);
			}
	}