<?php
namespace Nubersoft;

class nView
	{
		private	static	$singleton;
		
		public	function __construct()
			{
				if(!(self::$singleton instanceof nView)) {
					self::$singleton	=	$this;
				}
				
				return self::$singleton;
			}
		/*
		**	@description	Extracts from configs array any styles that require dynamic adding
		*/
		public	function getStyles()
			{
				$inst	=	array();
				$nFunc	=	\nApp::nFunc();
				$sConf	=	$nFunc->getMatchedArray(array('stylesheet'));
				$sList	=	array();
				$nFunc->fetchScripts($sConf['stylesheet'],$sList);
				$pId	=	\nApp::getPage('unique_id');
				$pPath	=	\nApp::getPage('full_path');
				$pAdmin	=	\nApp::getPage('is_admin');
				
				if(empty($sList))
					return false;
				
				$nFunc->autoload('version_from_file',NBR_FUNCTIONS);
				$includes	=	array();
				foreach($sList as $sInstance) {
					if(!isset($sInstance['include']))
						continue;
		
					$rLink		=	\nApp::nAutomator()->matchFunction($sInstance['include']);
					$link		=	site_url().str_replace(_DS_._DS_,_DS_,_DS_.str_replace(NBR_ROOT_DIR._DS_,'',$rLink));
		
					if(!is_file($rLink))
						continue;
						
					$html	=	'<link type="text/css" rel="stylesheet" href="'.$link.version_from_file($rLink).'" />';
					
					if($sInstance['loadid'] != 'na') {
						if($pId == $sInstance['loadid'])
							$includes[]	=	$html;
					}
					elseif($sInstance['loadpage'] != 'na') {
						if($pPath == $sInstance['loadpage'])
							$includes[]	=	$html;
					}
					elseif($sInstance['is_admin'] != false) {
						if($pAdmin == $sInstance['is_admin'])
							$includes[]	=	$html;
					}
					else
						$includes[]	=	$html;
				}
				
				return implode(PHP_EOL,$includes).PHP_EOL;
			}
	}