<?php
$defaultVars	=	extract([
	'sign_up' => [],
	'maintenance_mode' => [],
	'frontend_admin' => [],
	'site_live' => [],
	'template' => [],
	'timezone' => [],
	'htaccess' => [],
	'two_factor_auth' => [],
	'webmaster' => [],
	'fileid' => [],
	'devmode' => []
]);

$system_settings	=	$this->getDataNode('settings')['system'];
$Settings			=	extract($system_settings);
$defaults			=	[
	[
		'label' => 'Webmaster'.((defined('WEBMASTER'))? ' (Registry: '.WEBMASTER.')':''),
		"name" => "setting[webmaster]",
		"type" => "text",
		'value' => (!empty($webmaster['option_attribute']))? $webmaster['option_attribute'] : (defined('WEBMASTER')? WEBMASTER : ""),
		'class' => 'nbr',
		'other' => [
			'required="required"'
		]
	],
	[
		'label' => 'Allow Public Sign Up?',
		"name" => "setting[sign_up]",
		"type" => "select",
		"options" => array_map(function($v) use ($sign_up) {
			if($v['value'] == $sign_up['option_attribute'])
				$v['selected']	=	true;
			
			return $v;
		}, [
			["name" => "No","value" => "off"],
			["name" => "Yes","value" => "on"]
		]),
		'class' => 'nbr'
	],
	[
		'label' => '2 Factor Authentiation',
		"name" => "setting[two_factor_auth]",
		"type" => "select",
		"options" => array_map(function($v) use ($two_factor_auth) {
			if(!empty($two_factor_auth) && ($v['value'] == $two_factor_auth['option_attribute']))
				$v['selected']	=	true;
			
			return $v;
		}, [
			["name" => "Disabled (Use Basic Login)", 'value' => 'off'],
			["name" => "Admin Only","value" => "admin"],
			["name" => "Frontend Only","value" => "frontend"],
			["name" => "Admin and Frontend","value" => "both"]
		]),
		'class' => 'nbr'
	],
	[
		'label' => 'Allow Frontend Admin Login?',
		'name' => 'setting[frontend_admin]',
		'type' => 'select',
		"options" => array_map(function($v) use ($frontend_admin) {
			if(!empty($frontend_admin['option_attribute']) && ($v['value'] == $frontend_admin['option_attribute']))
				$v['selected']	=	true;
			
			return $v;
		},[
			["name" => "No","value" => "off"],
			["name" => "Yes","value" => "on"]
		]),
		'class' => 'nbr'
	],
	[
		'label' => 'Maintenance Mode',
		'name' => 'setting[maintenance_mode]',
		'type' => 'select',
		"options" => array_map(function($v) use ($maintenance_mode) {
			if(!empty($maintenance_mode['option_attribute']) && ($v['value'] == $maintenance_mode['option_attribute']))
				$v['selected']	=	true;
			
			return $v;
		},[
			["name" => "Off","value" => "off"],
			["name" => "On","value" => "on"]
		]),
		'class' => 'nbr'
	],
	[
		'label' => 'Site Status',
		'name' => 'setting[site_live]',
		'type' => 'select',
		"options" => array_map(function($v) use ($site_live) {
			if($v['value'] == $site_live['option_attribute'])
				$v['selected']	=	true;
			
			return $v;
		},[
			["name" => "Inactive","value" => "off"],
			["name" => "Live","value" => "on"]
		]),
		'class' => 'nbr'
	],
	[
		'label' => 'Global Template',
		'name' => 'setting[template]',
		'type' => 'select',
		'options' =>
			$this->createContainer(function(\Nubersoft\Settings\Page\Controller $Page) use ($system_settings){
				$val	=	(!empty($system_settings['template']['option_attribute']))? $system_settings['template']['option_attribute'] : false;
				
				return array_map(function($v) use ($val) {
					if($v['value'] == $val)
						$v['selected']	=	true;
					
					return $v;
				},$Page->getTemplateList());
			}),
		'class' => 'nbr'
	],
	[
		'label' => 'Site Timezone ('.date('Y-m-d H:i:s').')',
		'name' => 'setting[timezone]',
		'type' => 'select',
		'options' => array_map(function($v) use ($timezone) {
			return [
				'name' => str_replace(['/', '_'],[' – ', ' '],$v),
				'value' => $v,
				'selected' => ($timezone['option_attribute'] == $v)
			];
		},\DateTimeZone::listIdentifiers()),
		'class' => 'nbr'
	],
	[
		'label' => 'Server Rewriting',
		'name' => 'setting[htaccess]',
		'type' => 'textarea',
		'value' => (!empty($htaccess['option_attribute']))? $htaccess['option_attribute'] : $this->enc(file_get_contents(NBR_ROOT_DIR.DS.'.htaccess')),
		'class' => 'nbr tabber code required',
		'other' => ['required="required"'],
		'style' => 'height: 300px;'
	],
	[
		'label' => 'Show File Inclusions',
		'name' => 'setting[fileid]',
		'type' => 'select',
		'options' => array_map(function($v) use ($fileid) {
			if(!empty($fileid['option_attribute']) && $fileid['option_attribute'] == $v['value'])
				$v['selected']	=	true;
				
			return $v;
		}, [
			[
				'name' => 'Off',
				'value' => 'off'
			],
			[
				'name' => 'On',
				'value' => 'on'
			]
		]),
		'class' => 'nbr required',
		'other' => ['required="required"']
	],
	[
		'label' => 'Production Mode',
		'name' => 'setting[devmode]',
		'type' => 'select',
		'options' => array_map(function($v) use ($devmode) {
			if(!empty($devmode['option_attribute']) && $devmode['option_attribute'] == $v['value'])
				$v['selected']	=	true;
				
			return $v;
		}, [
			[
				'name' => 'Production Mode',
				'value' => 'live'
			],
			[
				'name' => 'Developer Mode',
				'value' => 'dev'
			]
		]),
		'class' => 'nbr required',
		'other' => ['required="required"']
	]
];
?>
<div class="section-head nTrigger" data-instructions='{"FX":{"fx":["slideUp","accordian"],"event":["click","click"],"acton":[".section","next::slideToggle"],"fxspeed":["fast","fast"]}}'>General Settings</div>
<div class="section hide">
	<p>Change the settings of your entire site.</p>
	<?php
	$Form	=	@$this->nForm();
	echo $Form->open(["action" => "?loadpage=load_settings_page&subaction=global"]);
	echo $Form->fullhide(['name' => 'token[nProcessor]', 'value' => '']);
	echo $Form->fullhide(['name' => 'action', 'value' => 'save_settings']);
	echo $Form->fullhide(['name' => 'category_id', 'value' => 'site']);
	echo $Form->fullhide(['name' => 'option_group_name', 'value' => 'system']);
	?>

		<?php
		foreach($defaults as $row):
			$type	=	$row['type'];
			unset($row['type']);
		?>

		<div class="col-count-<?php echo (in_array($type,['textarea']))? '2' : '4' ?> lrg-1">
			<div class="col-1">
				<?php echo $Form->{$type}($row) ?>
			</div>
		</div>

		<?php endforeach ?>

		<div class="col-count-4 lrg-2 med-1">
			<div class="col-1">
				<?php echo $Form->submit(['value' => 'Save', 'class' => 'medi-btn dark']) ?>
			</div>
		</div>

	<?php echo $Form->close() ?>
