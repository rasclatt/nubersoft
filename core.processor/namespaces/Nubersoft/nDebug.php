<?php
namespace Nubersoft;

class	nDebug
	{
		private	static	$singleton;
		
		public	function __construct()
			{
				if(self::$singleton instanceof nDebug)
					return self::$singleton;
				
				self::$singleton	=	$this;
				
				return self::$singleton;
			}
		
		public	function printPre($args,$count)
			{
				$print		=	($count >= 1)? $args[0] : false;
				$values		=	array();
				$assemble	=	array();
				if(empty($print))
					return '<div class="nbsprintpre"><div>Empty</div></div>';
				
				$count	=	(($count-1) <= 0)? 0 : ($count-1); 
				for($i = 1; $i <= $count; $i++) {
					if(!empty($args[$i])) {
						$aVal		=	$this->printPreAutoSelect($args[$i]);
					
						if(empty($aVal['key']))
							continue;
							
						$values[$aVal['key']]	=	$aVal['value'];
					}
				}
					
				if(!empty($values['whitelist'])) {
					if(!in_array($_SERVER['REMOTE_ADDR'],$values['whitelist']))
						return false;
				}
				
				if(isset($values['line']))
					$assemble[]	=	$values['line'];
				
				if(isset($values['file']))
					$assemble[]	=	$values['file'];
					
				ob_start();
				include(NBR_RENDER_LIB._DS_.'class.html'._DS_.str_replace(__NAMESPACE__.'\\','',__CLASS__)._DS_.strtolower(__FUNCTION__)._DS_.'main.php');
				$data	=	ob_get_contents();
				ob_end_clean();
				return $data;
			}
		
		private	function printPreAutoSelect($val)
			{
				if(is_numeric($val))
					return array('key'=>'line','value'=>$val);
				elseif(is_string($val)) {
					if(strpos($val,_DS_) !== false)
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
							include(NBR_RENDER_LIB._DS_.'class.html'._DS_.str_replace(__NAMESPACE__.'\\','',__CLASS__)._DS_.__FUNCTION__._DS_.'main.php');
							$data	=	ob_get_contents();
							ob_end_clean();
							return array('key'=>'debugger','value'=>$data);
						}
						elseif($val == '{whitelist}') {
							$list	=	\nApp::getWhiteList('printpre');
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