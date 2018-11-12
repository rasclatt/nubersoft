<?php
$Form		=	$this->getHelper('nForm');
$Token		=	$this->getHelper('nToken');
$compData	=	$this->getPluginContent('component_content');
$ID			=	(!empty($compData['ID']))? $compData['ID'] : 'add';
$token		=	'component_'.$ID;
$ref_page	=	$compData['ref_page'];
?>
<div class="component-container">
	<div class="component-shade col-count-4">
		<div><a href="#editorid-<?php echo $compData['ID'] ?>" class="nTrigger nodrag close-btn" data-instructions='{"DOM":{"html":[" "], "sendto":["#editorid-<?php echo $compData['ID'] ?>"],"event":["click"]}}'>&times;</a></div>
		<div class="last-col">ID: <?php echo $compData['ID'] ?></div>
	</div>
	<div class="component-btns nodrag no-drag">
		<div>
			<?php echo $this->setPluginContent('add_component', array_merge($compData,['token' => $token]))->getPlugin('component', DS.'add.php') ?>
		</div>
		<div>
			<?php echo $Form->open() ?>
				<?php echo strip_tags($Form->fullhide(['name' => 'action', 'value' => 'edit_component']),'<input>') ?>
				<?php echo strip_tags($Form->fullhide(['name' => 'subaction', 'value' => 'duplicate']),'<input>') ?>
				<?php echo strip_tags($Form->fullhide(['name' => 'token[nProcessor]', 'value' => $Token->setToken($token)->getToken($token, false)]),'<input>') ?>
				<?php echo strip_tags($Form->fullhide(['name' => 'ref_page', 'value' => $this->getPage('unique_id')]),'<input>') ?>
				<?php echo strip_tags($Form->fullhide(['name' => 'parent_dup', 'value' => $compData['ID']]),'<input>') ?>
				<?php echo strip_tags($Form->submit(['value' => 'DUPLICATE', 'class' => 'mini-btn dark no-margin']),'<input>') ?>
			<?php echo $Form->close() ?>
		</div>
		<div>Store</div>
	</div>
	<div class="component-wrap">
		<?php
		$inputs	=	$this->getHelper('Settings\Controller')->getFormAttr('components');
		echo $Form->open(['id'=>'user-editor','enctype'=>'multipart/form-data']);
		echo $Form->fullhide(['name' => 'action', 'value' => 'edit_component']);
		echo $Form->fullhide(['name' => 'token[nProcessor]', 'value' => $this->getHelper('nToken')->setToken($token)->getToken($token, false)]);
		?>

			<div class="table-row nodrag">
		<?php
		foreach($compData as $field => $value):
			if(in_array($field, ['ID','unique_id'])) {
				$inputs[$field]['column_type']	=	'fullhide';
			}
			# Ignore this one
			if($field == 'ref_anchor') {
				continue;
			}
			
			if($field == 'file_size') {
				echo '<div style="margin-top: 1em; font-size: 0.85em; color: blue;">'.round($this->getHelper('Conversion\Data')->getByteSize($value, ['from' => 'b', 'to' => 'mb']),2).'MB<br /></div>';
				continue;
			}
			$keyset	=	(!empty($inputs[$field]['column_type']));
			//if($field == 'usergroup' && is_string($value))
			//	$value	=	constant($value); ?>
				<div<?php if($keyset && $inputs[$field]['column_type'] == 'fullhide'): ?> style="display: none;"<?php endif ?>>
				<?php
				if(empty($value))
					$value	=	$this->getPost($field);

				$label	=	$this->colToTitle($field);

				if($field == 'category_id') {
					$value	=	(!empty($value))? $value : 'nbr_layout';
					if(!isset($inputs[$field]))
						$inputs[$field]	=	[];

					$inputs[$field]['column_type'] = 'fullhide';
				}

				if($field == 'page_order') {

					for($i = 1; $i <= 50; $i++) {
						$counter[$i]['name']	=
						$counter[$i]['value']	=	$i;
					}

					echo $Form->select(['label' => 'Component Order', 'name'=> $field, 'options' => 
						array_map(function($v) use ($value) {
							if($v['value'] == $value)
								$v['selected']	=	true;
							return $v;
						}, $counter)
					, 'class' => 'nbr']);
				}
				elseif($field == 'parent_id') {

					$rows	=	$this->query("SELECT `title`, `content`, `unique_id` FROM components WHERE `component_type` = 'container' OR `component_type` = 'div' AND `ref_page` = ? AND `ID` != ?",[$ref_page, $compData['ID']])->getResults();

					if(empty($rows)) {
						continue;
					}
					
					$rows	=	array_merge([['title'=>'Select', 'unique_id' => '']],$rows);
					$rows	=	array_map(function($v) use ($value) {
						$v	= [
							'name' => $v['title'],
							'value' => $v['unique_id']
						];

						if($v['value'] == $value)
							$v['selected']	=	true;

						return $v;
					},$rows);

					echo $Form->select(['label' => 'Parent Component', 'name'=> $field, 'options' => 
						array_map(function($v) use ($value) {
							if($v['value'] == $value)
								$v['selected']	=	true;
							return $v;
						}, $rows)
					, 'class' => 'nbr']);
				}
				elseif(in_array($field, ['file_path','timestamp'])) {
					$meth	=	(!empty($value))? 'text' : 'fullhide';
					echo $Form->{$meth}(['label' => $label, 'name'=> $field, 'value' => $value, 'class' => 'nbr', 'other' =>['readonly="readonly"']]);
				}
				elseif($field == 'file_name' && !empty($value)) {
					echo $Form->text(['label' => $label, 'name'=> $field, 'value' => $value, 'class' => 'nbr']);
				}
				elseif(isset($inputs[$field])) {

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
							if(!empty($compData['file_path'])) {
								
								$imgTypes	=	[
									'gif',
									'jpeg',
									'jpg',
									'png',
									'bmp'
								];
								
								$isImg	=	(in_array(strtolower(pathinfo($compData['file_name'], PATHINFO_EXTENSION)), $imgTypes));
								
		ini_set('display_errors',1);
		error_reporting(E_ALL);
								try {
									$imageThumb	=	($isImg)? $this->getHelper('System\Controller')->getThumbnail($compData['file_path'], $compData['file_name']) : false;
								}
								catch (\Exception $e) {
									$imageThumb	=	$e->getMessage();
								}
								catch (\Nubersoft\HttpException $e) {
									$imageThumb	=	$e->getMessage();
								}

								echo $this->setPluginContent('image_tools', [
									'thumb' => $imageThumb,
									'table'=> 'components',
									'ID' => $compData['ID'],
									'file_path' => $compData['file_path'],
									'file_name' => $compData['file_name']
								])->getPlugin('component', DS.'image_tools.php');
							}
							echo $Form->file(['name'=> $field, 'value' =>'', 'class' => 'nbr']);
							break;
						case('select'):
							echo $Form->select(['label' => $label, 'name'=> $field, 'options' => 
								array_map(function($v) use ($field, $value) {

									if($field == 'usergroup')
										$v['value']	=	(!empty($v['value']) && !is_numeric($v['value']))? constant($v['value']) : $v['value'];

									if($v['value'] == $value)
										$v['selected']	=	true;

									$v['name']	=	ucfirst($v['name']);
									return $v;
								}, $inputs[$field]['options'])
							, 'class' => 'nbr']);
							break;
						case('textarea'):
							echo '<a class="expander mini-btn dark" href="#" data-acton=".component-container">EXPAND</a>';
							echo $Form->textarea(['name'=> $field, 'value' => $value, 'class' => 'nbr code component tabber']);
							break;
						default:
							echo $Form->text(['label' => $label, 'name'=> $field, 'value' => $value, 'class' => 'nbr']);

					}
				}
				else{
					if($field == 'ref_page') {
						echo $Form->select(['label' => 'Web page to display on', 'name'=> $field, 'options' => 
							array_map(function($v) use ($value) {
								$a['name']	=	$v['menu_name'].' ('.(($v['page_live'] == 'off')? 'Off' : 'On').')';
								$a['value']	=	$v['unique_id'];

								if($a['value'] == $value)
									$a['selected']	=	true;
								return $a;
							}, $this->query("SELECT `unique_id`, `menu_name`, `page_live` FROM main_menus")->getResults())
						, 'class' => 'nbr']);
					}
					else {
						echo $Form->text(['label' => $label, 'name'=> $field, 'value' => $value, 'class' => 'nbr']);
					}
				}
				?>

				</div>

		<?php endforeach ?>
			<?php if(empty($this->getRequest('create'))): ?>
			<div class="col-1"><?php echo $Form->checkbox(['label' => 'Delete?','name'=>'delete', 'value'=>'on', 'class' => 'nbr']) ?></div>
			<?php endif ?>
			<div class="last-col"><?php echo $Form->submit(['name'=>'','value'=>'save', 'class' => 'nbr button green']) ?></div>

		<?php echo $Form->close() ?>
		</div>
	</div>
</div>
<div class="component-shade">&nbsp;</div>