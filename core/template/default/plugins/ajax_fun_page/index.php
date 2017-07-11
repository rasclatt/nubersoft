<?php
$Menus	=	$this->getMenus();

$arr	= array(
	'test'=>array(
		'@attributes'=>array(
			'boring'=>'best',
			'is_admin'=>true
		),
		'goosh'=>'bugs',
		'tester'=>array(
			'goober'=>array(
				'@attributes'=>array(
					'value'=>'none',
					'madd'=>'tres'	
					)
				)
			)
		),
	'boogers'=>'test'
	);

$goosh	=	(new \Nubersoft\Methodize())->saveAttr('test',$arr);

echo $goosh->getTest()->toXml('config',true);