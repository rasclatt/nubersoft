<?php
namespace nPlugins\Nubersoft\ShoppingCart\Model;

class	Observer extends \nPlugins\Nubersoft\ShoppingCart\Model
	{
		public	function listen()
			{
				$POST	=	$this->toArray($this->getPost());
				if(empty($POST['item']))
					return false;
				
				$item	=	preg_replace('/^qty_/','',$POST['item']);
				$Cart	=	$this->getItems($item);
				if(empty($Cart))
					return false;
				
				$this->add($item,$POST['qty'],true);
				
				$this->ajaxResponse(array(
					'html'=>array('<script>window.location="'.$this->localeUrl('/cart/').'";</script>'),
					'sendto'=>array('body')
				));
			}
	}