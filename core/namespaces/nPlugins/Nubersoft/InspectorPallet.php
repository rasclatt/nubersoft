<?php
namespace nPlugins\Nubersoft;

class InspectorPallet extends \Nubersoft\nApp
	{
		protected	$ID;
		
		public	function getPageId()
			{
				if(!empty($this->ID))
					$id	=	$this->ID;
				elseif(!empty($this->getPost('deliver')->ID))
					$id	=	$this->getPost('deliver')->ID;
				elseif(!empty($this->getPageURI('ID')))
					$id	=	$this->getPageURI('ID');
				else
					$id	=	false;
				
				$this->ID	=	$id;
				
				return $this->ID;
			}
		
		public	function execute($settings = false)
			{
				$animate	=	(!empty($settings['fx']))? $settings['fx']:'fade';
				$buttons	=	(!empty($settings['toolbar']) && is_array($settings['toolbar']))? $settings['toolbar']:false;
				$this->ID	=	(!empty($settings['ID']))? $settings['ID'] : false;
				
				if($this->isAdmin()) {
					include(__DIR__.DS.'InspectorPallet'.DS.'execute.php');
				}	
			}
			
		public	function getAjaxPallet()
			{
				//$Cache	=	$this->getHelper('nCache');
				//$Cache->cacheBegin($this->getStandardPath(DS.'inspector.html'));
				//if(!$Cache->isCached())
				ob_start();	
				$this->execute($this->toArray($this->getPost()));
				$data	=	ob_get_contents();
				ob_end_clean();
				$this->ajaxResponse(array('html'=>array($data),'sendto'=>array('#tool_inspector_container')));
			}
		
		protected	function button()
			{	
				# ToggleFunction required to run the current session state for the toggle edit function
				return	$this->render(__DIR__.DS.'InspectorPallet'.DS.'Button.php','include',$this);
			}
		
		public	function adminToolsQuickLinks($admin_link = array())
			{
				if($this->siteValid()) {
					# Show tables in database
					$results	=	$this->toArray($this->getDataNode('tables'));
					return $this->render(__DIR__.DS.'InspectorPallet'.DS.'AdminToolsQuickLinks.php','include',$results);
				}
			}
		
		protected	function	AdminToolsPallet()
			{
				return new ToolInspector();
			}
		
		public	function getXmlInterfaces()
			{
				# Fetch xml interface prefs file
				$getBtns	=	$this->getPrefFile('bake');
				# Build a file
				if(empty($getBtns)) {
					
					if(!is_file($baker = NBR_CLIENT_SETTINGS.DS.'bake.xml')) {
						$this->saveIncidental('bake_file',array('msg'=>'You do not have a "bake.xml" file (located in your /client/settings/ directory). It is not required but allows you to add interface elements into your Admin page.'));
						return array();
					}
					
					$nHtml		=	$this->getHelper('nHtml');
					$nImage		=	$this->getHelper('nImage');
					$nAutomator	=	$this->getHelper('nAutomator',$this);
					$interface	=	$this->getHelper('nRegister')->parseXmlFile($baker);
					
					if(!empty($interface['interface']['object'])) {
						$obj	=	(isset($interface['interface']['object'][0]))? $interface['interface']['object'] : array($interface['interface']['object']);
						foreach($obj as $execute) {
							$attr	=	(isset($execute['@attributes']))? $execute['@attributes'] : false;
							if(empty($attr))
								continue;
								
							if(isset($attr['page_live']) && $attr['page_live'] == 'on') {
								if(isset($execute['button'])) {
									$link	=	(isset($execute['button']['@attributes']['link']))? $nAutomator->matchFunction(trim($execute['button']['@attributes']['link'])) : false;
									
									$button	=	$execute['button'];
									if(!empty($button['image']['path'])) {
										$path		=	$nAutomator->matchFunction($button['image']['path']);
										$local		=	(strpos($path,'http') === false);
										$inline		=	(!empty($button['instructions']['@attributes']))? $button['instructions']['@attributes'] : false;
										$imgInline	=	(!empty($button['image']['@attributes']))? $button['image']['@attributes'] : false;
										
										if(isset($button['instructions']['@attributes']))
											unset($button['instructions']['@attributes']);
											
										$instr		=	array(
											'data-instructions'=>((isset($button['instructions']['raw']))? $button['instructions']['raw'] : json_encode($button['instructions'])),
											'class'=>((!empty($button['instructions']['class'])? $button['instructions']['class'] : ''))
										);
										
										if($inline) {
											$inline		=	array_merge($inline,$instr);
											$layout[]	=	'<div '.$nHtml->processAttr($inline).'>';
										}
										
										if($link) {
											$layout[]	=	'<a href="'.$link.'">';
										}
										
										if($imgInline) {
											$layout[]	=	$nImage->image($path,$imgInline,$local,$local);
										}
										
										if($link) {
											$layout[]	=	'</a>';
										}
										
										if($inline) {
											$layout[]	=	'</div>';
										}
										
										$buttons[]	=	implode(PHP_EOL,$layout);
										$layout		=	array();
									}
								}
							}
						}

						# Save our html for quick retieve
						if(!empty($buttons)) {
							$this->savePrefFile('bake',$buttons);
						}
					}
				}
				else
					$buttons	=	$getBtns;
				
				return (!empty($buttons))? $buttons : array();
			}
		
		public function getEditStatus()
			{
				return (isset($this->getDataNode('_SESSION')->toggle->edit));
			}
		
		public	function validateToggle()
			{
				# Check if user is admin
				if(!$this->isAdmin())
					return;
				
				$dataSession	=	$this->toArray($this->getSession());
				
				if($this->getPost('toggle') == 1 || !empty($this->getGet('edit'))) {
					$dataSession['toggle']['edit']['type']	=	
					$_SESSION['toggle']['edit']['type']		=	$this->setDefaultToggle($this->getPost('type'));
				}
				else {
					unset($dataSession['toggle']);
					unset($_SESSION['toggle']);
				}
				
				$this->saveSetting('_SESSION',$dataSession,true);
			}
		
		private	function setDefaultToggle($value)
			{
				return (empty($value))? 'track' : $value;
			}
			
		public	function loginToggle()
			{
				$nRouter	=	$this->getHelper('nRouter');
				$redirect	=	$nRouter->stripIndex($this->getDataNode('_SERVER')->PHP_SELF);
				
				if($this->isAdmin()) {
					//$dataSession							=	$this->toArray($this->getSession());
					//$dataSession['toggle']['edit']['type']	=	
					//$_SESSION['toggle']['edit']['type']		=	$this->setDefaultToggle($this->getPost('type'));
					//$this->saveSetting('_SESSION',$dataSession,true);
					$this->setSession(array('admintools','editor'),true);
				}
				# If there is a jumppage, execute it
				if($this->getPost('jumppage'))
					$this->processJumpPage();
				# If there is an auto-forward after loging on
				elseif(!empty($this->getPageURI('auto_fwd_post'))){
					# Get path
					$path	=	$this->getPageURI('auto_fwd');
					# If there is a path to auto-forward to
					if(!empty($path)) {
						if($path !== 'NULL') {
							# If there is no external path, just tack on the site url
							if(strpos($path,'http') !== true)
								$path	=	$this->siteUrl().$path;
							# Route to a new page
							$nRouter->addRedirect($path);
						}
					}
				}
				
				# Redirect back to self
				$nRouter->addRedirect($redirect);
			}
	}