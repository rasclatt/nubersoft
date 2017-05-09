<?php
namespace Nubersoft;

class nCookie
	{
		private	$expireTime,
				$cookieName;
		
		/*
		** @description This will set the time for the cookie to expire
		*/
		public	function setTime($time)
			{
				$this->expireTime	=	$time;
				return $this;
			}
		/*
		** @description Returns the name of the last cookie used in the instance
		*/
		public	function getName()
			{
				return $this->cookieName;
			}
		/*
		** @description This will set the name of the cookie
		*/
		public	function setName($name = false)
			{
				$this->cookieName	=	$name;
				return $this;
			}
		/*
		** @description This actually creates the cookie
		*/
		public	function setCookie($val, $name = false)
			{
				if(!empty($name))
					$this->setName($name);
				
				if(empty($this->cookieName))
					return false;
				
				$this->expireTime	=	(!empty($this->expireTime))? $this->expireTime : (time()+60*60*24*30);
				setcookie($this->cookieName,json_encode(array($this->expireTime,$val)),$this->expireTime);
			}
		/*
		** @description Self-explanatory
		*/
		public	function destroyCookie($name = false)
			{
				if(!empty($name))
					$this->setName($name);
	
				if($this->cookieExists($this->cookieName))
					setcookie($this->cookieName,null,(time()-1000));
			}
		/*
		** @description Self-explanatory
		*/
		public	function cookieExists($name = false)
			{
				if(!empty($name))
					$this->setName($name);
				
				return (isset($_COOKIE[$this->cookieName]));
			}
		/*
		** @description Self-explanatory
		*/
		public	function getCookie($name = false)
			{
				$cookie	=	$this->getCookieData($name);
				
				return (!empty($cookie[1]))? $cookie[1] : $cookie;
			}
		/*
		** @description This will get an array of the value and expire time
		*/
		public	function getCookieData($name = false)
			{
				if(!empty($name))
					$this->setName($name);
				
				return (!empty($_COOKIE[$this->cookieName]))? json_decode($_COOKIE[$this->cookieName],true) : false;
			}
		/*
		** @description Checks if the cookie is expired
		*/
		public	function isExpired($name = false)
			{
				$cookie	=	$this->getCookieData($name);
				if(!empty($cookie[0]))
					return false;
				
				return true;
			}
		/*
		** @description Gives an array for a countdown of sorts
		*/
		public	function willExpire($name = false)
			{
				$cookie	=	$this->getCookieData($name);
				$now	=	strtotime("now");
				if(!empty($cookie[0])) {
					$seconds	=	($now - $cookie[0]);
					return	array(
								'h'=>trim(number_format(($seconds/60/60),0),'-'),
								'm'=>trim(number_format(($seconds/60),0),'-'),
								's'=>trim($seconds,'-')
							);
				}
				
				return true;
			}
		/*
		** @description Resets the expire time on the cookie
		*/
		public	function extendTime($time,$name=false)
			{
				$cookie	=	$this->getCookieData($name);
				$this->setTime($time)->setCookie($cookie[1]);
			}
	}