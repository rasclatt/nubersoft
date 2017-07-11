<?php
namespace Nubersoft\Accessibility;

use \Nubersoft\Methodize as App;

class	View extends \Nubersoft\nRender
	{
		protected	$App;
		
		public	function __construct()
			{
				$this->App	=	new App();
				
				return parent::__construct();
			}
		# Creates a skip link for repeatable content
		public	function createSkipLink($id,$content)
			{
				# Creates the a link
				$id	=	preg_replace('/[^a-zA-Z0-9\_]/','',str_replace(' ','_',strtolower($id)));
				# Creates the html elements, both a link and anchor
				$this->App->saveAttr('s_link','<a href="#'.$id.'" class="accessibility"><img src="'.$this->imagesUrl('/accessibility/empty.gif').'" class="accessibility" alt="'.$content.'"></a>');
				$this->App->saveAttr('a_link','<a name="'.$id.'"></a>');
				
				return $this;
			}
		
		public	function createImage($path,$alt,$options = '')
			{
				$options	=	(is_array($options))? ' '.implode(' ',$options) : $options;
				return '<img src="'.$path.'" alt="'.$alt.'"'.$options.' />';
			}
		
		# Uses the methodizer to make magic recall data
		public	function __call($name,$args=false)
			{
				return $this->App->{$name}(...$args);
			}
	}