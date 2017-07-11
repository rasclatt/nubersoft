<?php
namespace nPlugins\Nubersoft;

class ComponentEditor extends \Nubersoft\nRender
	{
		protected	$useData;
		
		public	function saveAjaxRequest()
			{
				if($this->isAjaxRequest()) {
					$this->nQuery()
						->update("components")
						->set(array("content"=>$this->getPost('content')))
						->where(array("ID"=>$this->getPost('ID')))
						->write();
					
					die(json_encode(array("saved"=>true)));
				}
			}
		
		public	function getLayout($name)
			{
				$name	=	ucfirst(strtolower($name));
				$path	=	__DIR__.DS.'ComponentEditor'.DS.'renderlib'.DS."get{$name}".DS.'index.php';
				if(is_file($path)) {
					if(!$this->isAdmin()) {
						echo '403 Forbidden';
						//http_response_code(403);
					}
					else
						include($path);
				}
				else
					echo '404 File not found';
					//http_response_code(404);
				
				exit;
			}
	}