</div>

<div class="section-head nTrigger" data-instructions='{"FX":{"fx":["slideUp","accordian"],"event":["click","click"],"acton":[".section","next::slideToggle"],"fxspeed":["fast","fast"]}}'>Admin Setup</div>
<div class="section hide">
	<h3>Backend Name</h3>
	<p>Change your back office path name to help keep it masked from unwanted probing.</p>
	<?php
	extract(@$this->nRouter()->getPage(1, 'is_admin'));
	echo $Form->open(['enctype' => 'multipart/form-data', "action" => "?loadpage=load_settings_page&subaction=global"]);
	echo $Form->fullhide(['name' => 'token[nProcessor]', 'value' => '']);
	echo $Form->fullhide(['name' => 'action', 'value' => 'update_admin_url']);
	echo $Form->fullhide(['name' => 'ID', 'value' => $ID]);
	?>

		<div class="col-count-4 lrg-2 med-1">
			<div class="">
				<?php echo $Form->text(['label' => 'Admin Title', 'name' => 'menu_name', 'value' => $menu_name, 'class' => 'nbr', 'other' => ['required="required"']]) ?>
			</div>
			<div class="col-1">
				<?php echo $Form->text(['label' => 'Slug / Url', 'name' => 'full_path', 'value' => $full_path, 'class' => 'nbr', 'other' => ['required="required"']]) ?>
			</div>
			<div class="col-1">
				<?php echo $Form->select([
				'label' => 'Template',
				'name' => 'template',
				'type' => 'select',
				'options' =>
					$this->createContainer(function(\Nubersoft\Settings\Page\Controller $Page) use ($template) {
						
						return array_map(function($v) use ($template) {
							if($v['value'] == $template)
								$v['selected']	=	true;

							return $v;
						},$Page->getTemplateList());
					}),
				'class' => 'nbr'
			]) ?>
			</div>
			<div class="col-1">
				<?php echo $Form->submit(['value' => 'Save', 'class' => 'medi-btn dark']) ?>
			</div>
		</div>

	<?php echo $Form->close() ?>
