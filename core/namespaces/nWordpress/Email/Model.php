<?php
namespace nWordpress\Email;

class Model extends \Nubersoft\nApp
{
	protected	$layout,
				$to,
				$from,
				$bcc,
				$cc,
				$message,
				$subject;
	
	private		$isHtml;
	
	public	function setTo($email)
	{
		$email	=	trim($email);
		
		if(filter_var($email,FILTER_VALIDATE_EMAIL))
			$this->to[]	=	$email;
		else
			throw \Exception('Invalid "TO" email.');
			
		return $this;
	}
	public	function setFrom($email)
	{
		$email	=	trim($email);
		
		if(filter_var($email,FILTER_VALIDATE_EMAIL))
			$this->from[]	=	$email;
		else
			trigger_error('Invalid "FROM" email',E_USER_WARNING);
			
		return $this;
	}
	public	function setCc($email)
	{
		$email	=	trim($email);
		
		if(filter_var($email,FILTER_VALIDATE_EMAIL))
			$this->cc[]	=	$email;
		else
			trigger_error('Invalid "CC" email',E_USER_WARNING);
			
		return $this;
	}
	public	function setBcc($email)
	{
		$email	=	trim($email);
		
		if(filter_var($email,FILTER_VALIDATE_EMAIL))
			$this->bcc[]	=	$email;
		else
			trigger_error('Invalid "BCC" email',E_USER_WARNING);
			
		return $this;
	}
	public	function setMessage($content)
	{
		$this->message	=	$content;
		
		return $this;
	}
	public	function setSubject($subj)
	{
		$this->subject	=	$this->safe()->encodeSingle($subj);
		return $this;
	}
	public	function send()
	{
		if(empty($this->to)){
			trigger_error('Email can not be empty.',E_USER_NOTICE);
			return false;
		}
		$header	=	'';
		
		if($this->isHtml) {
			$header	.=	"MIME-Version: 1.0".PHP_EOL;
			$header	.=	"Content-type:text/html;charset=UTF-8".PHP_EOL;
		}
		
		$header	.=	'From:'.implode(',',$this->from).PHP_EOL;
		
		if(!empty($this->cc))
			$header	.=	'CC:'.implode(',',$this->cc).PHP_EOL;
		if(!empty($this->bcc))
			$header	.=	'BCC:'.implode(',',$this->bcc).PHP_EOL;
		
		return mail(implode(',',$this->to),$this->subject,$this->message,$header);
	}
	
	public	function setTemplateTrue()
	{
		$this->isHtml	=	true;
	}
}