<?php
namespace nPlugins\Nubersoft;

class Permission extends \Nubersoft\nApp
	{
		public	function onWhiteList($ip,$type = 'admintools')
			{
				# Get the whitelist
				$list	=	$this->getWhiteList($type);
				# If not there
				if(!is_array($list) || empty($list)) {
					# Just warn there is no listing
					$this->saveIncidental('whitelist_'.$type, array('whitelist_'.$type.'_warning'=>'no white list available for '.$type));
					# Return user allowed
					return true;
				}
				# If the value is returned but has a mixture of arrays and values
				if(isset($list[0]) && is_array($list[0])) {
					$new	=	array();
					# Loop through the list
					foreach($list as $ipSet) {
						# Filter values from arrays
						if(is_array($ipSet))
							$new	=	array_merge($ipSet,$new);
						else
							$new[]	=	$ipSet;
					}
					# Save to list value
					$list	=	$new;
				}
				
				return (in_array($ip,$list));
			}
		
		public	function getBannedStatus($username,$ip)
			{
				$ipBanSql	=	"SELECT COUNT(*) as count from `api_mxi_banned_ips` where `dist_id` =:0 OR `username`= :1";
				$count		=	$this->nQuery()->query($ipBanSql,array($username,$username))->getResults(true);
				$userCnt	=	$count['count'];
				
				$ipBanSql	=	"SELECT COUNT(*) as count from `api_mxi_banned_ips` where `ip_address` = :0";
				$count		=	$this->nQuery()->query($ipBanSql,array($ip))->getResults(true);
				
				$ipCnt		=	$count['count'];
				
				return array(
					'ip'=>$ipCnt,
					'user'=>$userCnt
				);
			}
		/*
		**	@description	Searches config for a whitelist
		**	@use			<whitelist>
		**						<admintools>
		**							<ip>12.123.12.123</ip>
		**						</admintools>
		**					</whitelist>
		*/
		public	function getWhiteList($type)
			{
				if(!is_string($type))
					return false;
				
				$searchArr	=	array('whitelist',$type,'ip');
				$sName		=	'nbr_'.implode('_',$searchArr);
				$whitelist	=	$this->getConfigSetting($searchArr);
				$dbList		=	$this->getPrefFile($sName,array('save'=>true),false,function($path,$nApp) use ($sName){
					$ipList	=	$nApp->nQuery()->query("select `content` from `components` where `ref_spot` = :0 and `page_live` = 'on'",array($sName))->getResults();
					if($ipList != 0)
						return array_keys($nApp->organizeByKey($ipList,'content',array('multi'=>true)));
					
					return array();
				});
				
				$ips	=	(!empty($whitelist['ip']))? $whitelist['ip'] : false;
				
				if(!empty($ips)) {
					if(!empty($dbList))
						$ips	=	array_merge($dbList,$ips);
				}
				else
					$ips	=	(!empty($dbList))? $dbList : false;
				
				return (is_array($ips))? $this->getRecursiveValues($ips) : false;
			}
	}