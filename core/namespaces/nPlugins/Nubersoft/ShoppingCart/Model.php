<?php
namespace nPlugins\Nubersoft\ShoppingCart;

class Model extends \Nubersoft\ShoppingCart
	{
		/*
		**	@description	Fetches the status of the cart
		*/
		public	function isActive()
			{
				$registry	=	$this->getRegistry('cart_options');
				
				return	(!empty($this->getBoolVal($registry['active'])));
			}
		
		public	function getCatelogueByCategory($on = false)
			{
				if($on)
					$on	=	"(`page_live` != '' AND `page_live` != 'off' AND `for_sale` != '') AND";
				$LOCALE		=	(!empty($this->getLocale()))? $this->getLocale() : 'USA';
				return $this->getPrefFile('cart_products_category_'.$LOCALE,array('save'=>true),false,function($path,$nApp) use ($on,$LOCALE) {
					$all	=	$nApp->nQuery()->query("SELECT *, CONCAT(file_path,file_name) as image_path FROM cart_products WHERE {$on} `product_sku` IN (select `product_sku` from `cart_products_locales` WHERE `locale_abbr` = '{$LOCALE}')")->getResults();
					return (is_array($all))? $nApp->organizeByKey($all,'product_category',array('multi'=>true)) : array();
				});
			}
		
		public	function getCatalog()
			{
				return $this->getCatelogue();
			}
		
		public	function getCatelogue()
			{
				$LOCALE		=	(!empty($this->getLocale()))? $this->getLocale() : 'USA';
				$catalog	=	 $this->getPrefFile('cart_products_'.$LOCALE,array('save'=>true),false,function($path,$nApp) use ($LOCALE) {
					return $nApp->nQuery()->query("SELECT *, CONCAT(file_path,file_name) as image_path FROM cart_products WHERE `product_sku` IN (select `product_sku` from `cart_products_locales` WHERE `locale_abbr` = '{$LOCALE}')")->getResults();
				});
				
				return $catalog;
			}
		
		public	function getCatalogueBySku($sku)
			{
				$sku	=	$this->getPrefFile('cart_product_sku_'.$sku,array('save'=>true),false,function($path,$nApp) use ($sku) {
					return $nApp->nQuery()->query("SELECT *, CONCAT(file_path,file_name) as image_path FROM cart_products WHERE `product_sku` = '{$sku}'")->getResults(true);
				});
				# For whatever reason these "on" values get turned to bool (1), converting back
				if(isset($sku['page_live']) && !is_string($sku['page_live']))
					$sku['page_live']	=	($sku['page_live'] == 1)? 'on' : 'off';
				
				if(isset($sku['for_sale']) && !is_string($sku['for_sale']))
					$sku['for_sale']	=	($sku['for_sale'] == 1)? 'on' : 'off';
					
				return $sku;
			}
			
		public	function toMoney($number,$exchBase = 'USD')
			{
				if($this->getLocale() == $exchBase)
					return $number;
				
				$Currency	=	$this->getHelper('Currency');
				$exchCurr	=	$this->getCurrencySymbol();
				$priceExch	=	$Currency->convert(array('from'=>$exchBase,'to'=>$exchCurr,'value'=>$number));
				$value		=	$Currency->toMoney($priceExch,$exchCurr,true);
				
				return (empty(preg_replace('/[^1-9]/','',$value)) && !empty(preg_replace('/[^1-9]/','',$number)))? $number : $value;
			}
			
		public	function getCurrencySymbol($locale=false)
			{
				if(empty($locale))
					$locale	=	$this->getLocale();
					
				return $this->getCurrencyConverter($locale);
			}
	}