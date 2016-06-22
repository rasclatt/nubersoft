<?php
/*
** @param $arr	[array]	Add in an array and get a javascript object back
** @param $json	[bool]	true just returns a json string.
*/
function javascript_array_to_obj($arr = false,$json = false)
	{
		return nApp::jsEngine()->makeObject($arr,$json);
	}