<?php
namespace nWordpress\Email;

class Template extends \nWordpress\Email\Model
{
	public	function getTemplate()
	{
		$args	=	func_get_args();
		$path	=	(!empty($args[0]))? __DIR__.DS.'Template'.DS.'renderlib'.DS.$args[0] : false;
		$data	=	(!empty($args[1]))? $args[1] : false;
		
		$this->setTemplateTrue();
		
		if(!is_numeric($path)) {
			$replace	=	preg_replace_callback('/~[^~]+~/i',function($v) use ($data){
				
				if(stripos($v[0],'data::') !== false){
					$item	=	preg_replace('/data::|~/i','',$v[0]);
					return (isset($data[$item]))? $data[$item] : false;
				}
				else {
					return (new \nWordpress\Automator())->automate($v[0]);
				}
				
			},file_get_contents($path));
			
			return $replace;
		}
		else {
			
		}
	}
}