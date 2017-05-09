<?php
namespace Nubersoft;

class nView extends \Nubersoft\nApp
	{
		/*
		**	@description	Extracts from configs array any styles that require dynamic adding
		*/
		public	function getStyles()
			{
				$inst	=	array();
				$sConf	=	$this->getMatchedArray(array('stylesheet'));
				// No stylesheets, just return
				if(empty($sConf['stylesheet']))
					return false;
				$sList	=	array();
				$this->fetchScripts($sConf['stylesheet'],$sList);
				$pId	=	$this->getPage('unique_id');
				$pPath	=	$this->getPage('full_path');
				$pAdmin	=	$this->getPage('is_admin');
				if(empty($sList))
					return false;
				
				$this->autoload('version_from_file',NBR_FUNCTIONS);
				$includes	=	array();
				foreach($sList as $sInstance) {
					if(!isset($sInstance['include']))
						continue;
		
					$rLink		=	$this->getHelper('nAutomator',$this)->matchFunction($sInstance['include']);
					$link		=	site_url().str_replace(DS.DS,DS,DS.str_replace(NBR_ROOT_DIR.DS,'',$rLink));
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