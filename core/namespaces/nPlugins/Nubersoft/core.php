<?php
namespace nPlugins\Nubersoft;

class core extends \nPlugins\Nubersoft\CoreHelper
	{
		public	function __construct()
			{
				$this->renderEngine	=	new RenderPageElements();
				$this->MenuEngine	=	new MenuEngine();
				return parent::__construct();
			}
		
		protected	function getInfo($key,$type,$default = false)
			{
				if(isset($this->info[$key][$type]))
					return $this->info[$key][$type];
				else
					return $default;
			}
		
		# Main rendering iterator function for displaying page
		protected	function renderIterator($current, $key = '')
			{
				# Initialize the processing of array settings
				$this->renderEngine->initialize($this->info[$key]);	
				$_layout	=	$this->renderEngine	->setStyles()
													->setIdClass('_id')
													->setIdClass('class')
													->compile()
													->display_inline;

				$_perm		=	$this->renderEngine->checkPermissions();		
				$id			=	$this->getInfo($key,'ID');
				$currType	=	$this->getInfo($key,'component_type','undefined');
				$isCode		=	($currType == 'code');
				$renderWrap	=	($currType == 'div' || $currType == 'row' || $isCode);
				$locales	=	$this->getLocaleRestrictions($id);
				# If there is an array of locales (possible restrictions), set to restrict
				$restrict	=	(!empty($locales));
				# If there is an array of ids
				if(is_array($locales) && !empty($locales)) {
					# If the current locale is in the array of approved
					if(in_array($this->getLocale(),$locales)) {
						# Set no restrictions
						$restrict	=	false;
					}
				}
				else
					# Remove restriction if false positive
					$restrict	=	false;
				
				if(empty($current)) {
					if($_perm) {
						if($currType == 'code' && $this->isAdmin() && ($restrict === false)) { ?><article data-cid="<?php echo $id; ?>"><?php }
	
						if($restrict === false)
							echo $this->renderEngine->Display()->getDisplay();
	
						if($currType == 'code' && $this->isAdmin() && ($restrict === false)) { ?></article><?php }
					}
				}
				
				if($_perm && ($restrict === false)) {
						if(is_array($current) || $isCode) {
							if($renderWrap) { ?>

		<div <?php echo $_layout; ?>><?php
							}
						}
		
						if(is_array($current)) {
							foreach($current as $childkey => $childvalue) {
								$this->renderIterator($childvalue,$childkey);
							}
						}
							
						if(is_array($current) || $isCode) { 
							if($renderWrap) { ?>
		</div><?php		}
					}
				}
			}

		public	function execute()
			{
				if(!empty($this->getDataNode('connection')->health)) {
					# New track/render instance
					$track				=	new TrackEditor(NBR_ROOT_DIR);
					# Retrieve settings
					$prefs				=	$this->getPage();
					$user				=	$this->getUser();
					$unique_id			=	(!empty($prefs->unique_id))? $prefs->unique_id : false;
					# Insert Default CSS
					$track->DefaultCSS();
					# Run a query to check if there are rows for this page
					$_content	=	$this->nQuery()
										->select()
										->from("components")
										->where(array("ref_page"=>$unique_id,"parent_id"=>$unique_id),"OR",false,true)
										->addCustom("AND `ref_spot` = 'nbr_layout'")
										->orderBy(array("page_order"=>"ASC"))
										->fetch();
					
					# Saves default state for IDs as unrestricted
					$localeRestrict	=	true;
					# Fetch current
					$lList	=	$this->storeLocalesList($_content);
					# Save the list for use in the iterator
					$this->saveLocales();
					if($lList->getLocaleCount() > 0)
						$localeRestrict	=	true;
					# If there are rows found for page, continue on	
					if($_content !== 0) {
						# See if the editor is turned on
						$turnedOnEdit		=	($this->getEditStatus())? '': "and page_live = 'on'";
						# Loop through rows, save all component unique_ids to filtered array
						foreach($_content as $object) {
							if(empty($object['unique_id']))
								continue;
							$css	=	array();	
							# Apply css to object
							if(isset($object['c_options']))
								unset($object['c_options']);
							
							$object	=	array_merge($object,$css);
							# Assign final array
							$this->info[$object['unique_id']]	=	$object;
							# Filter the array out
							$this->info[$object['unique_id']]	=	array_filter($this->info[$object['unique_id']]);
							# Remove $css so it doesn't persist to the next set-up
							unset($css);
						}
						# Save to object for output to normal array
						$struc	=	new \ArrayObject($this->getTreeStructure($this->info, $parent = 0));
						
						# Save to an easily recursable array
						foreach($struc as $keys => $values) {
							$struct[$keys]	=	$values;
						}

						if($this->getEditStatus() && $this->isAdmin()) {
							$nQuery				=	$this->nQuery();
							# Run a query to check if there are rows for this page
							$page_components	=	$nQuery
														->select("COUNT(*) as count")
														->from("components")
														->where(array(
															"ref_page"=>$unique_id,
															"parent_id"=>$unique_id
														),"OR",false,true)
														->addCustom("AND `ref_spot` = 'nbr_layout'")
														->addCustom($turnedOnEdit)
														->fetch();
							
							if($page_components[0]['count'] > 0) {
								# Render out the page
								$track->track($struct,$this->info);
							}
						}
						else {
							# If page is turned on
							$_page_live	=	(isset($prefs->page_live) && $prefs->page_live == 'on');
							# if Login is required to access
							$_login_req	=	(isset($prefs->session_status) && $prefs->session_status == 'on');
							# If user is logged in
							$_logged_in	=	($this->isLoggedIn());
							# If page on
							if($_page_live || (!$_page_live && $this->isAdmin())) {
								# If login required and loggedin, or login is not required
								if(($_login_req && $_logged_in) || !$_login_req) {
									foreach($struct as $key => $array) {
										# Render engine
										$this->renderIterator($array,$key);
									}
								}
							}
						}
					}
				else {
					# If the page is empty, and the edit is toggled, show an empty component
					if($this->getEditStatus()) {
?>
			<div class="componentWrap">
				<?php echo $this->componentEditors($unique_id); ?>
			</div>
<?php						}
					}
				}
			}
		
		public	function getLocaleCount()
			{
				# Get the list of ids
				$keys	=	$this->getStoredLocales();
				# If there are no ids
				if(empty($keys))
					return 0;
				
				$count	=	$this->nQuery()
					->query("SELECT COUNT(*) as count FROM component_locales WHERE comp_id IN (".$keys.")")
					->getResults(true);
				
				return $count['count'];
			}
			
		protected	$localesList,
					$locales;
		
		protected	function storeLocalesList($keys,$col = 'ID')
			{
				if(!is_array($keys))
					return $this;
				
				$this->localesList[$col]	=	implode(",",array_keys($this->organizeByKey($keys,$col)));
				
				return $this;
			}
		
		public	function getStoredLocales($col = 'ID')
			{
				return (!empty($this->localesList[$col]))? $this->localesList[$col] : false;
			}
		
		public	function saveLocales()
			{
				if($this->getLocaleCount() == 0)
					$this->locales	=	array();
				else
					$this->locales	=	$this->getLocaleList();
				
				return $this;
			}
			
		public	function getLocaleRestrictions($ID)
			{
				if(isset($this->locales[$ID]))
					return $this->locales[$ID];
					
				return false;
			}
		
		public	function getLocaleList($keys = false)
			{
				if(empty($keys) && empty($this->getStoredLocales()))
					return false;
				
				if(empty($keys))
					$keys	=	$this->getStoredLocales();
				
				$locales	=	$this->nQuery()
					->query("SELECT `locale_abbr`, `comp_id` FROM component_locales WHERE comp_id IN (".$keys.")")
					->getResults();
					
				if($locales == 0)
					return false;
				
				foreach($locales as $row) {
					$new[$row['comp_id']][]	=	$row['locale_abbr'];
				}
				
				return $new;
			}
		
		public	function trackView($root_folder = false, $payload,$content)
			{
				$this->autoload('get_edit_status');
				$this->payload	=	$payload;
				$this->content	=	$content;
				$component		=	new create();
				
				# Create a style string for subsequent style="" if set
				$this->setStyle();
				
				$row_wrap	=	($this->content[key($this->payload)]['component_type'] == 'row');
				
				# If the component is a row, then add the default two container divs
				if($row_wrap) { ?>
				
				<div<?php echo $this->style_arr['style']; ?> class="<?php echo $this->style_arr['class']; ?>">
					<div class="graybar_1_content"><?php
					}
					
				$divSet		=	(is_array($this->payload));
				
				if(get_edit_status()):
					# Track editor
					$this->trackEditorRender($this->content, $this->payload);
				endif;
				
				if($row_wrap) { ?>
					</div>
				</div>

				<?php }
			}

		public	function trackEditorRender($render_content, $hiarchy_content)
			{
				$this->hiarchy_content	=	$hiarchy_content;
				$this->render_content	=	$render_content;
				$divSet					=	(is_array($this->hiarchy_content))? true: false;
				
				if($divSet) {
?>					<div class="track_editor">
<?php				}
					
				# Loop through the tree array
				foreach($this->hiarchy_content as $keys => $values) {
					# This is the content array+unique_id value 
					$info	=	$this->render_content[$keys];
					
					# Start an instance of a render
					$rendElem		=	new RenderPageElements;
					# Styles for this container component if not row
					$rendElem->renderContentElements($info, $this->GetCSSList());
					
					$is_div	=	(isset($info['component_type']) && $info['component_type'] == 'div');
					if($is_div) {
?>						<div <?php $styles = $this->CreateStyles($info,$this->GetCSSList()); if(!empty($styles)) echo 'style="'.$styles.'"'; ?>>
						<div class="track_editor_nested" <?php if(isset($info['admin_tag']) && !empty($info['admin_tag'])) { ?>style="background-color: <?php echo $info['admin_tag']; ?>; background-image: url(/images/core/window_grad.png); background-repeat: repeat-x;"<?php } ?>><?php 
					}
					if(get_edit_status()) 
						$this->componentEditors($info);
									
					# If value is an array, loop back through this method
					if(is_array($values)) {
						$this->trackView($values,$this->render_content);
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
			
		public	function footer($settings = false)
			{	
				$content		=	(!empty($settings['content']))? $settings['content']:false;
				$toggle			=	(!empty($settings['toggle']))? $settings['toggle']:false;
				$bypass			=	(!empty($settings['bypass']))? $settings['bypass']:false;
				$allowBypass	=	($bypass != false && is_file($_file = NBR_ROOT_DIR.$bypass));
				
				if($allowBypass == true) {
					include($_file);
					return;
				}
					
				if($toggle == 'on') {
					$this->autoload("use_markup");
					echo self::call('Safe')->decode(use_markup($content));
				}
				else {
					include(NBR_TEMPLATE_DIR.DS.'default'.DS.'frontend'.DS.'foot.php');
				}

				if(!empty($_error)) {
					$errorString	=	'';
					foreach($_error as $key => $value) {
						if(is_array($value))
							$errorString	.=	strtoupper($key).'<br />[ '.implode("<br /> ",$value).' ]<br />';
						else
							$errorString	.=	strtoupper($key).' [ '.$value.' ]<br />';
					}
					include(__DIR__.DS.'core'.DS.'Footer.js.php');
				}
			}
			
		public	function header($header = false,$_bypass = false)
			{
				if($_bypass != false) {
					if(is_file($_file = NBR_ROOT_DIR.$_bypass)) {
						include($_file);
						return;
					}
				}
				
				if(isset($header->page_live) && $header->page_live == 'on') {
					echo use_markup($this->Safe()->decode($header->content));
					return;
				}
				
				$this->useTemplatePlugin('render_site_logo');
				
				if(($_bypass != false) && ($this->isAdmin())) {
					global $_error;
					$_error['bypass'][]	=	'Header: File not found.';
				}
			}
	}