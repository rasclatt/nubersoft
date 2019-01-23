<?php
namespace Nubersoft;

class	nCache extends \Nubersoft\nApp
{
	protected	$destination,
				$has_layout,
				$layout;
	
	public	function start($path_to_file)
	{
		$this->destination	=	$path_to_file;
		$this->has_layout	=	false;
		if(is_file($this->destination)) {
			$this->has_layout	=	true;
		}
		
		ob_start();
		
		return $this;
	}
	
	public	function isCached()
	{
		return $this->has_layout;
	}
	
	public	function render()
	{
		if($this->isCached()){
			include($this->destination);
		}
		$this->layout	=	ob_get_contents();
		ob_end_clean();
		
		if(!$this->isCached()) {
			file_put_contents($this->destination, $this->layout);
		}
		
		return $this->layout;
	}
}