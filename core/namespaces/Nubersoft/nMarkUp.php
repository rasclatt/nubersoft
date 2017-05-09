<?php
namespace Nubersoft;

class nMarkUp extends \Nubersoft\nRender
	{
		public	function automate($match)
			{
				if(isset($match[0])) {
					$replaced	=	str_replace("~","",$match[1]);
					if(preg_match('/eval::/i',$replaced)) {
						$allow	=	(defined("ALLOW_EVAL"))? ALLOW_EVAL : false;
						if($allow) {
							$replaced	=	str_replace(array("eval::","EVAL::"),"",$replaced);
							$command	=	$this->safe()->decode($replaced);
							ob_start();
							eval($command);
							$data		=	ob_get_contents();
							ob_end_clean();
							return $data;
						}
					}
					# This will attempt to add a template component
					elseif($front = (stripos($replaced,'FRONTEND::') !== false) || $back = (stripos($replaced,'BACKEND::') !== false)) {
						$action		=	(!empty($front))? 'FRONTEND::' : 'BACKEND::';
						$replaced	=	str_replace($action,'',$replaced);
						$link		=	(!empty($front))? $this->getFrontEndPath($replaced) : $this->getBackEndPath($replaced);
						if(!empty($link))
							return $this->render($link);
					}
					# This will attempt to add a template component
					elseif(stripos($replaced,'PLUGIN::') !== false) {
						$file	=	str_replace('PLUGIN::','',$replaced);
						return $this->useTemplatePlugin($file);
					}
					# This will attempt to add a template component
					elseif(strtolower($replaced) == 'site_url') {
						return $this->siteUrl();
					}
					# This will retreive componenents from the database
					elseif(stripos($replaced,'COMPONENT::') !== false) {
						# Strip out the label
						$unique_id	=	str_replace('COMPONENT::','',$replaced);
						# Get component
						$array		=	$this->nQuery()
											->query("select * from `components` where `unique_id` = :0",array(trim($unique_id)))
											->getResults(true);
						if($array == 0)
							return false;
						
						# Initialize the processing of array settings
						$coreRender	=	$this->getPlugin('\nPlugins\Nubersoft\RenderPageElements');
						$_layout	=	$coreRender->initialize($array);
						$_perm		=	$coreRender->checkPermissions();
						return $coreRender->display(true)->getDisplay();
					}
					elseif(preg_match('/app::/i',$replaced)) {
						$replaced	=	str_replace(array("app::","APP::"),"",$replaced);
						$settings	=	explode("[",trim($replaced,"]"));
						$settings	=	array_filter($settings);
						$function	=	array_shift($settings);
						$keypairs	=	(isset($settings[0]))? $settings[0]:false;
						
						if(!function_exists($function))
							$this->autoload($function);
						
						if(function_exists($function)) {
							# Match key value pairs
							preg_match_all('/([^\=]{1,})="([^"]{1,})"/i',$keypairs,$pairs);
							# If there are key value pairs loop through the pairs
							if(!empty($pairs[0])) {
								$pairs[0]	=	NULL;
								
								$i = 0;
								foreach($pairs[1] as $values) {
									$values	=	trim($values);
									$array[$values]	=	(isset($pairs[2][$i]))? trim($pairs[2][$i]):false;
									$i++;
								}
							}
							
							$array	=	(isset($array))? $array : $keypairs;
							$access	=	true;
							# This will disallow access to full core functions
							if(defined("F_ACCESS") && F_ACCESS !== true) { 
								if(is_file(NBR_FUNCTIONS.DS."{$function}.php"))
									$access	=	false; 
							}
							
							return $function($array);
						}
							
						return $replaced;
					}
					elseif(strpos($replaced,'MATCH::') !== false) {
						$string	=	str_replace(array('MATCH::','{','}'),array('','~','~'),$replaced);
						return $this->getHelper('nAutomator')->matchFunction($string);
					}
					elseif(strpos($replaced,'CONSTANT::') !== false && !empty(constant(str_replace('CONSTANT::','',$replaced)))) {
						return constant(str_replace('CONSTANT::','',$replaced));
					}
					elseif(strpos($replaced,'WORKFLOW::') !== false) {
						$workflow	=	str_replace('WORKFLOW::','',$replaced);
					}
					elseif(strpos($replaced,'LOGGEDOUT::') !== false) {
						if(!$this->isLoggedIn()) {
							$value	=	str_replace('LOGGEDOUT::','',$replaced);
							return	$value;
						}
						return '';
					}
					elseif(strpos($replaced,'LOGGEDIN::') !== false) {
						if($this->isLoggedIn()) {
							$value	=	str_replace('LOGGEDIN::','',$replaced);
							return	$value;
						}
						return '';
					}
					elseif(strpos($replaced,"[") === false) {
						if(is_file(NBR_ROOT_DIR.$replaced)) {
							ob_start();
							include(NBR_ROOT_DIR.$replaced);
							$data	=	ob_get_contents();
							ob_end_clean();
							
							return $data;
						}
						else {
							$this->autoload('get_markup_command');
							$array	=	get_markup_command($replaced);
							if(is_array($array))
								return printpre($array,"Array: ".strtoupper($replaced));
							else
								return $replaced;
						}
							
						return;
					}
					
					$this->autoload('apply_submarkup');
					return preg_replace_callback('/([a-z0-9]{1,})::([a-z0-9\/\.\_\-\>\[\]\s\:\,]{1,})/i','apply_submarkup',$replaced);
				}
			}
	}