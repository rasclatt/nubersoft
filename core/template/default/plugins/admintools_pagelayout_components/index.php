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
		<div>
			<label>Component Types
				<select name="types" class="nbr">
					<option value="">Select</option>
					<?php foreach($this->nQuery()->query("SELECT DISTINCT ref_spot FROM components WHERE ref_spot != ''")->getResults() as $type): ?>
					<option value="<?php echo $type['ref_spot'] ?>"<?php if($this->getGet('view')==$type['ref_spot']) echo ' selected' ?>><?php echo $this->colToTitle($type['ref_spot']) ?></option>
					<?php endforeach ?>
				</select>
			</label>
		</div>
		<div class="span-3">
			<?php if($this->getGet('view')): ?>
				<?php
				$usergroup	=	$this->getSession('usergroup');
				$type		=	$this->getGet('view');
				$Search		=	$this->getPlugin('nPlugins\Nubersoft\SearchEngine');
				$query		=	$Search->fetch(['columns'=>['ref_spot','content']],
					function() use ($type,$usergroup){
						$args		=	func_get_args();
						$nApp		=	$args[0];
						$thisObj	=	$args[1];
						$query	=	$nApp->nQuery()->query("SELECT COUNT(*) as count FROM components WHERE ref_spot=:0 AND `login_permission` <= :1",[$type,$usergroup])->getResults(1);
						return $query['count'];
					},
					function($req,$thisObj,$page,$limit,$orderB,$orderH) use ($type,$usergroup){
						$query		=	$thisObj->nQuery()->query("SELECT * FROM components WHERE ref_spot=:0 AND `login_permission` <= :1 ORDER BY `{$orderB}` {$orderH} LIMIT ".$page.", ".$limit,[$type,$usergroup])->getResults();
						return $query;
					});
			
				$data	=	$query->getStats()['data'];
			
				echo printpre($data);
			
				foreach($data['results'] as $row): ?>
		
			<?php echo printpre($row,['backtrace'=>false]) ?>
			
				<?php endforeach ?>
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