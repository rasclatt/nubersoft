<?php
$user	=	$this->getDataNode("table_data");
$this->removeNode("table_data");
$inputs	=	$this->getHelper('Settings\Controller')->getFormAttr($this->getRequest('table'));
$Form	=	$this->getHelper('nForm');
echo $Form->open(['id'=>'user-editor','enctype'=>'multipart/form-data']);
echo $Form->fullhide(['name' => 'action', 'value' => 'edit_table_rows_details']);
echo $Form->fullhide(['name' => 'token[nProcessor]', 'value' => '']);
?>

	<div class="table-row col-count-3 gapped lrg-2 med-1">
<?php
foreach($user as $field => $value):
	if(in_array($field, ['ID','unique_id'])) {
		$inputs[$field]['column_type']	=	'fullhide';
	}
		
	if($field == 'usergroup' && !is_numeric($value) && !empty($value))
		$value	=	constant($value); 
		
	$keyset	=	(!empty($inputs[$field]['column_type']));
	//if($field == 'usergroup' && is_string($value))
	//	$value	=	constant($value); ?>
		<div<?php if($keyset && $inputs[$field]['column_type'] == 'fullhide'): ?> style="display: none;"<?php endif ?>>
		<?php
		if(empty($value))
			$value	=	$this->getPost($field);
		
		$label	=	$this->colToTitle($field);
		if(isset($inputs[$field])) {
			switch($inputs[$field]['column_type']) {
				case('fullhide'):
					echo $Form->fullhide([ 'name'=> $field, 'value' => $value]);
					break;
				case('hidden'):
					echo $Form->hidden(['name'=> $field, 'value' => $value]);
					break;
				case('text'):
					echo $Form->text(['label' => $label, 'name'=> $field, 'value' => $value, 'class' => 'nbr']);
					break;
				case('password'):
					echo $Form->password(['label' => $label, 'name'=> $field, 'value' => '', 'class' => 'nbr']);
					break;
				case('file'):
					echo $Form->file(['name'=> $field, 'value' =>'', 'class' => 'nbr']);
					break;
				case('textarea'):
					echo $Form->textarea(['label' => $label, 'name'=> $field, 'value' =>$value, 'class' => 'nbr tabber code editable']);
					break;
				case('select'):
					echo $Form->select(['label' => $label, 'name'=> $field, 'options' => 
						array_map(function($v) use ($field, $value) {
							
							if($field == 'usergroup' && !is_numeric($v['value']) && !empty($v['value']))
								$v['value']	=	constant($v['value']);
							
							if($v['value'] == $value)
								$v['selected']	=	true;
							
							return $v;
						}, $inputs[$field]['options'])
					, 'class' => 'nbr']);
					break;
				default:
					echo $Form->text(['label' => $label, 'name'=> $field, 'value' => $value, 'class' => 'nbr']);

			}
		}
		else
			echo $Form->text(['label' => $label, 'name'=> $field, 'value' => $value, 'class' => 'nbr']);
		?>

		</div>

<?php endforeach ?>
	<?php if(empty($this->getRequest('create'))): ?>
	<div class="col-1"><?php echo $Form->checkbox(['label' => 'Delete?','name'=>'delete', 'value'=>'on', 'class' => 'nbr']) ?></div>
	<?php endif ?>
	<div class="last-col"><?php echo $Form->submit(['name'=>'','value'=>'save', 'class' => 'nbr button green token_button', 'disabled'=>'disabled', 'other'=> ['data-token="nProcessor"']]) ?></div>
	</div>
<?php echo $Form->close() ?>