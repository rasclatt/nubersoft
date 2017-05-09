
var	$_SERVER	=	{
		'X_FULL_HOST': window.location.host,
		'SERVER_NAME': window.location.hostname,
		'DOMAIN_NAME_REAL': window.location.hostname,
		'HTTP_HOST': window.location.hostname,
		'HTTP_X_FORWARDED_PROTO': (window.location.protocol).replace(':',''),
		'SERVER_PORT': window.location.port,
		'SCRIPT_URI': window.location.origin,
		'HTTP_USER_AGENT': navigator.userAgent,
		'GEO': navigator.geolocation
	}
	
function base64_encode(val)
	{
		return	window.btoa(val);
	}

function base64_decode(val)
	{
		return	window.atob(val);
	}
	
function empty(value)
	{
		if(typeof(value) === "object") {
			try {
				if(value instanceof jQuery) {
					getjQcount = function(value) {
						var L=0;
						jQuery.each(value, function(i, elem) {
							L++;
						});
						return L;
					}
					
					return (getjQcount(value) == 0);
				}
				else {
					var count = 0;
					var i;
					for (i in value) {
						if (value.hasOwnProperty(i)) {
							count++;
						}
					}
				}
				return (count == 0);
			}
			catch(Exception) {
				console.log(Exception.message);
			}
		}
		else {
			return (value == '' || value == null || value == false || (typeof value === "undefined") || (Array.isArray(value) && value.length == 0));
		}
	}

function is_object(val)
	{
		return (typeof val === "object");
	}

function is_array(val)
	{
		return Array.isArray(val);
	}

function in_array(array,value)
	{
		if(!is_array(array))
			return false;
			
		return (array.indexOf(value) != -1)
	}

function array_filter(array)
	{
		if(!is_array(array))
			return array;
		
		var	newArr		=	[];
		var getCount	=	count(array);
		for(var i = 0; i < getCount; i++) {
			if(!empty(array[i]))
				newArr	=	array_push(newArr,array[i]);
		}
		
		return newArr;
	}

function isset(obj,key)
	{
		if(empty(obj))
			return false;
			
		if(!is_object(obj))
			return false;
		
		return (typeof obj[key] !== "undefined");
	}

function array_unique(array)
	{
		if(typeof array === "undefined")
			return array;
		else if(!is_array(array))
			return array;
			
		var	newArr		=	[];
		var getCount	=	count(array);
		for(var i=0; i < getCount; i++) {
			if(in_array(newArr,array[i]))
				newArr	=	array_push(newArr,array[i]);
		}
		
		return newArr;
	}

function count(array)
	{
		if(!is_array(array))
			return 0;
		
		return array.length;
	}

function is_numeric(val)
	{
		if(empty(val))
			return false;
		
		return (!isNaN(parseFloat(val)) && isFinite(val));
	}

function asort(array,SORT_NAT)
	{
		if(!empty(SORT_NAT))
			return array.sort(function(a, b){return a - b});
		else
			return array.sort();
	}

function arsort(array,SORT_NAT)
	{
		array	=	(!empty(SORT_NAT))? asort(array,SORT_NAT) : asort(array);
		return array.reverse();
	}

function array_chunk(array,num)
	{
		if(!is_numeric(num)) {
			if(num === 0)
				throw new Exception('Must be a number greater than zero','0004');
			else
				throw new Exception('Must be a number','0003');
	
			return array;
		}
		else if(empty(num)) {
			throw new Exception('Number can not be empty','0001');
			return array;
		}
		var	newArr		=	[];
		var getCount	=	count(array);
		var	a			=	0;
		for(var i = 0; i < getCount; i++) {
			if(isset(newArr,a)) {
				if(count(newArr[a]) == num) {
					a++;
					newArr[a]	=	[];
					newArr[a].push(array[i]);
				}
				else
					newArr[a].push(array[i]);		
			}
			else {
				newArr[a]	=	[];
				newArr[a].push(array[i]);
			}
		}
		
		return newArr;
	}
/*
**	@description	Mimicks the explode feature in PHP
*/
function explode(delimiter,string)
	{
		return string.split(delimiter);
	}
	
function array_push(array,value)
	{
		array.push(value);
		
		return array;
	}
	
function get_dom_type(value)
	{
		if(value instanceof jQuery)
			return (isset(value,0) && !empty(value[0].nodeName))? value[0].nodeName : false;
		
		return value.nodeName;
	}

function preg_match(regex,value)
	{
		if(!is_string(value) || !is_string(regex))
			return value;
		
		var expression	=	new RegExp(regex, "gm");
		var myArray		=	expression.exec(value);
		
		return myArray;
	}

function preg_replace(regex,repVal,value)
	{
		return value.replace(regex,repVal);
	}
	
function is_bool(value)
	{
		return (typeof value === 'boolean');
	}

function is_string(value)
	{
		if(empty(value))
			return false;
		else if(is_object(value))
			return false;
		else if(is_array(value))
			return false;
		else if(is_bool(value))
			return false;
		
		return true;
	}

function echo(value,line)
	{
		line	=	(empty(line))? "" : " ("+line+")";
		if(!is_object(value))
			console.log(value+line);
		else
			console.log(value);
	}
	
function trim(value)
	{
		return (is_string(value))? value.trim() : value;
	}