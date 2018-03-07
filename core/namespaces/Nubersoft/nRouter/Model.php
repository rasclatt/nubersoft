<?php
namespace Nubersoft\nRouter;

class Model extends \Nubersoft\nRouter
{
	protected $path;
	
	public	function getPageById($id,$key=false)
	{
		$find	=	'*';
		if(!empty($key))
			$find	=	'`'.implode('`,`',$key).'`';

		$sql	=	"SELECT {$find} FROM `main_menus` WHERE ID = :0";
		return $this->nQuery()->query($sql,array($id))->getResults(true);
	}

	public	function getPage()
	{
		$args		=	func_get_args();
		$var		=	(!empty($args[0]))? $args[0] : false;
		$pageURI	=	$this->getDataNode('pageURI');
		if(!empty($pageURI)) {
			if(!empty($var))
				return (!empty($pageURI->{$var}))? $pageURI->{$var} : false;

			return $pageURI;
		}
	}
}