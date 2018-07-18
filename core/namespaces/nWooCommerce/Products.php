<?php
namespace nWooCommerce;

class Products extends \nWooCommerce\App
{
	private	static	$currprod	=	[];
	
	public	function getCartCount()
	{
		return (function_exists('WC'))? $this->safe()->encodeSingle($this->getCart()->get_cart_contents_count()) : 0;
	}
	
	public	function hasItems()
	{
		return ($this->getCartCount() > 0);
	}
	/**
	*	@desctription	Fetches the products
	*/
	public	function get()
	{
		$args		=	func_get_args();
		# Check if the parameter is filled
		if(!empty($args[0])) {
			# If it's filled with the object already, just return the data
			if($args[0] instanceof \WC_Product_Simple) {
				$product	=	$args[0]->get_data();
				self::$currprod[$product['id']]	=	$product;
				
				return self::$currprod[$product['id']];
			}
			else
				$query	=	(is_numeric($args[0]))? wc_get_product($args[0]) : new \WC_Product_Query($args[0]);
		}
		else
			$query	=	new \WC_Product_Query();
		
		if(method_exists($query,'get_products')) {
			$products	=	$query->get_products();
		}
		else {
			self::$currprod[$args[0]]	=	$query->get_data();
			return self::$currprod[$args[0]];
		}
		
		$attr		=	[
			'id',
			'name',
			'slug',
			'date_created',
			'date_modified',
			'status',
			'featured',
			'catalog_visibility',
			'description',
			'short_description',
			'sku',
			'price',
			'regular_price',
			'sale_price',
			'date_on_sale_from',
			'date_on_sale_to',
			'total_sales',
			'tax_status',
			'tax_class',
			'manage_stock',
			'stock_quantity',
			'stock_status',
			'backorders',
			'sold_individually',
			'weight',
			'length',
			'width',
			'height',
			'upsell_ids',
			'cross_sell_ids',
			'parent_id',
			'reviews_allowed',
			'purchase_note',
			'attributes',
			'default_attributes',
			'menu_order',
			'virtual',
			'downloadable',
			'category_ids',
			'tag_ids',
			'shipping_class_id',
			'downloads',
			'image_id',
			'gallery_image_ids',
			'download_limit',
			'download_expiry',
			'rating_counts',
			'average_rating',
			'review_count'
		];

		$row	=	[];

		if(empty($products))
			return $row;
		# Loop products
		foreach($products as $item => $product){
			# Loop attributes and build array
			foreach($attr as $val) {
				if(isset($product->{$val}))
					$row[$product->id][$val]	=	$product->{$val};
			}
			# Fetch image from media
			$img	=	wp_get_attachment_image_src(get_post_thumbnail_id($product->id), 'full' );
			# Rename their stupid keys
			$row[$product->id]['image']['full'] = [
				'url' => (!empty($img[0]))? $img[0] : false,
				'height' => (!empty($img[1]))? $img[1] : false,
				'width' => (!empty($img[2]))? $img[2] : false,
			];
		}
		
		return self::$currprod	=	$row;
	}
	
	public	function getCurrent($id=false,$column=false)
	{
		if(!empty($id)) {
			if(empty($column))
				return (isset(self::$currprod[$id]))? self::$currprod[$id] : [];
			else {
				if(!empty(self::$currprod)) {
					foreach(self::$currprod as $idval => $product) {
						if(isset($product[$column]) && $product[$column] == $id)
							return $product;
					}
				}
				
				return [];
			}
		}
		
		return self::$currprod;
	}
	/**
	*	@description	Fetch product by sku
	*	@param	$sku	[string|int] The sku value for an item
	*	@param	$key	[string|int] Return a key from the array if available
	*	@returns		boolean (false) | array/object mix | value
	*/
	public	function getBySku($sku,$key=false)
	{
		if(!empty(self::$currprod[$sku]))
			return self::$currprod['sku_'.$sku];
		
		$item	=	$this->get(['sku'=>$sku]);
		
		if(empty($item))
			return false;
		
		$array	=	$item[key($item)];
		$item	=	($array['sku'] == $sku)? $array : false;
		
		if(empty($item))
			return false;
		
		self::$currprod['sku_'.$sku]	=	$item;
		
		if(!empty($key))
			return (isset($item[$key]))? $item[$key] : false;
		
		return $item;
	}
	
	public	static	function getPageProduct($key=false)
	{
		global $product;
		
		$data	=	(new Products())->get($product);
		
		if(!empty($key))
			return (isset($data[$key]))? $data[$key] : false;
		
		return $data;
	}
}