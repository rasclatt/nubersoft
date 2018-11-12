<?php
function getHost()
{
	return (!empty($_SERVER['DOMAIN_NAME_REAL']))? $_SERVER['DOMAIN_NAME_REAL'] : preg_replace('/^www\./', '', $_SERVER['SERVER_NAME']);
}