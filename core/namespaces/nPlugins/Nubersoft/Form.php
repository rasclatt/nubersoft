<?php
namespace nPlugins\Nubersoft;

class Form extends \Nubersoft\nForm
	{
		protected	$optionMenu,
					$assocCol,
					$selOpt;
		
		public	function getOptions($selected = false, $addSelect = 'off', $page_live = true)
			{
				if(empty($this->assocCol)) {
					trigger_error('You are missing the associated column name to generate a proper option array.',E_USER_NOTICE);
					
					return array();
				}
				
				if($addSelect) {
					if(!is_array($this->optionMenu))
						$this->optionMenu	=	array();
					
					if(empty($this->selOpt)) {
						$this->selOpt		=	true;
						$this->optionMenu	=	array_merge(array(
							array(
								'name'=>'Select',
								'value'=>((!is_bool($addSelect))? $addSelect : '' ),
								'page_order'=> 0)),$this->optionMenu);
					}
				}
				
				$opts	=	array();
				foreach($this->optionMenu as $i => $row) {
					if($selected == $row['value'])
						$opts[$i]	= array_merge($row,array('selected'=> true));
					else
						$opts[$i]	=	$row;
				}
				
				return $opts;
			}
			
		public	function createOptions($assoc_column,$page_live = true)
			{
				$this->assocCol		=	$assoc_column;
				$this->optionMenu	=	array();
				
				if(empty($this->optionMenu)) {
					$sql	=	"SELECT
									`ID`,
									`menuVal` as `value`,
									`menuName` as `name`,
									`page_order`
								FROM
									`dropdown_menus`
								WHERE
									`assoc_column` = '{$this->assocCol}'";
					
					if($page_live)
						$sql	.=	" AND `page_live` = 'on'";
					
					$sql	.=	" ORDER BY `page_order` ASC";
					
					$this->optionMenu	=	$this->nQuery()->query($sql)->getResults();
				}
				
				return $this;
			}
			
		public	function fetchOpts()
			{
				return $this->optionMenu;
			}
	}