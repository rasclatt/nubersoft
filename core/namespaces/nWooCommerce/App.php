<?php
namespace nWooCommerce;

class App extends \Nubersoft\nApp
{
	protected	$cart;
	
	public	function getCart()
	{
		$this->cart	=	WC()->cart;
		
		return $this->cart;
	}
	
	public	function getCountries()
	{
		return (new \WC_Countries())->__get('countries');
	}
}