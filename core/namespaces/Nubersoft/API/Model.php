<?php
namespace Nubersoft\API;

class Model extends \Nubersoft\API\Core
{
	public	function getPost($args=false,$decode=false)
	{
		return self::fetch($args,$decode,true);
	}
	
	public	function getGet($args=false,$decode=false)
	{
		return self::fetch($args,$decode,false);
	}
}