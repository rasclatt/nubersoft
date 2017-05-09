<?php
namespace Nubersoft;

class	nDebug extends \Nubersoft\nApp
	{
		public	function printPre($print = false,$settings = false)
			{
				$values		=	array();
				$assemble	=	array();
				$debugBlock	=	false;
				$backtrace	=	(!isset($settings['backtrace']) || (isset($settings['backtrace']) && !empty($settings['backtrace'])));
				$line		=	(!empty($settings['line']))? $settings['line'] : '';
				
				if($backtrace) {
					$debugBlock	=	implode('</p><p style="margin: 0 0 5px 0;">',array_filter(array_map(function($v){
								$place	=	(!empty($v['file']))? str_replace(NBR_ROOT_DIR,'',$v['file']) : false;
								
								if(strpos($place,'printpre.php') === false) {
									if(isset($v['file']))
										return str_replace(NBR_ROOT_DIR,'',$v['file']).' <b>('.$v['line'].')</b>';
								}
							
							},debug_backtrace())));
				}
				
				if(empty($print))
					return '
				<div class="nbsprintpre">
					<h3 style="margin: 30px 0 5px 0; background: transparent; box-shadow: none; background-color: transparent; border: none;">Empty Value'.((!empty($line))? ' - LINE: '.$line : '').'</h3>'.((!empty($debugBlock))? '
					<div style="padding: 10px; border-radius: 3px; border: 1px solid; margin: 5px 0; background-color: #EBEBEB; font-family: Arial; box-shadow: inset 0 0 5px rgba(0,0,0,0.5);">
						<p style="margin: 0 0 5px 0; color: red;">'.$debugBlock.'
					</div>' : '').'
				</div>';
				
				if(!empty($settings['whitelist'])) {
					if(!in_array($_SERVER['REMOTE_ADDR'],$values['whitelist']))
						return false;
				}
				
				if(isset($settings['line']))
					$assemble[]	=	$settings['line'];
				
				if(isset($settings['file']))
					$assemble[]	=	$settings['file'];
					
				ob_start();
				include(__DIR__.DS.str_replace(__NAMESPACE__.'\\','',__CLASS__).DS.strtolower(__FUNCTION__).DS.'main.php');
				$data	=	ob_get_contents();
				ob_end_clean();
				return $data;
			}
		
		private	function printPreAutoSelect($val)
			{
				if(is_numeric($val))
					return array('key'=>'line','value'=>$val);
				elseif(is_string($val)) {
					if(strpos($val,DS) !== false)
						return array('key'=>'file','value'=>$val);
					else {
						if($val == '{backtrace}') {
							$dFuncs		=	get_defined_functions();
							$cCount		=	0;
							$fCount		=	0;
							$mCount		=	0;
							$eCount		=	0;
							$setFuncs	=	array('internal'=>0,'user'=>0,'anon'=>0);
							$debug		=	debug_backtrace();
							$disp		=	array();
							$NBR_ROOT_DIR	=	(defined('NBR_ROOT_DIR'))? NBR_ROOT_DIR : $_SERVER['DOCUMENT_ROOT'];
							$i = 1;
							foreach($debug as $key => $kind) {
								if(empty($kind['file']))
									continue;
								elseif(strpos($kind['file'],'nDebug.php') !== false)
									continue;
								elseif(strpos($kind['file'],'printpre.php') !== false)
									continue;
									
								$disp[$i]['file']		=	(!empty($kind['file']))? str_replace($NBR_ROOT_DIR,'',$kind['file']) : false;
								$disp[$i]['line']		=	(!empty($kind['line']))? $kind['line'] : false;
								$disp[$i]['class']		=	(!empty($kind['class']))? $kind['class'] : false;
								$disp[$i]['method']		=	(!empty($kind['function']) && !empty($kind['class']))? $kind['function'] : false;
								$disp[$i]['function']	=	(empty($disp[$i]['method']) && !empty($kind['function']))? $kind['function'] : false;
								
								if($disp[$i]['function'] == "eval") {
									$eCount	+= 1;
								}
								if(!empty($disp[$i]['class'])) {
									$cCount	+= 1;
								}
								if(!empty($disp[$i]['method'])) {
									$mCount	+= 1;
								}
								if(!empty($disp[$i]['function'])) {
									if(in_array(strtolower($disp[$i]['function']),$dFuncs['internal']))
										$setFuncs['internal']	+=	1;
									elseif(in_array(strtolower($disp[$i]['function']),$dFuncs['user']))
										$setFuncs['user']	+=	1;
									elseif(strpos($disp[$i]['function'],'{') !== false)
										$setFuncs['anon']	+=	1;
									
									$fCount	+= 1;
								}
								
								$disp[$i]	=	array_filter($disp[$i]);
								$i++;
							}
							
							ob_start();
							include(__DIR__.DS.str_replace(__NAMESPACE__.'\\','',__CLASS__).DS.__FUNCTION__.DS.'main.php');
							$data	=	ob_get_contents();
							ob_end_clean();
							return array('key'=>'debugger','value'=>$data);
						}
						elseif($val == '{whitelist}') {
							$list	=	$this->getWhiteList('printpre');
							if(!empty($list))
								return array('key'=>'whitelist','value'=>$list);
						}
					}
				}
				elseif(is_array($val))
					return array('key'=>'whitelist','value'=>$val);
				elseif(is_bool($val))
					return array('key'=>'dump','value'=>$val);
				
				return false;
			}
	}