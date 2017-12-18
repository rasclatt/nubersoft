<?php
namespace Nubersoft;

class Ajax extends \Nubersoft\nApp
	{
		/*
		**	@desciption	Fetches the ajax container name
		*/
		public	function getContainer($key = false)
			{
				$main		=	(!empty($key))? $key : 'main';
				$default	=	'#loadspace';
				$registry	=	$this->getRegistry('ajax');
				
				if(empty($registry))
					return $default;
				
				$find	=	$this->findKey($registry,$main)->getKeyList();
				
				return (!empty($find[0]))? $find[0] : $default;
			}
	}