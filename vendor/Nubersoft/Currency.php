<?php
namespace Nubersoft;

use \Nubersoft\nApp;

class Currency extends \Nubersoft\cURL
{
	protected	$endpoint,
				$rates,
				$currency,
				$queryString,
				$baseCurrency;

	const	DEFAULT_API		=	'http://api.fixer.io/latest';
	const	BASE_CURRENCY	=	'USD';

	public	function setBaseCurrency($currency)
	{
		$this->baseCurrency	=	$currency;
		return $this;
	}

	public	function getBaseCurrency()
	{
		return (!empty($this->baseCurrency))? $this->baseCurrency : self::BASE_CURRENCY;
	}

	public	function setAttributes($attr)
	{
		$this->queryString	=	http_build_query($attr);
		return $this;
	}

	public	function fetch()
	{
		if(empty($this->endpoint)) {
			$this->endpoint	=	self::DEFAULT_API;
			$this->setAttributes(array('base'=>$this->getBaseCurrency()));
		}

		$this->query($this->endpoint.((!empty($this->queryString))? '?'.$this->queryString : ''));

		$this->queryString	=	false;
		return $this;
	}

	public	function getRates($get = false)
	{
		$response	=	$this->getResponse(true);
		if(!empty($get)) {
			if($get == $this->getBaseCurrency())
				return 1;

			return (!empty($response['rates'][$get]))? $response['rates'][$get] : false;
		}

		return (isset($response['rates']))? $response['rates'] : array();
	}

	public	function convert($array)
	{
		$this->setBaseCurrency($array['from'])->fetch();

		$to				=	$array['to'];
		$rate			=	$this->getRates($to);
		$array['value']	=	preg_replace('/[^\d\.]/','',$array['value']);
		return $array['value']*$rate;
	}

	public	function getLocale($country)
	{
		$locales	=	$this->getLocaleList();
		return (isset($locales[$country]['lang']))? $locales[$country]['lang'] : 'en_US';
	}

	public	function getLocaleList($key = 'abbr3')
	{
		$path	=	__DIR__.DS.'Currency'.DS.'Core'.DS.'settings'.DS.'locale_list.xml';
		$reg	=	nApp::call()->toArray(simplexml_load_file($path));
		return ArrayWorks::organizeByKey($reg['locale'], $key, ['unset' => false]);
	}

	public	function getFormatList()
	{
		$nApp	=	\Nubersoft\nApp::call();

		return $nApp->getPrefFile('money_format',array('save'=>true),false,function($path,$nApp) {
			$config	=	$nApp->getHelper('nRegister')->parseXmlFile(__DIR__.DS.'Currency'.DS.'Core'.DS.'settings'.DS.'money_format.xml');
			$array	=	$nApp->organizeByKey($config['money_format'],'abbr',array('unset'=>false));
			ksort($array);
			return $array;
		});

	}

	public	function getMoneyFormat($country)
	{
		$format	=	$this->getFormatList();

		$array	=	(!empty($format[$country]))? $format[$country] : array('abbr'=>'USD','format'=>'#,###.##','dec'=>'2');

		$chars	=	explode('#',$array['format']);
		$filter	=	array(' ','.',',');
		foreach($chars as $i => $value) {
			if(!in_array($value,$filter))
				unset($chars[$i]); 
			else {
				if($value == ' ')
					$chars[$i]	=	'&nbsp;';
			}
		}

		$chars	=	(!empty($chars))? array_values($chars) : false;
		$array['format']	=	array(
			'sep_num'=>(isset($chars[0]))? $chars[0] : '',
			'sep_dec'=>(isset($chars[1]))? $chars[1] : ''
		);


		return $array;
	}

	public	function toMoney($number,$country,$append='$',$toArray=false)
	{
		$format	=	$this->getMoneyFormat($country);
		$value	=	number_format($number,$format['dec'],$format['format']['sep_dec'],$format['format']['sep_num']);
		if(!empty($append)) {
			if($toArray) 
				return (is_bool($append))? array('symbol'=>$format['abbr'],'value'=>$value) : array('symbol'=>$append,'value'=>$value);

			return	(is_bool($append))? '('.$format['abbr'].') '.$value : $append.$value;
		}

		return $value;
	}

	public	function toDollar($number,$country = 'USA',$format = '%i',$append='UTF-8')
	{
		$locales	=	$this->getLocaleList();
		$encode		=	(isset($locales[$country]['lang']))? $locales[$country]['lang'] : 'en_US';
		if(!empty($append))
			$encode	.=	'.'.$append;

		echo $encode;

		setlocale(LC_MONETARY, $encode);
		return money_format($format, $number);
	}
}