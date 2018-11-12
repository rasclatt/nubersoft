
<h1>Create your registry file.</h1>
<p>This file is a hardcopy of settings to help your application run. If you don't know what it does, you may want to just leave it as is.</p>
<?php echo $Form->open() ?>
	<?php echo $Form->fullhide(['name'=>'action', 'value' => 'save_registry_doc']) ?>
<table border="0">
	<?php foreach($data as $key => $value): ?>
	<tr>
		<td><?php echo $key ?></td>
		<td><?php echo $Form->text(['name' => $key, 'value' => $value, 'class' => 'nbr required', 'other'=>['size="'.(strlen($value)*1.1).'"', 'required="required"']]) ?></td>
	</tr>
	<?php endforeach ?>
	<tr>
		<td colspan="2" class="align-right"><?php echo $Form->submit(['value' => 'Save', 'class' => 'medi-btn dark']) ?></td>
	</td>
</table>
<?php echo $Form->close();