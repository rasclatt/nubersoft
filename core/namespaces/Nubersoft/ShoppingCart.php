<?php
namespace Nubersoft;

abstract class ShoppingCart extends \Nubersoft\nApp
{
	public	function __construct()
	{
	}

	public	function add()
	{
		# Get Args
		$args		=	func_get_args();
		# First should be item code
		$itemcode	=	(!empty($args[0]))? $args[0] : false;
		# Second should be qty, default to 1
		$qty		=	(!empty($args[1]) && is_numeric($args[1]))? $args[1] : 1;
		# Reset the counter
		$reset		=	false;
		if(count($args) == 3) {
			foreach($args as $type) {
				if(is_bool($type) && $type === true)
					$reset	= true;
			}
		}
		# If there is no item code, stop
		if(empty($itemcode))
			return;
		# Get the current cart
		$cart	=	$this->toArray($this->getSession('cart'));
		# No current cart set, start one
		if(empty($cart))
			$cart	=	array();
		# If the item is already in the cart, add onto it
		if(isset($cart[$itemcode]) && !$reset)
			$cart[$itemcode]['qty']	+=	$qty;
		# Start the cart 
		else
			$cart[$itemcode]['qty']	=	$qty;
		# Reset the cart
		$this->setSession('cart',$cart,true);
	}

	public	function remove($itemcode,$qty = false)
	{
		# Get cart
		$cart	=	$this->toArray($this->getSession('cart'));
		# No cart, return
		if(empty($cart))
			return;
		# Cart but no same item in cart, return
		elseif(!isset($cart[$itemcode]))
			return;
		# If there is no value to subtract from cart
		if(!is_numeric($qty))
			# Remove completely
			unset($cart[$itemcode]);
		else {
			# Do some math
			$qty	=	($cart[$itemcode]['qty'] > 0)? ($cart[$itemcode]['qty']-$qty) : 0;
			# If the quantiy is 0, remove entirely
			if($qty <= 0)
				unset($cart[$itemcode]);
			# Revise quantity
			else
				$cart[$itemcode]['qty']	=	$qty;
		}
		# Reset the cart
		$this->setSession('cart',$cart,true);
	}

	public	function clear()
	{
		# Reset the cart
		$this->setSession('cart',array(),true);
		return $this;
	}

	public	function getTotalQty()
	{
		$cart = $this->toArray($this->getSession('cart'));
		return (!empty($cart))? array_sum(array_map(function($v) { return $v['qty']; },$cart)) : 0;
	}

	public	function getTotalItems()
	{
		$cart = $this->toArray($this->getSession('cart'));
		return (!empty($cart))? count(array_keys($cart)) : 0;
	}

	public	function hasItems()
	{
		return	$this->getTotalItems() > 0;
	}

	public	function getItems($itemcode = false)
	{
		$cart = $this->toArray($this->getSession('cart'));

		if(empty($cart))
			return array();

		if(!empty($itemcode))
			return (isset($cart[$itemcode]))? $cart[$itemcode] : array();

		return $cart;
	}
	/*
	**	@description	Takes one array with map settings like array("DISTID"=>"~dist_id~")
	**					and finds the key/value pair in the $values array (in this case finds
	**					$values['dist_id']) and moves the value to a new array ($new['DISTID'])
	**	@param	$map	Contains the array mapping settings
	**	@param	$values	Contains the "from" values
	**	@param	$required	Not working, but the idea is to output an array of required fields
	**	@return	$new [array]	This will be the transposed data
	*/
	public	function mapFields(array $map, array $values)//,&$required = false)
	{
		# The array to be returned
		$new	=	array();

		foreach($map as $to => $from) {
			$new[$to]	=	preg_replace_callback('/~(.*?)~/',function($match) use ($values){
				return (isset($values[$match[1]]))? $values[$match[1]] : '';
			},$from);
		}

		//echo strip_tags(printpre($ref,array('backtrace'=>false)));
		# Return final array
		return $new;
	}

