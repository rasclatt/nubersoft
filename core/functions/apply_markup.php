<?php
/*Title: apply_markup()*/
/*Description: Used in conjunction with the `use_markup()`  It is used in context with a `preg_replace_callback()` */
use Nubersoft\nApp as nApp;

function apply_markup($match)
	{
		return nApp::call()->getHelper('nMarkUp')->automate($match);
	}