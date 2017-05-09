<?php
namespace nPlugins\Nubersoft;

class Render extends \Nubersoft\nRender
	{
		public	function renderPlugin()
			{
				$args	=	func_get_args();
				if($this->isAjaxRequest()) {
					$this->ajaxResponse(array(
						'sendto'=>array($args[0][1]),
						'html'=>array($this->useTemplatePlugin($args[0][0]))
					));
				}
			}
	}