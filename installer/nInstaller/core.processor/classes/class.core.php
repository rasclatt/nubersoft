<?php
	class	core
		{
			public		static	$root_folder;
			public		static	$info;
			public		static	$payload;
			public		static	$content;
			public		static	$render_content;
			public		static	$hiarchy_content;
			public		static	$component_settings;
			public		static	$RenderEngine;
			
			protected	static	$Style;
			protected	static	$ai;
			protected	static	$nuber;
			protected	static	$markup;
			protected	static	$nubquery;
			
			private		static	$uId;
			private		static	$curr;
			private		static	$addNew;
			
			public	static	function Initialize()
				{
					register_use(__METHOD__);
				}
			
			// Main rendering iterator function for displaying page
			protected	static	function RenderIterator($current, $key = '')
				{
					register_use(__METHOD__);
					
					// Initialize the processing of array settings
					self::$RenderEngine->Initialize(self::$info[$key]);	
					$_layout	=	self::$RenderEngine->SetStyles()->SetIdClass('_id')->SetIdClass('class')->Compile()->display_inline;
					$_perm		=	self::$RenderEngine->CheckPermissions();
					
					if(empty($current)) {
						if($_perm) {
							echo self::$RenderEngine->Display()->display;
						}
					}
					
					if($_perm) {
							if(is_array($current)) {
								if(self::$info[$key]['component_type'] == 'div' || self::$info[$key]['component_type'] == 'row') { ?>

			<div <?php echo $_layout; ?>><?php
								}
							}
			
							if(is_array($current)) {
								foreach($current as $childkey => $childvalue) {
									self::RenderIterator($childvalue,$childkey);
								}
							}
								
							if(is_array($current)) { 
								if(self::$info[$key]['component_type'] == 'div' || self::$info[$key]['component_type'] == 'row') { ?>
			</div><?php		}
						}
					}
				}

			public	static	function execute()
				{
					register_use(__METHOD__);
					AutoloadFunction('nQuery,get_edit_status');
					if(DatabaseConfig::$con != false) {
						// New track/render instance
						self::$RenderEngine	=	new RenderPageElements();
						$track				=	new TrackEditor(ROOT_DIR);
						// Retrieve settings
						$prefs				=	nApp::getPage();
						$user				=	nApp::getUser();
						$unique_id			=	(!empty($prefs->unique_id))? $prefs->unique_id : false;
						// Insert Default CSS
						$track->DefaultCSS();
						// Run a query to check if there are rows for this page
						$_content			=	nQuery()	->select()
															->from("components")
															->where(array("ref_page"=>$unique_id,"parent_id"=>$unique_id),false,false,"or")
															->orderBy(array("page_order"=>"ASC"))
															->fetch();
						
						// If there are rows found for page, continue on	
						if($_content !== 0) {
							// Autoload required functions
							AutoLoadFunction('tree_structure,is_admin,check_empty');
							// See if the editor is turned on
							$turnedOnEdit		=	(get_edit_status())? '': "and page_live = 'on'";
							// Loop through rows, save all component unique_ids to filtered array
							foreach($_content as $object) {
								$css	=	array();
								// Split out the css
								if(!empty($object['c_options'])) {
									try {
											AutoloadFunction("serialbox");
											$css	=	serialbox($object,'c_options');
										}
									catch (Exception $e) {
											echo $e->getMessage();
										}
								}
									
								// Apply css to object
								if(isset($object['c_options']))
									unset($object['c_options']);
								
								$object	=	array_merge($object,$css);

								// Assign final array
								self::$info[$object['unique_id']]	=	$object;
								// Filter the array out
								self::$info[$object['unique_id']]	=	array_filter(self::$info[$object['unique_id']]);
								// Remove $css so it doesn't persist to the next set-up
								unset($css);
							}
					
							// Save to object for output to normal array
							$struc	=	new ArrayObject(tree_structure(self::$info, $parent = 0));
							// Save to an easily recursable array
							foreach($struc as $keys => $values) {
								$struct[$keys]	=	$values;
							}

							if(get_edit_status() && is_admin()) {
								// Run a query to check if there are rows for this page
								$page_components	=	nQuery()	->select("COUNT(*) as count")
																	->from("components")
																	->where(array("ref_page"=>$unique_id,"parent_id"=>$unique_id),false,false,"or")
																	->addCustom($turnedOnEdit)
																	->fetch();
								if($page_components[0]['count'] > 0) {
									// Render out the page
									$track->Track($struct,self::$info);
								}
							}
							else {
								AutoloadFunction("is_loggedin");
								// If page is turned on
								$_page_live	=	($prefs->page_live == 'on');
								// if Login is required to access
								$_login_req	=	($prefs->session_status == 'on');
								// If user is logged in
								$_logged_in	=	(is_loggedin());
								// If page on
								if($_page_live || (!$_page_live && is_admin())) {
									// If login required and loggedin, or login is not required
									if(($_login_req && $_logged_in) || !$_login_req) {
										foreach($struct as $key => $array) {
											// Render engine
											self::RenderIterator($array,$key);
										}
									}
								}
							}
						}
					else {
						// If the page is empty, and the edit is toggled, show an empty component
						if(get_edit_status()) {
?>				<div class="componentWrap">
					<?php self::ComponentEditors($unique_id); ?>
				</div>
<?php						}
						}
					}
				}
			
			public	static function TrackView($root_folder = false, $payload,$content)
				{
					AutoloadFunction('get_edit_status');
					self::$payload	=	$payload;
					self::$content	=	$content;
					$component		=	new create();
					
					// Create a style string for subsequent style="" if set
					self::SetStyle();
					
					$row_wrap	=	(self::$content[key(self::$payload)]['component_type'] == 'row');
					
					// If the component is a row, then add the default two container divs
					if($row_wrap == true) { ?>
                    
                    <div<?php echo self::$style_arr['style']; ?> class="<?php echo self::$style_arr['class']; ?>">
                        <div class="graybar_1_content"><?php
						}
						
					$divSet		=	(is_array(self::$payload))? true: false;
					
					if(get_edit_status()):
						// Track editor
						self::TrackEditorRender(self::$content, self::$payload);
					endif;
					
					if($row_wrap) { ?>
                        </div>
					</div>

					<?php }
				}

			public	static	function TrackEditorRender($render_content, $hiarchy_content)
				{
					register_use(__METHOD__);
					
					self::$hiarchy_content	=	$hiarchy_content;
					self::$render_content	=	$render_content;
					$divSet					=	(is_array(self::$hiarchy_content))? true: false;
					
					if($divSet) {
?>					<div class="track_editor">
<?php				}
						
					// Loop through the tree array
					foreach(self::$hiarchy_content as $keys => $values) {
						// This is the content array+unique_id value 
						$info	=	self::$render_content[$keys];
						
						// Start an instance of a render
						$rendElem		=	new renderElements;
						// Styles for this container component if not row
						$rendElem->renderContentElements($info, self::GetCSSList());
						
						$is_div	=	(isset($info['component_type']) && $info['component_type'] == 'div')? true: false;
						if($is_div) {
?>						<div <?php $styles = self::CreateStyles($info,self::GetCSSList()); if(!empty($styles)) echo 'style="'.$styles.'"'; ?>>
							<div class="track_editor_nested" <?php if(isset($info['admin_tag']) && !empty($info['admin_tag'])) { ?>style="background-color: <?php echo $info['admin_tag']; ?>; background-image: url(/core_images/core/window_grad.png); background-repeat: repeat-x;"<?php } ?>><?php 
						}
						AutoloadFunction('get_edit_status');
						if(get_edit_status()) 
							self::ComponentEditors($info);
										
						// If value is an array, loop back through this method
						if(is_array($values)) {
							self::TrackView($values,self::$render_content);
						}
							
						if($is_div) {
?>							</div>
						</div>
<?php 					}
					}
						
					if($divSet) {
?>					</div>
<?php				}
				}
				
			public	static	function ComponentEditors($component_settings = array())
				{
					self::$addNew					=	false;
					self::$component_settings		=	$component_settings;
					// This is set on the premise that is a new component for the page
					if(!is_array(self::$component_settings)) {
						$unique_id					=	self::$component_settings;
						self::$component_settings	=	array();
						self::$addNew				=	true;
					}
						
					self::$curr	=	(!empty(self::$component_settings))? self::$component_settings: false;
                    self::$uId	=	(!empty(self::$curr['unique_id']))? self::$curr['unique_id']: self::$curr['ID'];
					
					if(!is_file($custom = CLIENT_ASSETS.'/components/template/page/index.php'))
						include(RENDER_LIB.'/class.html/core/ComponentEditors.php');
					else
						include($custom);
				}
			/*
			** @description	This method takes the component settings from the database and processed the data to create the look of the component
			*/
			private	static	function getCompSettings($curr,$unique_id)	
				{
					$aTag		=	(!empty($curr['admin_tag']))? 'background-color: '.$curr['admin_tag'].';': '';
					$aNotes		=	(!empty($curr['admin_notes']));
					$_is_img	=	check_empty($curr,'component_type','image');
					$sVars		=	array(	"unique_id"=>((isset($curr['unique_id']))? $curr['unique_id'] : false),
										"ref_page"=>((isset($unique_id))? $unique_id:$curr['ref_page']),
										"autorun"=>true);
					$icon		=	site_url().'/core_images/core/icn_alert.png';
					if(!empty($curr['component_type'])) {
						if(is_file(ROOT_DIR.'/core_images/core/icn_'.$curr['component_type'].'.png'))
							$icon	=	site_url().'/core_images/core/icn_'.$curr['component_type'].'.png';
						elseif(is_file(CLIENT_DIR.'/components/'.$curr['component_type'].'/icon.png'))
							$icon	=	site_url().'/client_assets/components/'.$curr['component_type'].'/icon.png';
					}
					
					$sIcon	=	'/core_images/core/led_'.((check_empty($curr,'page_live','on'))? 'green': 'red').'.png';
					$bImg	=	'background-image: url('.site_url().'/core_images/core/mesh.png); background-repeat: no-repeat; background-size: cover; background-position: center;';
					if(!empty($curr['file_path'])) {
						if(is_file(ROOT_DIR.$curr['file_path'].$curr['file_name'])) {
							$use	=	(is_file(str_replace("//","/",THUMB_DIR."/components/".$curr['file_name'])))? str_replace(ROOT_DIR,"",THUMB_DIR."/components/") : $curr['file_path'];	
							$bImg	= 'background-image: url('.site_url().$use.$curr['file_name'].'); background-repeat: no-repeat; background-size: cover;';
						}
					}
					
					$userInc	=	false;
					if(!empty($curr['login_permission']) && is_numeric($curr['login_permission'])) {
						$userInc	=	$curr['login_permission'];
					}
					elseif(check_empty($curr,'login_view','on') && !is_numeric($curr['login_permission'])) {
						$userInc	=	(define("NBR_WEB"))? NBR_WEB : 3;
					}
					
					$loginReq	=	(check_empty($curr,'login_view','on'))? '':'opacity: 0.5; ';
					$attr[]		=	(!empty($curr['admin_notes']))? '<img src="'.site_url().'/core_images/core/icn_edit.png" style="max-height: 22px;" class="nbr_notes" />':"";
					$attr[]		=	(!empty($curr['file_path']))? '<img src="'.site_url().'/core_images/core/icn_image.png" style="max-height: 22px;" />':"";
					$attr[]		=	(!empty($curr['content']))? '<img src="'.site_url().'/core_images/core/icn_cont.png" style="max-height: 22px;" />':"";
					$attr[]		=	(!empty($curr['admin_lock']))? '<img src="'.site_url().'/core_images/core/lock.png" style="max-height: 25px;" />':"";
					$attr[]		=	($userInc)? '<img src="'.site_url().'/core_images/core/login_'.$userInc.'.png" style="'.$loginReq.'max-height: 20px;" />':"";
					$attr		=	array_filter($attr);
					
					
					return (object) array(	
											"is_new"=>(empty($curr['component_type']) && !empty($curr['ID'])),
											"is_img"=>$_is_img,
											"aTag"=>$aTag,
											"aNotes"=>$aNotes,
											"bImg"=>$bImg,
											"admin_notes"=>$curr['admin_notes'],
											"attr"=>$attr,
											"sVars"=>$sVars,
											"sIcon"=>$sIcon,
											"icon"=>$icon
										);
				}
			
			public	static	function WrongPage($error404 = false,$message = array('title'=>"Whoops! Page not found.",'body'=>"It's possible the page you are looking for has been moved or removed."))
				{
					if(is_file($error404)) {
						include($error404);
						return true;
					}
					else
						return $message;
				}
			
			public	static	function Footer($settings = false)
				{
					register_use(__METHOD__);
					
					AutoloadFunction('apply_markup,check_empty,display_autoloaded');
					$content		=	(!empty($settings['content']))? $settings['content']:false;
					$toggle			=	(!empty($settings['toggle']))? $settings['toggle']:false;
					$bypass			=	(!empty($settings['bypass']))? $settings['bypass']:false;
					$allowBypass	=	($bypass != false && is_file($_file = ROOT_DIR.$bypass));
					
					if($allowBypass == true) {
						include($_file);
						return;
					}
						
					if($toggle == 'on') {
						AutoloadFunction("use_markup");
						echo Safe::decode(use_markup($content));
					}
					else {
						include(TEMPLATE_DIR.'/default/foot.php');
					}

					if(!empty($_error)) {
						$errorString	=	'';
						foreach($_error as $key => $value) {
							if(is_array($value))
								$errorString	.=	strtoupper($key).'<br />[ '.implode("<br /> ",$value).' ]<br />';
							else
								$errorString	.=	strtoupper($key).' [ '.$value.' ]<br />';
						}
						include(RENDER_LIB.'/class.html/core/Footer.js.php');
					}
				}
				
			public	static	function Header($header = false,$_bypass = false)
				{
					register_use(__METHOD__);
					
					if($_bypass != false) {
							if(is_file($_file = ROOT_DIR.$_bypass)) {
									include($_file);
									return;
								}
						}
						
					if(isset($header->page_live) && $header->page_live == 'on') {
							AutoloadFunction('use_markup');
							echo use_markup(Safe::decode($header->content));
							return;
						}
					
					include(RENDER_LIB.'/class.html/core/Header.php');
					
					AutoloadFunction('is_admin');
					if(($_bypass != false) && (is_admin())) {
							global $_error;
							$_error['bypass'][]	=	'Header: File not found.';
						}
				}
		}