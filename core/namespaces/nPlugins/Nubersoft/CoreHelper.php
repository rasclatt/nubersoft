<?php
namespace nPlugins\Nubersoft;

class CoreHelper extends \Nubersoft\nRender
{
	public		$root_folder,
				$info,
				$payload,
				$content,
				$render_content,
				$hiarchy_content,
				$component_settings,
				$renderEngine,
				$MenuEngine;

	protected	$Style,
				$ai,
				$markup,
				$uId,
				$curr,
				$addNew;

	public function getEditStatus($unset = false)
	{
		if(isset($this->getDataNode('_SESSION')->toggle->edit)) {
			if($unset) {
				unset($this->getDataNode('_SESSION')->toggle->edit);
				unset($_SESSION['toggle']['edit']);
				return false;
			}

			return true;
		}
		return false;
	}

	public	function componentEditors($component_settings = array())
	{
		$this->addNew					=	false;
		$this->component_settings		=	$component_settings;
		// This is set on the premise that is a new component for the page
		if(!is_array($this->component_settings)) {
			$unique_id					=	$this->component_settings;
			$this->component_settings	=	array();
			$this->addNew				=	true;
		}
		$this->curr	=	(!empty($this->component_settings))? $this->component_settings: false;
		$this->uId	=	(!empty($this->curr['unique_id']))? $this->curr['unique_id']: $this->curr['ID'];
		$custom		=	NBR_CLIENT_DIR.DS.'Components'.DS.'template'.DS.'page'.DS.'index.php';
		$default	=	__DIR__.DS.'core'.DS.'componentEditors.php';
		$render		=	(!is_file($custom))? $default : $custom;

		return $this->render($render,'include');
	}
	/*
	** @description	This method takes the component settings from the database
	**				and processed the data to create the look of the component
	*/
	protected	function getCompSettings($curr,$unique_id)	
	{
		$nImage		=	$this->getHelper('nImage');
		$coreImpUrl	=	$this->imagesUrl('core');
		$aTag		=	(!empty($curr['admin_tag']))? 'background-color: '.$curr['admin_tag'].';': '';
		$aNotes		=	(!empty($curr['admin_notes']));
		$_is_img	=	$this->checkEmpty($curr,'component_type','image');
		# Set usergroup allowed
		$usergroup	=	(isset($curr['login_permission']))? $curr['login_permission'] : false;
		$sVars		=	[
			"unique_id"=>((isset($curr['unique_id']))? $curr['unique_id'] : false),
			"ref_page"=>((isset($unique_id))? $unique_id:$curr['ref_page']),
			"autorun"=>true];
		$icon		=	$this->imagesUrl('/core/icn_alert.png');
		if(!empty($curr['component_type'])) {
			if(is_file(NBR_MEDIA_IMAGES.DS.'core'.DS.'icn_'.$curr['component_type'].'.png'))
				$icon	=	$this->imagesUrl('/core/icn_'.$curr['component_type'].'.png');
			// Convert icon to base 64
			elseif(is_file($icFile = NBR_CLIENT_DIR.DS.'Components'.DS.$curr['component_type'].DS.'icon.png')) {
				$icon	=	$nImage->toBase64($icFile);
			}
		}

		$sIcon	=	$coreImpUrl.'/led_'.(($this->checkEmpty($curr,'page_live','on'))? 'green': 'red').'.png';
		$bImg	=	'background-image: url('.$coreImpUrl.'/mesh.png); background-repeat: no-repeat; background-size: cover; background-position: center;';
		if(!empty($curr['file_path'])) {
			if(is_file(NBR_ROOT_DIR.$curr['file_path'].$curr['file_name'])) {
				$use	=	(is_file(str_replace(DS.DS,DS,NBR_THUMB_DIR.DS."components".DS.$curr['file_name'])))? str_replace(NBR_ROOT_DIR,"",NBR_THUMB_DIR."/components/") : $curr['file_path'];	
				$bImg	= 'background-image: url('.$this->siteUrl().str_replace(DS,'/',$use).$curr['file_name'].'); background-repeat: no-repeat; background-size: cover;';
			}
		}
		$userInc	=	false;
		if(($usergroup !== false) && is_numeric($this->getUsergroup($usergroup))) {
			$userInc	=	$this->getUsergroup($usergroup);
		}
		elseif($this->checkEmpty($curr,'login_view','on') && $usergroup === '') {
			$userInc	=	(defined("NBR_WEB"))? NBR_WEB : 3;
		}

		$loginReq	=	($this->checkEmpty($curr,'login_view','on'))? '':'opacity: 0; ';
		$attr[]		=	(!empty($curr['admin_notes']))? $nImage->image(NBR_MEDIA_IMAGES.DS.'core'.DS.'icn_edit.png',array('style'=>'max-height: 22px;', 'class'=>"nbr_notes")) :"";
		$attr[]		=	(!empty($curr['file_path']))? $nImage->image(NBR_MEDIA_IMAGES.DS.'core'.DS.'icn_image.png',array('style'=>"max-height: 22px;")) :"";
		$attr[]		=	(!empty($curr['content']))? $nImage->image(NBR_MEDIA_IMAGES.DS.'core'.DS.'icn_cont.png', array('style'=>'max-height: 22px;')) :"";
		$attr[]		=	(!empty($curr['admin_lock']))? $nImage->image(NBR_MEDIA_IMAGES.DS.'core'.DS.'lock.png', array('style'=>'max-height: 25px;')) : "";
		$attr[]		=	($userInc)? $nImage->image(NBR_MEDIA_IMAGES.DS.'core'.DS.'login_'.$userInc.'.png', array('style'=>$loginReq.'max-height: 20px;')) : "";
		$attr[]		=	($this->getPlugin('nPlugins\Nubersoft\Locales')->hasLocale($curr['ID']) > 0)? $nImage->image(NBR_MEDIA_IMAGES.DS.'core'.DS.'locales.png',array('style'=>'max-height: 20px;')) : "";

		$attr		=	array_filter($attr);


		return (object) array(	
			"is_new"=>(empty($curr['component_type']) && !empty($curr['ID'])),
			"is_img"=>$_is_img,
			"aTag"=>$aTag,
			"aNotes"=>$aNotes,
			"bImg"=>$bImg,
			"admin_notes"=>((!empty($curr['admin_notes']))? $curr['admin_notes'] : false),
			"attr"=>$attr,
			"sVars"=>$sVars,
			"sIcon"=>$sIcon,
			"icon"=>$icon
		);
	}
}