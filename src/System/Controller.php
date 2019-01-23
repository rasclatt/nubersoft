<?php
namespace Nubersoft\System;
/**
 *	@description	
 */
class Controller extends \Nubersoft\System
{
	/**
	 *	@description	
	 */
	public	function getThumbnail($filepath, $filename)
	{
		if(empty($filepath) || empty($filename))
			return false;
		$thumb		=	str_replace(DS.DS, DS, NBR_ROOT_DIR.$filepath.DS.'thumbs'.DS.$filename); 
		
		if(is_file($thumb)) {
			return $this->localeUrl(str_replace(NBR_ROOT_DIR,'',$thumb));
		}
		elseif(is_file($imgFile = NBR_ROOT_DIR.$filepath.DS.$filename)) {
			
			$get_memory	=	ini_get('memory_limit');
			$get_mb		=	(stripos(strtolower($get_memory), 'm') !== false);
			$maxsize	=	((float) preg_replace('/[^\d]/', '', $get_memory) * 1000000)*.25;
		
			if(in_array(strtolower(pathinfo($imgFile, PATHINFO_EXTENSION)), ['jpg','jpeg'])) {
				
				if((filesize($imgFile)*13) > $maxsize) {
					return $this->localeUrl(str_replace(DS.DS, DS, $filepath.'/'.$filename));
				}
			}
			$this->isDir(pathinfo($thumb, PATHINFO_DIRNAME), true);
			$Image		=	$this->getHelper('ImageFactory');
			$scale		=	$Image->setFileSize($maxsize)->autoScale($imgFile, 300);

			$Image->makeThumbnail(str_replace(DS.DS, DS, $imgFile), $scale['width'], $scale['height'], str_replace(DS.DS, DS, $thumb), 50, false);
			
			$imgpath	=	(is_file($thumb))? str_replace(NBR_ROOT_DIR,'',$thumb): str_replace(DS.DS, DS, $filepath.'/'.$filename);
			return $this->localeUrl($imgpath);
		}
		
		return $this->localeUrl(str_replace(DS.DS, DS, $filepath.'/'.$filename));
	}
}