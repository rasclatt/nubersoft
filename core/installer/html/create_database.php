<?php
$data	=	[
	[
		'label' => 'Database Host',
		'name' => 'DB_HOST',
		'other' => [
			'required="required"',
			'size="60"'
		],
		'class' => 'nbr',
		'value' => (defined('DB_HOST'))? base64_decode(DB_HOST) : 'localhost'
	],
	[
		'label' => 'Database Name',
		'name' => 'DB_NAME',
		'other' => [
			'required="required"',
			'size="60"'
		],
		'class' => 'nbr',
		'value' => (defined('DB_NAME'))? base64_decode(DB_NAME) : ''
	],
	[
		'label' => 'Database Username',
		'name' => 'DB_USER',
		'other' => [
			'required="required"',
			'size="60"'
		],
		'class' => 'nbr',
		'value' => (defined('DB_USER'))? base64_decode(DB_USER) : 'root'
	],
	[
		'label' => 'Database Password',
		'name' => 'DB_PASS',
		'other' => [
			'required="required"',
			'size="60"'
		],
		'class' => 'nbr',
		'value' => (defined('DB_PASS'))? base64_decode(DB_PASS) : ''
	],
	[
		'label' => 'Database Character Set',
		'name' => 'DB_CHARSET',
		'other' => [
			'required="required"',
			'size="60"'
		],
		'class' => 'nbr',
		'value' => (defined('DB_CHARSET'))? DB_CHARSET : 'utf8'
	]
];

$err	=	$this->getDataNode('installer_error');
if(!empty($err)): ?>
<div class="nbr_error"><?php echo $err ?></div>
<?php endif ?>
<h1>Create your database connection.</h1>
<?php echo $Form->open() ?>
	<?php echo $Form->fullhide(['name'=>'action', 'value' => 'save_dbcreds']) ?>
<table border="0">
	<?php foreach($data as $field): ?>
	<tr>
		<td><?php echo $Form->text($field) ?></td>
	</tr>
	<?php endforeach ?>
	<tr>
		<td class="align-right"><?php echo $Form->submit(['value' => 'Save', 'class' => 'medi-btn dark']) ?></td>
	</td>
</table>
<?php echo $Form->close();