</div>

<div class="section-head nTrigger" data-instructions='{"FX":{"fx":["slideUp","accordian"],"event":["click","click"],"acton":[".section","next::slideToggle"],"fxspeed":["fast","fast"]}}'>Site Logo</div>
<div class="section hide show">
	<p>Update your web site logo (jpeg, jpg, gif, png). This logo will be available in the front end as well as the back end.</p>
	<?php
	echo $Form->open(['enctype' => 'multipart/form-data', "action" => "?loadpage=load_settings_page&subaction=global"]);
	echo $Form->fullhide(['name' => 'token[nProcessor]', 'value' => '']);
	echo $Form->fullhide(['name' => 'action', 'value' => 'save_settings_sitelogo']);
	echo $Form->fullhide(['name' => 'category_id', 'value' => 'site']);
	echo $Form->fullhide(['name' => 'option_group_name', 'value' => 'system']);
	?>
		<div class="col-count-3 lrg-1">
			<div class="col-1">
				<?php
				$header_company_logo_toggle	=	(isset($header_company_logo_toggle))? $header_company_logo_toggle : false;

				echo $Form->select([
					'label' => 'Site Logo On?',
					'name' => 'setting[header_company_logo_toggle]',
					'type' => 'select',
					'options' => array_map(function($v) use ($header_company_logo_toggle) {
						return [
							'name' => $v['name'],
							'value' => $v['value'],
							'selected' => (!empty($header_company_logo_toggle['option_attribute']) && $header_company_logo_toggle['option_attribute'] == $v['value'])
						];
					},[
						["name" => "Off","value" => "off"],
						["name" => "On","value" => "on"]
					]),
					'class' => 'nbr'
				]) ?>
			</div>
		</div>
		<div class="col-count-3 lrg-1">
			<?php if(!empty($header_company_logo['option_attribute'])): ?>
			<div class="col-1" style="background-image: url('/core/template/default/media/images/ui/transparent-grid.gif'); background-repeat: repeat; background-size: 8px; padding: 2em; margin-top: 1em;">
				<img src="<?php echo $header_company_logo['option_attribute'] ?>" />
			</div>
			<div class="span-3 push-col-1 large">
				<p>
				File Size: <?php echo @$this->Conversion_Data()->getByteSize(filesize(NBR_ROOT_DIR.DS.$header_company_logo['option_attribute']),[
					'from' => 'b',
					'to' => 'kb',
					'round' => 2
	]) ?>KB</p>
			</div>
			<?php endif ?>
			<div class="col-1">
				<?php echo $Form->file(['name' => 'file', 'class' => 'nbr']) ?>
			</div>
		</div>
		<div class="col-count-4 lrg-2 med-1">
			<div class="col-1">
				<?php echo $Form->submit(['value' => 'Save', 'class' => 'medi-btn dark']) ?>
			</div>
		</div>

	<?php echo $Form->close() ?>
</div>

<script>
	// Calls the form token
	fetchAllTokens($);
</script>
<?php // echo printpre($this->query("select * from system_settings")->getResults()) ?>