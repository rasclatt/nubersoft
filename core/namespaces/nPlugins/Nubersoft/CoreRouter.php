<?php
namespace nPlugins\Nubersoft;

class CoreRouter extends \nPlugins\Nubersoft\CoreDatabase
	{
		protected	static	$rUrl,
							$ignoreSkip;
		
		public	function saveComponent($POST = false, $skip = false)
			{
				# Get post values
				$POST	=	(!empty($POST))? $POST : $this->toArray($this->getPost());
				#
				$ID		=	$POST['ID'];
				# See if there is an option to remove empty
				$filter	=	(!empty($POST['action_options']['filter']));
				# See if a thumbnail is required to be created
				$thumb	=	(!empty($POST['action_options']['thumb']));
				# See if there is a token
				$match	=	$this->getHelper('nToken')->getSetToken();
				# If not admin stop
				if(!$this->isAdmin()) {
					$this->saveError($POST['action'],'You must be an admin user.',true);
					return;
				}
				/*
				# If token doesn't match, stop
				if($match != $POST['token']['nProcessor']) {
					$this->saveError($POST['action'],'Invalid token',true);
					return;
				}
				*/
				# Filter columns/values
				$POST	=	$this->filterAvailableColumns($POST);
				# If empty set, remove empty fields
				if($filter) {
					$POST	=	array_filter($POST);
				}
				if(isset($POST['link']))
					$dirname	=	preg_replace('/[^0-9a-zA-Z\_\-]/','',trim($POST['link']));
	
				if(isset($POST['menu_name']))
					$bDirname	=	preg_replace('/[^0-9a-zA-Z\_\-]/','',trim($POST['menu_name']));
				else
					$bDirname	=	false;
				if(empty($dirname)) {
					if(empty($bDirname)) {
						$saveName	=	(isset($POST['action']))? $POST['action'] : 'CoreRouter';
						$this->saveError($saveName,array('msg'=>'Name can not be empty'));
						return;
					}
					else
						$dirname	=	$bDirname;
				}
				
				$POST['link']	=	$dirname;
				$path			=	array($dirname);
				$parent			=	$POST['parent_id'];
				$this->determineNested($parent,$path);
				$jumppage		=	'/'.implode('/',array_reverse($path)).'/';
				//$this->createUpdate($POST);
				//echo printpre($this->getPost());
				//die();
				if(!empty($ID)) {
					$this->updateComponent($POST,$ID);
				}
				else {
					$this->addNewMenu($POST);
				}
				
				$this->reIndexPages();
				$this->redirectTo($this->siteUrl().$jumppage);
			}
		
		public	function reIndexPages()
			{
				$reIndexArr	=	$this->nQuery()->query("select `unique_id`, `parent_id`, `link` from `main_menus`")->getResults();				
				if($reIndexArr == 0)
					return;
				
				foreach($reIndexArr as $menu) {
					$array	=	array($menu['link']);
					$this->determineNested($menu['parent_id'],$array);
					$path	=	'/'.implode('/',array_reverse($array)).'/';
					$this->nQuery()->query("update `main_menus` set `full_path` = '{$path}' where `unique_id` = '".$menu['unique_id']."'");
				}
			}
		
		public	function determineNested($parent,&$array)
			{
				if(!empty($parent)) {
					$getParent	=	$this->nQuery()->query("select `link`, `unique_id`, `parent_id` from `main_menus` where `unique_id` = '{$parent}'")->getResults(true);
					if(!empty($getParent['unique_id'])) {
						$parentVal	=	$getParent['parent_id'];
						$array[]	=	$getParent['link'];
						$this->determineNested($parentVal,$array);
					}
				}
			}
		
		public	function updateMenu($ID = false,$data = false)
			{
				if(!$this->isAdmin())
					return;
				if(empty($ID)) {
					$POST	=	$this->toArray($this->getPost());
					$match	=	$this->getHelper('nToken')->getSetToken();
					$ID		=	$POST['ID'];
				}
				else {
					$POST	=	$data;
					$match	=	true;
				}
				
				if(!$match)
					return;
				
				$this->setTable('main_menus')
					->saveComponent();
			}
		
		public	function deleteMenu($ID = false)
			{
				if(!$this->isAdmin())
					return;
				
				if(!$this->getHelper('nToken')->getSetToken())
					return;
				
				$ID		=	(!empty($ID))? $ID : $this->getPost('ID');
				$page	=	$this->query("select `unique_id` from `main_menus` where `ID` = :0",array($ID))->getResults(true);
				$test	=	$this->query("delete from `main_menus` where `ID` = :0",array($ID));
				$this->query("update `main_menus` set `parent_id` = '' where `parent_id` = :0",array($page['unique_id']));
				$this->reIndexPages();
				$this->redirectTo($this->siteUrl());
				
			}
		
		private	function redirectTo($jumppage)
			{
				$this->getHelper('nRouter')->addRedirect($jumppage);
			}
			
		public	function addNewMenu($POST = false)
			{
				try {
					$POST['unique_id']	=	$this->fetchUniqueId();
					$POST				=	array_filter($POST);
					$sql				=	$this->createInsert($POST);
					$query				=	$this->getConnection()->prepare("insert into `".$this->getTable()."` {$sql}");
					$query->execute($this->standardBind($POST));
				}
				catch(\PDOException $e) {
					die($e->getMessage());
				}
			}
		/*
		**	@description	Adds a redirect url to pass to the redirectOnWhitelist() method.
		**	@param	$url	[string]	Standard redirect url
		*/
		public	function addUrl($url)
			{
				self::$rUrl	=	$url;
				return $this;
			}
		/*
		**	@description	Sets an ignore if the url is tagged in the siteUrl()
		*/
		public	function ignoreOnUrl($str)
			{
				if(strpos($this->siteUrl(),$str) !== false)
					self::$ignoreSkip	=	true;
			}
		/*
		**	@description	Checks user IP and redirects if I not in list of accepted IPs
		**	@param	$array	[string|array]	List of ips to check against
		*/
		public	function redirectOnWhitelist($array)
			{
				if(empty($array))
					return;
				elseif(!empty(self::$ignoreSkip))
					return;
				
				$ip		=	$this->getClientIp();
				$array	=	(is_string($array))? array($array) : $array;
				
				if(!in_array($ip,$array)) {
					if($this->isAjaxRequest())
						$this->ajaxResponse(array('alert'=>'Content is restricted.'));
					else
						$this->getHelper('nRouter')->addRedirect(self::$rUrl);
				}
			}
		
		public	function redirectOnBan($args)
			{
				if(!empty($_COOKIE['banned_user'])) {
					if($this->isAdmin())
						setcookie('banned_user',false,-3000);
				}
				
				$list_table	=	$this->safe()->encode($args[0]);
				$ip_table	=	$this->safe()->encode($args[1]);
				$ip			=	$this->getClientIp();
				$marker		=	(!empty($args[2]))? $this->getSession($args[2]) : false;
				
				if(!empty($marker)) {
					$lists	=	$this->nQuery()->query("select * from `{$list_table}` where `{$args[2]}` = :0",array($marker))->getResults();
				}
				
				$ips	=	$this->nQuery()->query("select * from `{$ip_table}` where `ip_address` = :0",array($ip))->getResults();
				if(!empty($lists) || !empty($ips)) {
					setcookie('banned_user',$this->safe()->encOpenSsl(true),((time()+(86400 * 30))*30));
					$this->getHelper('nSessioner')->destroy();
					$msg	=	array(
						'title'=>'Banned User',
						'body'=>'Your credentials have been banned'
					);

					$this->saveSetting('error404',$msg,true);
					$this->saveError('error404',$msg,true);
					$this->saveIncidental('error404',$msg,true);

					$layout	=	$this->getHelper('nRender')->error404Page();
					
					if($this->isAjaxRequest())
						$this->ajaxResponse(array('alert'=>$ip,'html'=>array($layout),'sendto'=>array('html')));
					
					echo $layout;
					exit;
				}
			}
	}