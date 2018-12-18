<?php
$Settings	=	extract(\Nubersoft\ArrayWorks::organizeByKey($this->getDataNode('settings')['system'],'category_id'));
if(empty($footer_html))
	$footer_html	=	[];
if(empty($footer_html_toggle))
	$footer_html_toggle	=	[];

$defaults	=	[
	[
		'label' => 'Use Footer HTML?',
		"name" => "setting[footer_html_toggle]",
		"type" => "select",
		"options" => array_map(function($v) use ($footer_html_toggle) {
			if(!empty($footer_html_toggle) && ($v['value'] == $footer_html_toggle['option_attribute']))
				$v['selected']	=	true;
			
			return $v;
		}, [
			["name" => "Off","value" => "off"],
			["name" => "On","value" => "on"]
		]),
		'class' => 'nbr'
	],
	[
		'name' => 'setting[footer_html]',
		'type' => 'textarea',
		'value' => (!empty($footer_html['option_attribute']))? $footer_html['option_attribute'] : '&amp;copy; ~DATE::[Y]~.',
		'class' => 'nbr tabber code',
		'style' => 'height: 300px;'
	]
];
?>
<h2>Footer Settings</h2>
<p>Change footer settings for your site.</p>
<?php
$Form	=	@$this->nForm();
echo $Form->open(["action" => "?loadpage=load_settings_page&subaction=footer"]);
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
	
	<div class="col-count-4">
		<div class="col-1">
			<?php echo $Form->submit(['value' => 'Save', 'class' => 'medi-btn dark']) ?>
		</div>
	</div>

<?php echo $Form->close() ?>
<script>
	// Calls the form token
	fetchAllTokens($);
</script>