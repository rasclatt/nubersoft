<?php
namespace nWordpress;

class Token extends \Nubersoft\nApp
{
	public	function create($action)
	{
		return wp_create_nonce($action);
	}
	
	public	function verify($nonce,$action)
	{
		return wp_verify_nonce($nonce,$action);
	}
}