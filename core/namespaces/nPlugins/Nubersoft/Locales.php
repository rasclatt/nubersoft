<?php
namespace nPlugins\Nubersoft;

class Locales extends \Nubersoft\nApp
	{
		public	function hasLocale($ID,$locale = false)
			{
				$count	=	$this->nQuery()
					->query("SELECT COUNT(*) as count FROM component_locales WHERE comp_id = $ID")
					->getResults(true);
				
				return $count['count'];
			}
	}