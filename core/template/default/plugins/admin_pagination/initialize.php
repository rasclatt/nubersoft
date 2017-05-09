<?php
//********************************************************//
//************ Pagination Settings ***********************//
$settings['table'] 		=	(!empty($this->getGet('requestTable')))? $this->getGet('requestTable') : $this->getTableName();
$settings['spread'] 	=	2;
$settings['admin'] 		=	true;
$settings['max_range']	=	array(1,5,10,50,100);
$settings['layout']		=	__DIR__.DS."results.php";
$settings['submit']		=	"SEARCH";
$settings['sort']		=	"DESC";
$this->saveSetting('pagination_settings',$settings);
# Initialize Pagination
pagination_initialize($this,$settings);