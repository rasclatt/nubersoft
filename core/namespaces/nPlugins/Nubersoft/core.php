<?php
namespace nPlugins\Nubersoft;

class core extends \nPlugins\Nubersoft\CoreHelper
{

	protected	$localesList,
				$locales;

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
		include($this->getTemplatePlugin('core_render_iterator'));
	}

	public	function execute()
	{
		include($this->getTemplatePlugin('core_execute'));
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
		include($this->getTemplatePlugin('core_track_view'));
	}

	public	function trackEditorRender($render_content, $hiarchy_content)
	{
		include($this->getTemplatePlugin('core_track_editor_render'));
	}

	public	function footer($settings = false)
	{	
		$content		=	(!empty($settings['content']))? $settings['content']:false;
		$toggle			=	(!empty($settings['toggle']))? $settings['toggle']:false;
		$bypass			=	(!empty($settings['bypass']))? $settings['bypass']:false;
		$allowBypass	=	($bypass != false && is_file($_file = NBR_ROOT_DIR.$bypass));

		if($allowBypass) {
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