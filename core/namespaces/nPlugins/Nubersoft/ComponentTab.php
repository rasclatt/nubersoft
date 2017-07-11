<?php
namespace nPlugins\Nubersoft;

class ComponentTab
	{
		private	$useData,
				$data,
				$nApp;
		
		public	function __construct(\Nubersoft\nApp $nApp)
			{
				$this->nApp		=	$nApp;
				$this->nImage	=	$this->nApp->getHelper('nImage');
				$this->nHtml	=	$this->nApp->getHelper('nHtml');
			}
		/*
		**	@description	Determines if the editor is on or off
		*/
		public	function getEditorStatus()
			{
				if(!empty($this->nApp->getSession('admintools')->editor))
					return ($this->nApp->getSession('admintools')->editor == 'on');
				
				return false;
			}
		
		public	function getLayout()
			{
				try {
					return $this->toolBar();
					exit;
				}
				catch(\Exception $e) {
					if($this->nApp->isAdmin()) {
						die($e->getMessage());
					}
				}
			}
		
		public	function getData()
			{
				return $this->nApp->getHelper('App')->getData();
			}
		
		public	function __call($name,$args = false)
			{
				if(!$this->nApp->isAdmin())
					return;
				
				$this->data['unique_id']	=	$this->nApp->getPage('unique_id');
				$this->useData				=	$args;
				$this->nApp->getHelper('Methodize')->saveAttr('component_tab_data',$this->data);
				if(!include(__DIR__.DS.'ComponentTab'.DS.$name.DS.'index.php')) {
					echo printpre();
					throw new \Exception("File could not be found: {$name}");
				}
			}
	}