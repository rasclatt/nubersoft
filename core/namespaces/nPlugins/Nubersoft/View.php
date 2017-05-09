<?php
namespace nPlugins\Nubersoft;

class View extends \nPlugins\Nubersoft\core
	{
		private	static	$storeBuild;
		
		public	function __construct($renderMethod = false,$settings = false)
			{
				
				if($renderMethod && method_exists($this,$renderSiteLogo)) {
					self::$storeBuild[$renderMethod]	=	$this->{$renderMethod}($settings);
				}
				
				return parent::__construct();
			}
		
		public	function renderMastHead()
			{
				if(!empty(self::$storeBuild[__FUNCTION__]))
					return self::$storeBuild[__FUNCTION__];
					
				$content	=	$this->getSitePrefs();
				$bypass		=	(isset($content->site->content->head))? $content->site->content->head:false;
						
				if(!empty($mast_local[1]['file']) && is_file($mastfile = dirname($mast_local[1]['file']).DS."masthead.php")) {
					ob_start();
					include($mastfile);
					$data	=	ob_get_contents();
					ob_end_clean();
					$layout['content']		=	$data;
					$layout['page_live']	=	"on";
				}
				else {
					if(isset($content->header->content->html->toggle) && $content->header->content->html->toggle == 'on') {
						$layout['content']		=	$content->header->content->html->value;
						$layout['page_live']	=	"on";
					}
					else {
						$layout['content']		=	false;
						$layout['page_live']	=	"off";
					}
				}
				
				$layout		=	(object) $layout;
				
				ob_start();
				$this->header($layout,$bypass);
				$data	=	ob_get_contents();
				ob_end_clean();
				return $data;
			}
		
		public	function renderMenuBar($local = false)
			{
				if(!empty(self::$storeBuild[__FUNCTION__]))
					return self::$storeBuild[__FUNCTION__];
					
				if(empty($local))
					$bypass	=	(empty($this->getDataNode('bypass')->menu))? false : $this->getDataNode('bypass')->menu;
				else
					$bypass	=	$local;
					
				$menus	=	new MenuButton($this);
				ob_start();
				echo $menus->fetchSub()->graphicMenu($bypass)->thislayout;
				$data	=	ob_get_contents();
				ob_end_clean();
				return $data;
			}
			
		public	function useLayout($useLayout = false,$def = 'd')
			{
				# P for permissions page
				if($def == 'p')
					$this->permsPage	=	$useLayout;
				else
					$this->loginPage	=	$useLayout;
					
				return $this;
			}
		
		public	function loginPage($checkLogin)
			{
				if(!empty(self::$storeBuild[__FUNCTION__]))
					return self::$storeBuild[__FUNCTION__];
				
				ob_start();
				# If login is required
				if($checkLogin) {
					# If not logged in
					if(!$this->isLoggedIn()) {
						# Insert login Form
						echo $this->useTemplatePlugin('login_window','login'.DS.'dialogue.php');
					}
					# If logged in
					else {
						$_error['login'][]	= "PERMISSION DENIED.";
						# Notify permissions not good enough for viewing content
						echo $this->useTemplatePlugin('error403');
					}
				}
				
				$html	=	ob_get_contents();
				ob_end_clean();
				
				return $html;
			}
		
		public	function renderSiteLogo($settings = array())
			{
				if(!empty(self::$storeBuild[__FUNCTION__]))
					return self::$storeBuild[__FUNCTION__];
				
				$siteLogo	=	$this->getSiteLogo();
				$defLogo	=	NBR_MEDIA_IMAGES.DS.'logo'.DS.'default.png';
				
				if(empty($siteLogo))
					return '<!-- Empty logo file -->';
					
				if(!is_file($this->toSingleDs(NBR_ROOT_DIR.DS.$siteLogo))) {
					if(!is_file($defLogo))
						return '<!-- Logo file does not exist -->';
					else
						$siteLogo	=	$defLogo;
				}
				
				$opts[]	=	(!empty($settings['style']))? 'style="'.$settings['style'].'"':'';
				$opts[]	=	(!empty($settings['class']))? 'class="'.$settings['class'].'"':'';
				$opts[]	=	(!empty($settings['alt']))? 'alt="'.$settings['alt'].'"':'';
				$opts[]	=	(!empty($settings['id']))? 'id="'.$settings['id'].'"':'';
				$opts[]	=	(!empty($settings['name']))? 'name="'.$settings['id'].'"':'';
				$opts[]	=	(!empty($settings['data']))? 'data-'.$settings['data'][0].'="'.$settings['data'][1].'"':'';
				$opts[]	=	(!empty($settings['link']))? 'onClick="window.location=\''.$settings['link'].'\'"':'';
				$opts[]	=	(!empty($settings['custom']))? $settings['custom']:'';
				
				return $this->getHelper('nImage')->image($siteLogo,array_filter($settings),true);
			}
		
		public	function renderIncidental($name,$type = 'is_incidental')
			{
				if($this->getIncidental($name)) {
					$incidentals	=	array();
					$this->flattenArray($this->toArray($this->getIncidental($name)),$incidentals);
					$incidentals	=	array_filter($incidentals);
					foreach($incidentals as $key => $name) {
						$incidentals[$key]	=	ucfirst($name);
					}
					$img	=	$this->getHelper('nImage')->image(NBR_MEDIA_IMAGES.DS.'core'.DS.'icn_alert.png',array('style'=>'max-height: 20px; position: relative; top: 5px; margin: 0; margin-top: -5px;'));
					return '<div class="nbr_error_msg '.$type.'">'.$img.implode('</div><br />'.PHP_EOL.'<div class="nbr_error_msg '.$type.'">'.$img,$incidentals).'</div>';
				}
			}
		
		public	function renderFooter($file = false)
			{
				if($file != false && is_file($file)) {
					return $this->render($file);
				}
				else {
					return $this->render($this->getFrontEnd(DS.'foot.php'));
				}
			}
			
		protected	function getFootPrefs($key = false)
			{
				if(isset($this->getDataNode('preferences')->settings_foot)) {
					$prefs	=	$this->getDataNode('preferences')->settings_foot;
					
					if(!empty($key))
						return (isset($prefs->{$key}))? $prefs->{$key} : false;
					
					return $this->toArray($prefs);
				}
			}
	}