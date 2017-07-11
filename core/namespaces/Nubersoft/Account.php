<?php
/*
**	Copyright (c) 2017 Nubersoft.com
**	Permission is hereby granted, free of charge *(see acception below in reference to
**	base CMS software)*, to any person obtaining a copy of this software (nUberSoft Framework)
**	and associated documentation files (the "Software"), to deal in the Software without
**	restriction, including without limitation the rights to use, copy, modify, merge, publish,
**	or distribute copies of the Software, and to permit persons to whom the Software is
**	furnished to do so, subject to the following conditions:
**	
**	The base CMS software* is not used for commercial sales except with expressed permission.
**	A licensing fee or waiver is required to run software in a commercial setting using
**	the base CMS software.
**	
**	*Base CMS software is defined as running the default software package as found in this
**	repository in the index.php page. This includes use of any of the nAutomator with the
**	default/modified/exended xml versions workflow/blockflows/actions.
**	
**	The above copyright notice and this permission notice shall be included in all
**	copies or substantial portions of the Software.
**
**	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
**	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
**	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
**	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
**	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
**	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
**	SOFTWARE.

**SNIPPETS:**
**	ANY SNIPPETS BORROWED SHOULD BE SITED IN THE PAGE IT IS USED. THERE MAY BE SOME
**	THIRD-PARTY PHP OR JS STILL PRESENT, HOWEVER IT WILL NOT BE IN USE. IT JUST HAS
**	NOT BEEN LOCATED AND DELETED.
*/
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