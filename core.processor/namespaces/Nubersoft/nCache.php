<?php
namespace Nubersoft;

class	nCache
	{
		protected	static	$pageData;
		
		public	static function app()
			{
				return new \BuildCache();
			}
		
		public	static	function startCache()
			{
				// echo printpre(NubeData::$settings);
				$loggedin							=	(isset($_SESSION['username']));
				self::$pageData['id']				=	\nApp::getPage('ID');
				self::$pageData['cache']			=	\nApp::getDataNode('site')->cache_folder;
				self::$pageData['session_status']	=	(\nApp::getPage('session_status') == 'on' && $loggedin)? 1:2;
				self::$pageData['username']			=	($loggedin)? md5($_SESSION['username']) : md5('fpc');
				self::$pageData['usergroup']		=	($loggedin)? md5($_SESSION['usergroup']) : md5('fpcug');
				self::$pageData['dir']				=	str_replace(_DS_._DS_,_DS_,self::$pageData['cache']._DS_.self::$pageData['id']._DS_.self::$pageData['session_status']._DS_.self::$pageData['usergroup']._DS_.self::$pageData['username']._DS_);
				self::$pageData['inc']				=	self::$pageData['dir'].'index.php';
				if(is_file(self::$pageData['inc'])) {
					if(!is_admin()) {
						echo \nApp::nFunc()->render(self::$pageData['inc']);
						exit;
					}
				}
				else {
					if(!is_admin())
						ob_start();
				}
			}
		
		public	static	function endCache()
			{
				if(!is_admin()) {
					$data	=	ob_get_contents();
					ob_end_clean();
					
					echo $data;
					if(\nApp::nFunc()->isDir(self::$pageData['dir'],true,0777))
						file_put_contents(self::$pageData['inc'],$data);
				}
			}
	}