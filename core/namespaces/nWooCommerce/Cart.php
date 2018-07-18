<?php
/**
*	@description	Shortcut class to common cart functions
*/
namespace nWooCommerce;

class Cart extends \nWooCommerce\App
{
	/**
	*	@description	By default set the cart
	*/
	public	function __construct()
	{
		$this->getCart();
	}
	/**
	*	@description	Get the contents of the cart
	*/
	public function getCartContents()
	{
		return (!empty($this->cart->cart_contents))? $this->cart->cart_contents : [];
	}
	/**
	*	@description	Update an item by the id
	*/
	public	function updateQty($pid, $qty)
	{
		# Loop the cart and extract product key
		foreach($this->getCartContents() as $pkey => $product) {
			# If the id matches, use key to update cart
			if($product['product_id'] == $pid) {
				$this->cart->set_quantity($pkey, $qty);
				return true;
			}
		}
		
		return false;
	}
	/**
	*	@description	Add item by the id, if qty = 0, item will remove
	*/
	public	function addQty($pid, $qty = 1)
	{
		# Loop the cart and extract product key
		foreach($this->getCartContents() as $pkey => $product) {
			# If the id matches, use key to update cart
			if($product['product_id'] == $pid) {
				if(empty($qty)) {
					$this->cart->remove_cart_item($pkey);
					return true;
				}
				else {
					$qty	+=	$product['quantity'];
					$this->cart->set_quantity($pkey, $qty);
					return true;
				}
			}
		}
		
		return false;
	}
	/**
	*	@description	Remove item from cart
	*/
	public	function removeItem($pid)
	{
		# Loop the cart and extract product key
		foreach($this->getCartContents() as $pkey => $product) {
			# If the id matches, use key to update cart
			if($product['product_id'] == $pid) {
				$this->cart->remove_cart_item($pkey);
				return true;
			}
		}
		
		return false;
	}
	/**
	*	@description	Add item to cart
	*/
	public	function addItem($pid, $qty = 1)
	{
		if(empty($qty))
			return false;
		
		# Loop the cart and extract product key
		foreach($this->getCartContents() as $pkey => $product) {
			# If the id matches, use key to update cart
			if($product['product_id'] == $pid) {
				$this->cart->set_quantity($pkey,($product['quantity']+$qty));
				return true;
			}
		}
		
		$this->cart->add_to_cart($pid,$qty);
		return true;
	}
	/**
	*	@description	Check if item in cart
	*/
	public	function itemInCart($pid)
	{
		# Loop the cart and extract product key
		foreach($this->getCartContents() as $pkey => $product) {
			# If the id matches, use key to update cart
			if($product['product_id'] != $pid)
				continue;
			
			return $product;
		}
		
		return false;
	}
}