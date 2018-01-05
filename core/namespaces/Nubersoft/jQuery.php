<?php
namespace Nubersoft;

class jQuery extends \Nubersoft\nApp
{
	private	$js		=	[];
	private	$handle	=	'$';
	private	$func	=	[];
	
	public	function open($attr='')
	{
		if(!empty($attr))
			$attr	=	' '.$attr;
		$this->js['wrap_open']	=	'<script'.$attr.'>';	
		return $this;
	}
	
	public	function close()
	{
		$this->js['wrap_close']	=	'</script>';	
		return $this;
	}
	
	public	function docReady($type='$')
	{
		$this->handle		=	$type;
		$this->js['open']	=	$type.'(function('.$this->handle.'){';
		$this->js['close']	=	'});';
		return $this;
	}
	
	public	function toAjax($data)
	{
		$thisObj				=	$this->func;
		$this->js['script'][]	=	$this->handle.'.ajax('.preg_replace_callback('/"\{\{[^\{\}]+\}\}"/',function($matched) use ($thisObj){
			$key	=	str_replace(['{','}'],'',trim($matched[0],'"'));
			return (isset($thisObj[$key]))? $thisObj[$key] : '';
		},json_encode($data)).');';
		return $this;
	}
	
	public	function createAjax($data)
	{
		$thisObj				=	$this->func;
		return $this->handle.'.ajax('.preg_replace_callback('/"\{\{[^\{\}]+\}\}"/',function($matched) use ($thisObj){
			$key	=	str_replace(['{','}'],'',trim($matched[0],'"'));
			return (isset($thisObj[$key]))? $thisObj[$key] : '';
		},json_encode($data)).');';
	}
	
	
	public	function createFunc($name,$string,$e='')
	{
		$this->func[$name]	=	'function('.$e.'){
			'.$string.'
		}';
		return $this;
	}
	
	public	function getFunc($name)
	{
		return $this->func[$name];
	}
	
	public function create()
	{
		$compile	=	[];
		if(!empty($this->js['wrap_open']))
			$compile[]	=	$this->js['wrap_open'];
		
		if(!empty($this->js['open']))
			$compile[]	=	$this->js['open'];
		
		$compile[]	=	implode(PHP_EOL,$this->js['script']);
		
		if(!empty($this->js['close']))
			$compile[]	=	$this->js['close'];
		
		if(!empty($this->js['wrap_close']))
			$compile[]	=	$this->js['wrap_close'];
		
		
		return implode(PHP_EOL,$compile);
	}
	/*
	** @param	[array] $settings	This allows the method to decide what to include for output to browser
	*/
	public	function getLibary($settings = false)
	{
		$jq		=	(isset($settings['jq']))? $settings['jq'] : '3.1.1';
		$ui		=	(isset($settings['ui']))? $settings['ui'] : false;
		$val	=	(isset($settings['validate']))? $settings['validate'] : false;
		ob_start();
?>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/<?php echo $jq; ?>/jquery.min.js"></script>
<?php			if($ui) {
?><script type="text/javascript" src="//code.jquery.com/ui/<?php echo $ui; ?>/jquery-ui.js"></script>
<?php			}
			if($val) {
?><script type="text/javascript" src="//ajax.aspnetcdn.com/ajax/jquery.validate/<?php echo $val; ?>/jquery.validate.js"></script>
<?php			}

		$data	=	ob_get_contents();
		ob_end_clean();

		return $data;
	}
	
	public	function listenForChange($name,$function)
	{
		$this->js['script'][$name]	=	$this->handle.'("'.$name.'").on("change",function(e){
			'.$function.'
		});';
		
		return $this;
	}
}