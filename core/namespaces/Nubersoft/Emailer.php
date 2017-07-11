<?php
namespace Nubersoft;

class	Emailer extends \Nubersoft\nApp
	{
		public		$sent,
					$response,
					$sending;
		
		protected	$addresses,
					$headers,
					$reply,
					$notification;
		
		public	function __construct()
			{
				if(!defined('WEBMASTER'))
					define('WEBMASTER',"no-reply@".$_SERVER['HTTP_HOST']);
				
				return parent::__construct();
			}
		
		public	function getAllLayouts()
			{
				return $this->nQuery()->query("select * from `components` where `email_id` != ''")->getResults();
			}
		
		public	function getLayout($name = 'default')
			{
				$content	=	$this->nQuery()->query("select `content` from `emailer` where `email_id` = :0",array($name))->getResults();
				
				if(isset($content[0]['content']))
					$html	=	$this->safe()->decode($content[0]['content']);
				elseif(is_file($layout = NBR_CORE.DS.'settings'.DS.'default'.DS.'messaging'.DS.'email'.DS.$name.'.txt'))
					$html	=	file_get_contents($layout);
				
				return	(isset($html))? $html : '';
			}
		
		public	function addTo($from)
			{
				$this->sending['to']	=	(is_string($from))? $from : implode(',',$from);
				return $this;
			}
		
		public	function addAttr($key,$value)
			{
				$this->sending[$key]	=	$value;
				return $this;
			}
		
		public	function addFrom($from)
			{
				$fProc						=	((is_array($from))? implode(',',$from) : $from);
				$this->sending['raw_from']	=	$fProc;
				$this->sending['header'][]	=	'From: '.$fProc;
				return $this;
			}
			
		public	function addHeader($string)
			{
				$this->sending['header'][]	=	$string;
				return $this;
			}
			
		public	function addBcc($string)
			{
				$string						=	(is_array($string))? implode(',',$string) : $string;
				$this->sending['header'][]	=	"Bcc:".$string;
				return $this;
			}
			
		public	function addSubject($string)
			{
				$this->sending['subject']	=	$string;
				return $this;
			}
		
		public	function addMessage($message, $layout = 'default')
			{
				if(!empty($layout)) {
					$html		=	str_replace(array('~message~'),array($message),$this->getLayout($layout));
					$nAutomator	=	$this->getHelper('nAutomator',$this);
					$this->sending['message']	=	$nAutomator->matchFunction($html);
				}
				else
					$this->sending['message']	=	$message;
				
				return $this;
			}
		
		public	function addRawMessage($message)
			{
				$this->sending['raw_message']	=	$message;
				
				return $this;
			}
		
		public	function useHtml()
			{
				$this->sending['header'][]	=	'MIME-Version: 1.0';
				$this->sending['header'][]	=	'Content-type: text/html; charset=iso-8859-1';
				$this->sending['is_html']	=	true;
				return $this;
			}
			
		public	function send()
			{
				return mail($this->sending['to'],$this->sending['subject'],wordwrap($this->sending['message'],70,PHP_EOL),implode(PHP_EOL,$this->sending['header']));
			}
		
		public	function saveReceipt()
			{
				$columns	=	array('unique_id','content','ref_spot','page_live');
				$values		=	array("'".$this->fetchUniqueId()."'",':0',"'email_receipt'","'on'");
				$args		=	func_get_args();
				
				if(!empty($args[0])) {
					$columns[]	=	'ref_anchor';
					$values[]	=	"'{$args[0]}'";
				}
				
				$sql	=	"INSERT INTO
								`components` (`".implode('`,`',$columns)."`)
							VALUES (".implode(", ",$values).")";
				
				$this->nQuery()->query($sql,array(json_encode($this->sending)));
			}
		
		public	function getReceiptMessage($layout)
			{
				$arr	=	$this->nQuery()->query("select `return_response` from `emailer` where `email_id` = :0",array($layout))->getResults(true);
				
				return (!empty($arr['return_response']))? $arr['return_response'] : false;
			}
			
		public	function resetSendArray()
			{
				$this->sending	=	array();
				
				return $this;
			}
	}