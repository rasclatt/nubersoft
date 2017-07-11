<?php
namespace nPlugins\Nubersoft\ShoppingCart;

class View extends \Nubersoft\nRender
	{
		private	$layout;
		
		public	function minifiedCheckOut()
			{
				return $this->renderPluginElement('nbr_mini_check_out','#login_wrapper',array(
					'fx'=>array('slideDown'),
					'acton'=>array('#login_wrapper')
				));
			}
			
		public	function loadAddressForm()
			{
				return $this->renderPluginElement('nbr_cart_address_form','#login_wrapper',array(
					'fx'=>array('slideDown'),
					'acton'=>array('#login_wrapper')
				));
			}
			
		protected	function renderPluginElement($name,$ajax,$array = false)
			{
				$layout	=	$this->useTemplatePlugin($name);
				if($this->isAjaxRequest()) {
					$base	=	array(
						'html'=>array($layout),
						'sendto'=>array($ajax)
					);
					$arr	=	(is_array($array))? array_merge($array,$base) : $base;
					$this->ajaxResponse($arr);
				}
					
				return $layout;
			}
		
		public	function getCartFields(\Nubersoft\nForm $Form, $settings = false,$options = false)
			{
				$priority		=	(empty($settings['priority']))? 1 : $settings['priority'];
				$type			=	(empty($settings['contact_type']))? 'shipping' : $settings['contact_type'];
				$strip			=	(!empty($settings['strip']));
				$this->layout	=	array();
				$locale			=	strtoupper(trim($this->getSession('LOCALE'),'/'));
				$array	=	array(
					'ID'=>'',
					'unique_id'=>'',
					'username' => 'Username',
					'dist_id' => 'Distributor Id',
					'contact_type' => '',
					'first_name' => 'First Name',
					'last_name' => 'Last Name',
					'address_1' => 'Address 1',
					'address_2' => 'Address 2 (Optional)',
					'city' => 'City',
					'country' => 'Country',
					'region' => ((empty($locale) || $locale == 'USA')? 'State' : 'Province'),
					'postcode' =>  ((empty($locale) || $locale == 'USA')? 'ZIP' : 'Post Code'),
					'email' => $this->getSession('email'),
					'phone_1' => 'Primary Phone #',
					'phone_2' => 'Secondary (Optional)',
					'content' => '',
					'priority' => '',
					'page_live' => ''
					
				);
				
				$values	=	array(
					'ID'=> $this->fetchData('ID'),
					'unique_id'=> $this->fetchData('unique_id'),
					'username' => ($this->fetchData('username'))? $this->fetchData('username') : $this->getSession('email'),
					'dist_id' => $this->getSession('dist_id'),
					'contact_type' => ($this->fetchData('contact_type'))? $this->fetchData('contact_type') : $type,
					'first_name' => $this->fetchData('first_name'),
					'last_name' => $this->fetchData('last_name'),
					'address_1' => $this->fetchData('address_1'),
					'address_2' => $this->fetchData('address_2'),
					'city' => $this->fetchData('city'),
					'country' => $this->fetchData('country'),
					'region' => $this->fetchData('region'),
					'postcode' => $this->fetchData('postcode'),
					'email' => ($this->fetchData('email'))? $this->fetchData('email') : $this->getSession('email'),
					'phone_1' => $this->fetchData('phone_1'),
					'phone_2' => $this->fetchData('phone_2'),
					'content' => $this->fetchData('content'),
					'priority' => ($this->fetchData('priority'))? $this->fetchData('priority') : $priority,
					'page_live' => ($this->fetchData('page_live'))? $this->fetchData('page_live') : 'on'
					
				);
				
				$format	=	array(
					'ID' => 'fullhide',
					'unique_id' => 'fullhide',
					'username' => 'fullhide',
					'dist_id' => 'fullhide',
					'contact_type' => 'fullhide',
					'first_name' => 'text',
					'last_name' => 'text',
					'address_1' => 'text',
					'address_2' => 'text',
					'city' => 'text',
					'country' => 'select',
					'region' => 'select',
					'postcode' => 'text',
					'email' => 'fullhide',
					'phone_1' => 'text',
					'phone_2' => 'text',
					'content' => 'fullhide',
					'priority' => 'fullhide',
					'page_live' => 'fullhide'
				);
				
				foreach($array as $col => $value) {
					$method	=	$format[$col];
					if($col == 'contact_type')
						$values[$col]	=	$type;
					elseif($col == 'priority')
						$values[$col]	=	1;

					if($method == 'select') {
						$title	=	(strpos($col,'_') !== false)? $this->colToTitle($col) : ucwords($col);
						if($title == 'Region')
							$title	=	'State/Prov';
						$opts	=	(isset($options[$col]))? $options[$col] : array('~NULL~'=>'Select '.$title);
						$field	=	$Form->{$method}(array("name"=>"{$type}[{$col}]","value"=>$values[$col],'id'=>$type.'_'.$col,"options"=>$opts));
						$this->layout[]	=	'<div class="nbr_select">'.str_replace('~NULL~','',(($strip)? strip_tags($field,'<input><select><option><optgroup><textarea>') : $field)).'</div>';
					}
					else {
						$field	=	$Form->{$method}(array("name"=>"{$type}[{$col}]",'id'=>$type.'_'.$col,"value"=>$values[$col],"placeholder"=>$value));
						$this->layout[]	=	str_replace('~NULL~','',(($strip)? strip_tags($field,'<input><select><option><optgroup><textarea><lable>') : $field));
					}
					
				}
				
				return $this;
			}
		
		public	function getLayoutByType($return=false)
			{
				if(empty($this->layout))
					return false;
				
				if(is_bool($return) && empty($return))
					return $this->layout;
				
				return implode($return,$this->layout);
			}
			
		public	function placeRegion($option = true)
			{
				if(!$this->isAjaxRequest())
					return;
				$cou		=	$this->toArray($this->getPost('deliver'));
				$sendto		=	$cou['sendto'];
				$country	=	$cou['country'];
				$regions	=	$this->getPlugin('\nPlugins\Nubersoft\ShoppingCart\Model')->getRegions($country);
				$str[]		=	'<option value="">Select State/Province</option>';
				
				if(!empty($regions)) {
					foreach($regions as $key => $value) {
						$str[]	=	'<option value="'.$key.'">'.$value.'</option>';
					}
				}
				
				$string	=	(!empty($str))? implode(PHP_EOL,$str) : '<option value="">NA</option>';
				
				$this->ajaxResponse(array(
					'html'=>array(
						$string
					),
					'sendto'=>array(
						$sendto
					)
				));
			}
	}