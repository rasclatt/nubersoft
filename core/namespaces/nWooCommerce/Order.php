<?php
namespace nWooCommerce;

class Order extends \Nubersoft\nApp
{
	private		$raw_order;
	protected	$filter		=	[
		'first_name',
		'last_name',
		'company',
		'email',
		'phone',
		'address_1',
		'address_2',
		'city',
		'state',
		'postcode',
		'country'
	];
	
	public	function getOrder($id)
	{
		$arr	=	[];
		if(!function_exists('wc_get_order')){
			trigger_error('Woocommerce appears to not be available.',E_USER_NOTICE);
			return $arr;
		}
		
		$this->raw_order	=
		$WC					=	wc_get_order($id);
		
		if(empty($WC))
			return $arr;
		
		$data	=	$WC->get_data();
		
		foreach($data as $key => $value) {
			if(stripos($key,'meta_') !== false)
				continue;
			
			if(stripos($key,'line_') !== false) {
				foreach($value as $skey => $sval) {
					$arr['items'][]	=	$sval->get_data();
				}
			}
			elseif(stripos($key,'shipping_') !== false && is_array($value)) {
				foreach($value as $skey => $sval) {
					$ship_data	=	$sval->get_data();
					foreach($ship_data as $shkey => $shval) {
						if(stripos($shkey,'meta_') !== false)
							unset($ship_data[$shkey]);
					}
					$arr['shipping_data'][]	=	$ship_data;
				}
			}
			elseif(is_object($value)) {
				continue;
			}
			elseif(is_array($value)) {
				foreach($value as $skey => $sval) {
					$arr[$key."_".$skey]	=	$sval;
				}
			}
			else {
				$arr[$key]	=	$value;
			}
		}
		ksort($arr);
		return $arr;
	}
	
	public	function getRawOrder()
	{
		return $this->raw_order;
	}
	
	public	function createOrder($args)
	{
		
		$baseDir	=	$this->getBaseSettingsDir();
		
		# Used to map two arrays
		$ArrayWorks	=	new \nWordpress\ArrayWorks();
		$mapPath	=	$baseDir.DS.'mapping'.DS;
		$shipping	=	(!empty($args['shipping']))? $this->stripPrefix($args['shipping'],'shipping') : false;

		if(empty($shipping)) {
			trigger_error('Shipping is not set.',E_USER_NOTICE);
		//	return false;
		}
		
		$billing	=	(!empty($args['billing']))? $this->stripPrefix($args['billing'],'billing') : false;
		$itemcode	=	(!empty($args['item']['id']))? $args['item']['id'] : false;
		$itemQty	=	(!empty($args['item']['qty']))? $args['item']['qty'] : 1;
		$msg		=	(!empty($args['msg']))? $args['msg'] : 'Order created';
		
		if(empty($itemcode)) {
			trigger_error('You need to set an item by id.',E_USER_NOTICE);
			//return false;
		}
		
		$Wordpress	=	new \nWordpress\User();
		$Parser		=	$Wordpress->getHelper('nRegister');
		$userid		=	$Wordpress->getUserId();
		$bMap		=	$Parser->parseXmlFile($mapPath.'woo.invoice.bill.xml');
		$shipping	=	$ArrayWorks->mapFields($Parser->parseXmlFile($mapPath.'woo.invoice.ship.xml'),$shipping);
		
		$billing	=	(!empty($billing))? $ArrayWorks->mapFields($bMap,$billing) : $this->getBillingMeta($userid,array_keys($bMap));
		
		$order		=	wc_create_order();
		$order->add_product(get_product($itemcode), $itemQty);
		$order->set_address($billing, 'billing');
		$order->set_address($shipping, 'shipping');
	//	$payment_gateways = WC()->payment_gateways->payment_gateways();
	//	$order->set_payment_method($payment_gateways['bacs']);
		$order->calculate_totals();
		$order->update_status('completed', $msg.' - ');
		$id	=	$order->save();
		update_post_meta($id,'_customer_user',$userid);
		
	}
	
	protected	function stripPrefix($array,$prefix)
	{
		$row	=	[];
		foreach($array as $key => $value) {
			$key		=	preg_replace('/^'.$prefix.'/','',$key);
			$row[$key]	=	$value;
		}
		
		return $row;
	}
	
	public	function getBillingMeta($userid,$map=false)
	{
		
		if(empty($map)) {
			$Parser		=	$this->getHelper('nRegister');
			$map		=	array_keys($Parser->parseXmlFile($this->getBaseSettingsDir().DS.'woo.invoice.bill.xml'));
		}
		
		$row	=	[];
		foreach($map as $key) {
			$row[$key]	=	get_user_meta($userid, 'billing_'.$key, true);
		}
		
		return $row;
	}
	
	protected	function getBaseSettingsDir()
	{
		return (!defined('THEME_CLIENT_SETTINGS'))? $this->getPlugin('\nWordpress\Router')->getThemeDirectory(DS.'client'.DS.'settings') : THEME_CLIENT_SETTINGS;
	}
}