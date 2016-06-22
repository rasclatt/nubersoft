<?php
	function serialbox($array = false,$key = false)
		{
			if(empty($array[$key]))
				return array();
				
			if(@!$unserialized = unserialize(Safe::decode($array[$key])))
				throw new Exception("<!--Serialization failed-->");
			
			return (!is_array($unserialized))? array():$unserialized;
		}
?>