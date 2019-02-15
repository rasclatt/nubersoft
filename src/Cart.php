<?php
namespace Nubersoft;

class Cart extends nSession
{
	public	function addToCart($sku, $qty = 1)
	{
		$cart	=	$this->getCart(true);
		
		if(!isset($cart[$sku]))
			$cart[$sku]['qty']	=	$qty;
		else
			$cart[$sku]['qty']	+=	$qty;
		
		$this->set('cart', $cart);
		
		return $this;
	}
	
	public	function remove($sku, $qty = false)
	{
		return $this->removeFromCart($sku, $qty);
	}
	
	public	function removeFromCart($sku, $qty = false)
	{
		$cart	=	$this->getCart(true);
		
		if(isset($cart[$sku])) {
			if(!empty($qty)) {
				$cart[$sku]['qty']	-=	$qty;
			}
			else
				unset($cart[$sku]);
		}
		
		if(isset($cart[$sku]) && $cart[$sku]['qty'] <= 0)
			unset($cart[$sku]);
		
		$this->set('cart', $cart);
		
		return $this;
	}
	
	public	function getCart($destroy = false)
	{
		$cart	=	$this->get('cart');
		
		if(!empty($cart) && $destroy)
			$this->destroy('cart');
		
		return (!empty($cart))? $cart : [];
	}
	
	public	function setSkuAttr($sku, $key, $attr)
	{
		$cart	=	$this->getCart(true);
		
		if(isset($cart[$sku])) {
			$cart[$sku][$key]	=	$attr;
		}
		$this->set('cart', $cart);
		return $this;
	}
	
	public	function setTitle($sku, $title)
	{
		$this->setSkuAttr($sku, 'title', $title);
		return $this;
	}
	
	public	function setPrice($sku, $price)
	{
		$this->setSkuAttr($sku, 'price', $price);
		return $this;
	}
	
	public	function itemInCart($sku)
	{
		$cart	=	$this->getCart();
		
		return (isset($cart[$sku]))? $cart[$sku]['qty'] : 0;
	}
	
	public	function emptyCart()
	{
		$this->clearCart();
		return $this;
	}
	
	public	function clearCart()
	{
		$this->destroy('cart');
		return $this;
	}
	
	public	function getTotalItems()
	{
		return count($this->getCart());
	}
	
	public	function getTotalUnits()
	{
		$count	=	0;
		foreach($this->getCart() as $sku => $item) {
			$qty	=	(!isset($item['qty']))? 0 : $item['qty'];

			$count	+=	$qty;
		}
		
		return $count;
	}
	
	public	function setSubTotals(&$cart)
	{
		foreach($cart as $sku => $item) {
			$cart[$sku]['subtotal']	=	$this->sumSku($sku);
		}
	}
	
	public	function getCartSummary($func = false)
	{
		$cart	=	$this->getCart();
		$this->setSubTotals($cart);
		
		$totals	=	[
			'total_items' => $this->getTotalItems(),
			'total_units' => $this->getTotalUnits(),
			'cart_items' => $cart,
			'total_price' => $this->getCartTotal()
		];
		
		ksort($cart);
		return (is_callable($func))? $func($totals) : $totals;
	}
	
	public	function sumSku($sku)
	{
		$cart	=	$this->getCart();
		
		if(empty($cart))
			return 0;
		elseif(!isset($cart[$sku]))
			return 0;
		
		if(isset($cart[$sku]['qty']) && isset($cart[$sku]['price'])) {
			if(is_numeric($cart[$sku]['price']) && is_numeric($cart[$sku]['qty']))
			   return $cart[$sku]['price'] * $cart[$sku]['qty'];
		}
		return 0;
	}
	
	public	function getCartTotal($key = 'subtotal')
	{
		$cart	=	$this->getCart();
		$this->setSubTotals($cart);
		$sum	=	[];
		foreach($cart as $sku => $item) {
			if(isset($item[$key]))
				$sum[]	=	$item[$key];
		}
		
		return array_sum($sum);
	}
}