<?php
namespace Nubersoft\SearchEngine;

class View extends \Nubersoft\SearchEngine
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
		$data	=	$this->toArray($this->getData());
		if(isset($data['data']['results']))
			unset($data['data']['results']);

		return (empty($data['data']) || !is_array($data['data']))? [] : $data['data'];
	}

	public	function getResults()
	{
		$data	=	$this->toArray($this->getData());
		return (!empty($data['data']['results']) && is_array($data['data']['results']))? $data['data']['results'] : [];
	}
}