	protected	function assembleMapper($map,$values)
	{
		$ref	=	array();
		if(empty($map))
			return $ref;
		# Start loop
		foreach($map as $to => $from) {
			$ref[$to]	=	preg_replace_callback('/~([^~]{1,})~/',function($match) use ($values){
				return (isset($values[$match[1]]))? $values[$match[1]] : '';
			},$from);
		}

		return array_filter($ref);
	}
	/*
	protected	function assembleMapper($map,$values)
		{
			# Start loop
			foreach($map as $to => $from) {
				$atXp	=	explode('~',$from);
				if(count($atXp) > 1) {
					$atXp	=	array_values(array_filter($atXp));
					$atXp	=	array_filter(array_map(function($v) use ($values){
						if(strpos($v,',') !== false || empty(trim($v)))
							return false;

						return(isset($values[$v]))? array($v=>$values[$v]) : array($v=>'');
					},$atXp));

					foreach($atXp as $arr) {
						if(empty($ref[key($arr)])) {
							# Works to store for multi-similar fields
							$ref[key($arr)]	=	$arr[key($arr)];
							# Stores straight conversion fields
							$ref[$to]		=	$arr[key($arr)];
						}
					}
				}
				else  {
					if(empty($ref[$to]))
						$ref[$to]	=	$atXp[0];
				}
			}

			return (isset($ref))? array_filter($ref) : array();
		}
	*/
	protected	function getSettingsPath($append = false)
	{
		return rtrim(__DIR__.DS.'ShoppingCart'.DS.'Core'.DS.'settings'.DS.$append,DS);
	}
	/*
	**	@description	This fetches json list of country abbreviations and returns one or all
	**	@param	$abbrev	[string] Should be a 2 abbrev like "US"
	**	@return	$all | $abbrev	[array | string]	Depending on input, will return 3 abbrev "USA"
	**												or will return an array of all the mapped vals
	*/
	public	function getCountryCodes($abbrev = false,$abbr=2)
	{
		# Get file from /app/.../Infotrax/Core/settings/
		$getCodes	=	file_get_contents($this->getSettingsPath('country_data.json'));
		# Decode return to array
		$all		=	json_decode($getCodes,true);
		if(!empty($abbrev)) {
			foreach($all as $abbrRow) {
				if($abbrRow['alpha-'.$abbr] == $abbrev)
					return ($abbr == 2)? $abbrRow['alpha-3'] : $abbrRow['alpha-2'];
			}
			# If the return is specified, return 3 abbrev, if not return original
			return $abbrev;
		}
		# Return all by default
		return $all;
	}
	/*
	**	@description	Takes the abbreviation and returns the name of the country
	*/
	public	function getCountryName($abbrev = false,$count = 2)
	{
		$getCodes	=	file_get_contents($this->getSettingsPath("abbr{$count}_country_name.json"));
		$all		=	json_decode($getCodes,true);
		if(!empty($abbrev))
			return (isset($all[$abbrev]))? $all[$abbrev] : false;

		return $all;
	}
	/*
	**	@description	Takes the name and returns the 2 abbrev equivalent
	*/
	public	function getCountryAbbrFromName($abbrev,$count = 2)
	{
		$getCodes	=	file_get_contents($this->getSettingsPath("abbr{$count}_country_name.json"));
		$all		=	json_decode($getCodes,true);
		$key		=	array_search($abbrev,$all);

		if($key !== false) {
			return $key;
		}
	}
	/*
	**	@description	Takes the 2 inputs (USA,Nevada) and then returns uppercase NV
	*/
	public	function getRegion($cou,$state)
	{
		# Parse xml for states/provinces
		$getCodes	=	nApp::nRegister()->parseXmlDoc($this->getSettingsPath("states.xml"));
		# Find the appropriate array
		$getFind	=	$this->getMatchedArray(array($cou),'',$getCodes);
		# Throw exception if country not found.
		if(empty($getFind[$cou][0])) {
			//throw new \Exception('Country not found');
		}
		# If there is a listing for states
		else {
			# If that listing is an array
			if(is_array($getFind[$cou][0])) {
				# Search array for the string ("Nevada")
				$search		=	array_search($state,$getFind[$cou][0]);
				# If found
				if($search !== false) {
					# Return the uppercase version of the key
					return strtoupper($search);
				}
			}
		}
		# Just return the state input if all else fails
		return $state;
	}

