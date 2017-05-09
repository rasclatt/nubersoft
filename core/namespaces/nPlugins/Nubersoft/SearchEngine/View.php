<?php
namespace nPlugins\Nubersoft\SearchEngine;

class View extends \nPlugins\Nubersoft\SearchEngine
	{
		public	function highlightSearch($content,$search = false,$class="nbr_found")
			{
				if(empty($search))
					return $content;
				
				return preg_replace_callback('/'.$search.'/i',function($match) use ($class){
					return '<span class="'.$class.'">'.$match[0].'</span>';
				},$content);
			}
		
		public	function getAllButResults()
			{
				$data	=	$this->toArray($this->getStats());
				if(isset($data['data']['results']))
					unset($data['data']['results']);
				
				return (empty($data['data']) || !is_array($data['data']))? array() : $data['data'];
			}
			
		public	function getResults()
			{
				$data	=	$this->toArray($this->getStats());
				return (isset($data['data']['results']))? $data['data']['results'] : array();
			}
	}