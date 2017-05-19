<?php
namespace Nubersoft;

class Account extends \Nubersoft\nApp
	{
		public	function getInfo($username, $multi = false, $priority = false, $contact_type = false)
			{
				$user		=	$this->nQuery()->query('SELECT * FROM `users` WHERE `username` = :0',array($username))->getResults(true);
				
				if($user != 0) {
					unset($user['password']);
					$sql[]	=	'SELECT * FROM `user_account_details` WHERE `username` = :0';
					$bind[]		=	$username;
					
					if(!empty($priority) && is_numeric($priority)) {
						$sql[]	=	'`priority` = :'.count($sql);
						$bind[]	=	$priority;
					}
					if(!empty($contact_type)) {
						$sql[]	=	"`contact_type` = :".count($sql);
						$bind[]	=	$contact_type;
					}
					
					$sql		=	(count($sql) > 1)? implode(' AND ',$sql) : implode('',$sql);
					$account	=	$this->nQuery()->query($sql,$bind)->getResults();
					
					if(!empty($account)) {
						$account	=	$this->organizeByKey($account,'contact_type',array('mulit'=>$multi));
					}
					else {
						$account	=	array('shipping'=>false,'billing'=>'');
					}
					
					if(!isset($account['shipping']))
						$account['shipping']	=	false;
					if(!isset($account['billing']))
						$account['billing']	=	false;
					
					return array_merge(array('user'=>$user),$account);
				}
				
				return array('user'=>'','shipping'=>'','billing'=>'');
			}
		
		public	function save($array)
			{
				$nQuery		=	$this->nQuery();
				$available	=	$this->toArray($nQuery->getColumns('user_account_details'));
				foreach($array as $col => $value) {
					if(!in_array($col,$available))
						unset($array[$col]);
				}
				
				$cols		=	array_keys($array);
				
				if(in_array('ID',$cols)) {
					unset($cols[array_search('ID',$cols)]);
					
					$ID		=	$array['ID'];
					
					unset($array['ID']);
					
					$uCols	=	array_map(function($v) {
						return '`'.$v.'` = :'.$v;
					},$cols);
					
					$sql	=	'UPDATE `user_account_details` SET '.implode(', ',$uCols).' WHERE `ID` = :ID';
					
					foreach($array as $key => $value) {
						$bind[":{$key}"]	=	$value;
					}
					
					$bind[":ID"]	=	$ID;
					
					$nQuery->query($sql,$bind);
				}
				else {
					$i = 0;
					foreach($array as $key => $value) {
						$colArr[]	=	":{$i}";
						$bind[]		=	$value;
						$i++;
					}
					
					$nQuery->query("INSERT INTO `user_account_details` (`".implode("`, `",$cols)."`) VALUES (".implode(', ',$colArr).")",$bind);
				}
			}
	}