	public	function getRegions($country = 'ALL',$case = true)
	{
		$Cart	=	$this;
		# Parse xml for states/provinces
		return $this->getPrefFile('cart_region_'.$country,array('save'=>true),false,function($path,$nApp) use ($Cart,$country,$case) {
			$getCodes	=	$nApp->toArray($nApp->getHelper('nRegister')->doParse($Cart->getSettingsPath("states.xml")));

			# Find the appropriate array
			$arr	=	(isset($getCodes[$country]))? $getCodes[$country] : array();

			if(empty($arr))
				return $arr;

			$new	=	array();

			if($case) {
				foreach($arr as $key => $value) {
					$new[strtoupper($key)]	=	$value;
				}
			}
			else
				$new	=	$arr;

			return $new;
		});
	}
	/*
	public	function getCountries($type=false)
		{
			$data	=	$this->getPrefFile('cart_locales',array('save'=>true),false,function($path,$nApp) {
				$co	=	$nApp->nQuery()
					->query("SELECT
								`content`
							FROM
								`system_settings`
							WHERE
								`name` = 'country'
									AND
								`component` = 'locales'")
					->getResults(true);

				$countries	=	json_decode($nApp->safe()->decode($co['content']),true);

				return array(
					'ABBR'=>array_values($countries),
					'NAMES'=>array_keys($countries),
					'OPTIONS'=>array_combine(array_values($countries),array_keys($countries))
				);
			});

			if(empty($type))
				return $data;

			$type	=	strtoupper($type);

			return (!empty($data[$type]))? $data[$type] : array();
		}
	*/
	public	function getCountries($type=false)
	{
		$data	=	$this->getPrefFile('cart_locales',array('save'=>true),false,function($path,$nApp) {
			$data	=	$nApp->nQuery()
				->query("SELECT
						`menuVal` as value,
						`menuName` as name
					FROM
						dropdown_menus
					WHERE
						menuVal
					IN
						(
						SELECT
							DISTINCT `locale_abbr`
						FROM
							`cart_products_locales`
						)
					AND 
						`assoc_column` = 'locale_abbr'
					ORDER BY
						`menuName` ASC")
				->getResults();

			return array(
				'ABBR'=>array_keys($nApp->organizeByKey($data,'value')),
				'NAMES'=>array_keys($nApp->organizeByKey($data,'name')),
				'OPTIONS'=>$data
			);
		});

		if(empty($type))
			return $data;

		$type	=	strtoupper($type);

		return (!empty($data[$type]))? $data[$type] : array();
	}

	public	function saveOrderTransaction($data)
	{
		if(isset($data['other'])) {
			$data['content']	=	(is_array($data['other']))? json_encode($data['other']) : $data['other'];
			unset($data['other']);
		}
		elseif(isset($data['content'])) {
			if(is_array($data['content']))
				$data['content']	=	json_encode($data['content']);
		}

		foreach($data as $key => $value) {
			if($key == 'cc')
				$value	=	substr($value,-4,4);

			$enc			=	$this->safe()->encode($key);
			$cols[]			=	$enc;
			$bKey			=	":{$enc}";
			$bind[$bKey]	=	$value;
		}
		try {
			$PDO	=	$this->nQuery()->getConnection();
			$query	=	$PDO->prepare("INSERT INTO `order_transactions` (`unique_id`,`".implode("`,`",$cols)."`) VALUES ('".$this->fetchUniqueId()."',".implode(', ',array_keys($bind)).")");
			$query->execute($bind);
		}catch(\PDOException $e) {
			if($this->isAdmin())
				die(printpre([$data,$e->getMessage()]));
		}

		return $this;
	}
	/*
	**	@description	Returns list or currency country from database
	*/
	public	function getCountryCurrency($country = false)
	{
		$sql	=	"SELECT `ID`,`country`,`currency` from `cart_currency_locales` WHERE";
		if(!empty($country))
			$sql	.=	" `country` = :0 AND";

		$sql	.=	" `page_live` = 'on' ORDER BY `country` ASC";
		$nQuery	=	$this->nQuery();
		$query	=	(!empty($country))? $nQuery->query($sql,array($country)) : $nQuery->query($sql);

		return (!empty($country))? $nQuery->getResults(true) : $nQuery->getResults();

	}
	/*
	**	@description	Fetches and key/values Country abbrev vs country currency
	*/
	public	function getCurrencyList($country = false)
	{
		$list	=	$this->getCountryCurrency();
		foreach($list as $row)
			$new[$row['country']]	=	$row['currency'];

		return (!empty($new))? $new : array('USA'=>'USD');
	}

	public	function getCountryFromCurrency($country)
	{
		$sql	=	"SELECT
						`ID`,
						`country`,
						`currency`
					FROM
						`cart_currency_locales`
					WHERE
						`currency` = :0
					AND
						`page_live` = 'on'";
		return $this->nQuery()->query($sql,array($country))->getResults(true);
	}

	public	function getCurrency($cou)
	{
		$curr	=	$this->getCountryCurrency($cou);
		return (!empty($curr['currency']))? $curr['currency'] : 'USD';
	}

	public	function getExchange($get = false,$from = 'USD')
	{
		$Currency	=	$this->getHelper('Currency');
		$Currency->setBaseCurrency($from)->fetch();
		return $Currency->getRates($get);
	}

	public	function convertPrice($array)
	{
		$Currency	=	$this->getHelper('Currency');
		return $Currency->convert($array);
	}
	/*
	**	@description	Fetches the country to currency array from xml file
	*/
	public	function getCurrencyConverter($country = false,$root=false,$prefName = false)
	{
		$thisObj	=	$this;
		if(!empty($root) && empty($prefName))
			$prefName	=	'currency_codes_'.preg_replace('/[^a-zA-Z0-9\_]/','',dirname($root));

		if(empty($prefName))
			$prefName	=	'currency_codes';

		$root		=	(!empty($root))? $root : __DIR__.DS.'ShoppingCart'.DS.'Core'.DS.'settings';

		# Fetches pref file array
		$array	=	$this->getPrefFile($prefName.date('Ymd'),array('save'=>false),false,function($path,$nApp) use ($root,$thisObj){
			# Path to file
			$filename	=	$root.DS.'currency_codes.xml';
			# Fetch the xml file
			$get		=	$nApp->getHelper('nRegister')->parseRegFile($filename);
			# Extract array
			$couArr		=	$nApp->getMatchedArray(array('country'),$get);
			# Get countries
			$abbrevs	=	(!empty($couArr['country'][0][0]))? $couArr['country'][0] : array();
			# Isolate the create array
			$new		=	array();
			# Stop if empty
			if(!is_array($abbrevs) && !is_object($abbrevs))
				return $new;
			# Set as array
			if(is_object($abbrevs))
				$abbrevs	=	$nApp->toArray($abbrevs);
			# Loop country codes
			foreach($abbrevs as $cou) {
				$new[$cou['abbr']]	=	$cou['currency'];
			}
			# Send back all codes
			return $new;
		});

		if(empty($country))
			return $array;

		$country	=	strtoupper($country);

		foreach($array as $abbr => $code) {
			if($abbr == $country)
				return $code;
		}

		return 'USD';
	}
	/*
	**	@description	Basic rerouting method, used in tandem with an action like clear()
	*/
	public	function reRouteCurrent($msg=false)
	{
		$msg	=	(empty($msg))? $this->getRequest('action') : $msg;
		$this->setSession('cart_messages',$msg);
		$this->getHelper('nRouter')->addRedirect($this->localeUrl($this->getPageURI('full_path')));
	}
}
