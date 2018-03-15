<?php
/*
**	@description	Using the naming convention: admintools_pagelayout_{tablename} You change
**					the look of how a table is displayed
*/
?>
<div class="col-count-3 offset" style="text-align: left;">
	<div class="col-2 col-count-4">
		<div class="span-4">
			<h1>Components</h1>
			<p>Select the component type you would like to view</p>
		</div>
		<div class="span-2">
			<label>Component Types
				<select name="types" class="nbr">
					<option value="">Select</option>
					<?php foreach($this->nQuery()->query("SELECT DISTINCT category_id FROM components WHERE category_id != ''")->getResults() as $type): ?>
					<option value="<?php echo $type['category_id'] ?>"<?php if(!is_numeric($this->getGet('ID')) && ($this->getGet('view')==$type['category_id'])) echo ' selected' ?>><?php echo $this->colToTitle($type['category_id']) ?></option>
					<?php endforeach ?>
				</select>
			</label>
		</div>
		<div class="col-1 span-4">
			<?php if($this->getGet('action')=='edit_component_single' && is_numeric($this->getGet('ID'))): ?>
			<div class="span-4">
				<h2>Component Edit</h2>
				<?php
				$component	=	$this->getComponent(['ID'=>$this->getGet('ID')],true,false,'*,CONCAT(`file_path`,`file_name`) as image')->toArray();
				$nForm		=	$this->getHelper('nForm');
				$settings	=	$this->getFormElementsFromTable('components');
				?>

				<?php echo $nForm->open(['action'=>'?'.http_build_query($this->toArray($this->getGet())),'enctype'=>'multipart/form-data']) ?>
					<input type="hidden" name="action" value="nbr_edit_table_row" />
					<?php
					if(!empty($component)):
						foreach($component as $column => $value):
							$input['name']	=	$column;
							$input['value']	=	$value;
							$input['type']	=	(!empty($settings[$column]['column_type']))? $settings[$column]['column_type'] : 'text';
							$input['size']	=	(!empty($settings[$column]['size']))? $settings[$column]['size'] : false;
							$input['class']	=	'nbr';
							switch($input['type']){
								case('textarea'):
									$input['class']	.=	' tabber code';
									break;
								case('file'):
									break;
								default:
									$input['class']	.= ' code';
							}

							if(!empty($settings[$column]['options']))
								$input['options']	=	$settings[$column]['options'];
						?>
					<div class="col-count-5 side-by-side rule below">
						<?php if($column == 'image'): ?>
							<?php if(!empty($input['value'])): ?>
						<div><a href="<?php echo $this->localeUrl($input['value']) ?>" target="_blank"><img src="<?php echo $input['value'] ?>" style="display: block; width: 100%;" /></a></div>
						<div class="span-4 col-count-5">
							<div class="span-5">
								<label>URL</label>
								<input type="text" value="<?php echo $this->localeUrl($input['value']) ?>" class="nbr code" onClick="this.select();document.execCommand('Copy')" />
							</div>
							<div class="span-5">
								<label>Absolute</label>
								<input type="text" value="<?php echo NBR_ROOT_DIR.$input['value'] ?>" class="nbr code" onClick="this.select();document.execCommand('Copy')" />
							</div>
							<div>
								<a href="#" class="nTrigger nbr button" data-instructions='{"action":"nbr_open_modal","data":{"deliver":{"ID":<?php echo $this->getGet('ID') ?>,"table":"components","action":"nbr_component_edit_image","close_button":"CANCEL","jumppage":"\/AdminTools\/?requestTable=components"}}}'>EDIT IMAGE</a>
							</div>
						</div>
								<?php endif ?>
						<?php else: ?>
						<div><?php echo $this->colToTitle($column) ?></div>
						<div class="span-4"><?php echo strip_tags($nForm->multiForm($input),'<label><input><textarea><select><option>') ?></div>
						<?php endif ?>
					</div>
						<?php endforeach ?>
					<?php endif ?>
					<div class="col-count-6 lrg-4 med-3 sml-1">
						<div class="last-col">
							<?php echo $nForm->submit(['value'=>'UPDATE','class'=>'nbr button green']) ?>
						</div>
					</div>
				<?php echo $nForm->close() ?>
			</div>
			<?php elseif($this->getGet('view')): ?>
				<?php
				$usergroup	=	$this->getSession('usergroup');
				$type		=	$this->getGet('view');
				$Search		=	$this->getPlugin('nPlugins\Nubersoft\SearchEngine');
				$query		=	$Search->fetch(['columns'=>$this->getColumns('components')],
					function() use ($type,$usergroup){
						$args		=	func_get_args();
						$nApp		=	$args[0];
						$thisObj	=	$args[1];
						
						$search		=	(!empty($nApp->getGet('search')))? $nApp->getGet('search') : false;
						if(!empty($search)){
							foreach($thisObj->columns_allowed as $col){
								$finder[]			=	"{$col} LIKE :{$col}";
								$binder[":{$col}"]	=	"%".$nApp->getGet('search')."%";
							}
							
							$query		=	$nApp->nQuery()->query("SELECT COUNT(*) as count FROM components WHERE ".implode(" OR ",$finder),$binder)->getResults(1);
						}
						else
							$query		=	$nApp->nQuery()->query("SELECT COUNT(*) as count FROM components WHERE category_id = :0",[$type])->getResults(1);
						
						return $query['count'];
					},
					function($req,$thisObj,$page,$limit,$orderB,$orderH) use ($type,$usergroup){
						
						$search		=	(!empty($thisObj->getGet('search')))? $thisObj->getGet('search') : false;
						
						if(!empty($search)){
							foreach($thisObj->columns_allowed as $col){
								$finder[]			=	"{$col} LIKE :{$col}";
								$binder[":{$col}"]	=	"%".$thisObj->getGet('search')."%";
							}
							
							$query		=	$thisObj->nQuery()->query("SELECT * FROM components WHERE ".implode(" OR ",$finder),$binder)->getResults();
						}
						else
							$query		=	$thisObj->nQuery()->query("SELECT * FROM components WHERE category_id = :0 ORDER BY `{$orderB}` {$orderH} LIMIT ".$page.", ".$limit,[$type])->getResults();
						
						return $query;
					});
			
				$data				=	$query->getStats()['data'];
				$data['results']	=	(!empty($data['results']))? $data['results'] : [];
				$GET				=	$this->toArray($this->getGet());
				$GET['current']		=	$data['current'];
			
			?>
			<div class="col-count-8">
				<div>
					<?php
					foreach($data['max_range'] as $num):
						$max	=	$GET;
						if(isset($max['max']))
							unset($max['max']);
					?>
					<a href="<?php echo (isset($GET['max']) && $GET['max'] == $num)? '#content' : '?'.http_build_query($max).'&max='.$num ?>"><?php echo $num ?></a>
					<?php endforeach ?>
				</div>
				<div class="col-count-12">
					<?php if($data['current'] > 1):
						$first	=	$GET;
						if(isset($first['current']))
							unset($first['current']) ?>
					<a href="<?php echo '?'.http_build_query($first).'&current=1' ?>"><?php echo $this->safe()->encodeSingle('<<') ?></a>
					<?php endif ?>
					
					<?php if($data['previous'] != $data['current']): ?>
					<a href="<?php echo '?'.http_build_query($GET).'&current='.$data['previous'] ?>"><?php echo $this->safe()->encodeSingle('<') ?></a>
					<?php endif ?>
					
					<?php
					foreach($data['range'] as $curr):
						$current	=	$GET;
						if(isset($current['current']))
							unset($current['current']);

					   $currentSet	=	(isset($GET['current']) && $GET['current'] == $curr);
					   
					   if(!$currentSet):
					?>
					<a href="<?php echo (isset($GET['current']) && $GET['current'] == $curr)? '#content' : '?'.http_build_query($current).'&current='.$curr ?>"><?php echo $curr ?></a>
						<?php else: ?>
					<?php echo $curr ?>
						<?php endif ?>
					<?php endforeach ?>
					
					<?php
					if(!empty($data['next']) && $data['next'] != $data['current']):
						unset($GET['current']);
					?>
					<a href="<?php echo '?'.http_build_query($GET).'&current='.$data['next'] ?>"><?php echo $this->safe()->encodeSingle('>') ?></a>
					<?php endif ?>
					
					<?php
					if(($data['last'] > 1) && ($data['last'] != $data['current'])):
						unset($GET['current']);
					?>
					<a href="<?php echo '?'.http_build_query($GET).'&current='.$data['last'] ?>"><?php echo $this->safe()->encodeSingle('>>') ?></a>
					<?php endif ?>
				</div>
				<div class="span-3">
					<form action="" method="get">
						<?php
						foreach($GET as $key => $value):
							if($key == 'search')
								continue; 
						?>
						<input type="hidden" name="<?php echo $key ?>" value="<?php echo $value ?>" />
						<?php endforeach ?>
						<div class="col-count-4">
							<input type="text" name="search" placeholder="Search word" autocomplete="off" value="<?php echo $this->getGet('search')?>" class="nbr span-3" />
							<input type="submit" value="SEARCH" class="nbr button green" />
						</div>
					</form>
				</div>
			</div>
			<div class="col-count-7 standard-table">
				<div class="header-row col-1">ID</div>
				<div class="header-row">Title</div>
				<div class="header-row span-2">Content</div>
				<div class="header-row">Type</div>
				<div class="header-row">Category Id</div>
				<div class="header-row">Order</div>
				<?php foreach($data['results'] as $row): ?>
				<div class="col-count-7 span-7 table-row" onClick="window.location='?action=edit_component_single&ID=<?php echo $row['ID'] ?>&<?php echo http_build_query($GET) ?>'">
					<div class="col-1"><?php echo $row['ID'] ?></div>
					<div><?php echo (isset($row['title']))? $row['title'] : 'Untitled' ?></div>
					<div class="span-2"><?php echo wordwrap(substr($row['content'],0,200),40,PHP_EOL,true); if(strlen($row['content']) > 200) echo '...' ?></div>
					<div><?php echo $row['component_type'] ?></div>
					<div><?php echo $row['category_id'] ?></div>
					<div><?php echo $row['page_order'] ?></div>
				</div>
				<?php endforeach ?>
			</div>
			<?php endif ?>
		</div>
	</div>
</div>
<script>
	$(function(){
		$('select[name=types]').on('change',function(){
			window.location	=	'?requestTable=components&view='+$(this).val();
		});
	});
</script>
<?php if($this->getHelper('UserEngine')->isAllowed(1)): ?>
<div style="max-width: 1200px; margin: 0 auto; overflow: auto;">
	<?php echo $this->useTemplatePlugin('admintools_sql_master') ?>
</div>
<?php endif ?>