<?php

$Form	=	@$this->nForm();
?>
<?php echo $this->getPlugin('table_view', DS.'interface.php') ?>
<h3>Table View</h3>

<?php
$Pagination	=	$this->getHelper('SearchEngine\View')->fetch([
	'columns' => $this->getDataNode("table_data"),
	'sort' => 'DESC'
	],
	function($nQuery, $Pagination, $REQ){

		if(!empty($REQ['search'])) {
			$bind		=	array_fill(0,count($Pagination->getColumnsAllowed()),'%'.$Pagination->dec(urldecode($REQ['search'])).'%');
			
			foreach($Pagination->getColumnsAllowed('`') as $col) {
				$where[]	=	$col." LIKE ?";
			}
			
			$where	=	implode(' ',array_merge([" WHERE "],[implode(" OR ", $where)]));
		}
		else
			$where	=	'';
		
		$sql	=	"SELECT
						COUNT(*) as count
					FROM
						".$Pagination->getRequest('table')."
					{$where}";
		
		return $Pagination->query($sql,(!empty($bind)? $bind : null))->getResults(1)['count'];
	},
	function($REQ, $Pagination, $page, $limit, $orderB, $orderH){
		if(!empty($REQ['search'])) {
			$bind		=	array_fill(0,count($Pagination->getColumnsAllowed()),'%'.$Pagination->dec(urldecode($REQ['search'])).'%');
			
			foreach($Pagination->getColumnsAllowed('`') as $col) {
				$where[]	=	$col." LIKE ?";
			}
			
			$where	=	implode(' ',array_merge([" WHERE "],[implode(" OR ", $where)]));
		}
		else
			$where	=	'';
		
		$sql	=	"SELECT
						*
					FROM
						".$Pagination->getRequest('table')."
					{$where}
					ORDER BY
						".$orderB." ".$orderH."
					LIMIT
						{$page}, {$limit}";
		
		$result	=	$Pagination->query($sql,(!empty($bind)? $bind : null))->getResults();
		return (!empty($result))? $result : [];
	});

$page_details	=	$Pagination->getAllButResults();

?>
<div class="col-count-5" id="search-bar">
	<div class="col-count-12 lrg-10 med-6 sml-5 search-bar max-range">
		<?php foreach($page_details['max_range'] as $num): ?>
		<div class="pagination-max"><a href="?<?php echo http_build_query(['max' => $num, "table" => $this->getRequest('table'), 'current' => $page_details['current'], 'search' => $this->getGet('search')]) ?>"><?php echo $num ?></a></div>
		<?php endforeach ?>
	</div>
	<div class="col-count-8 search-bar navigator">
		
		<?php if($page_details['previous'] !== 1 && !empty($page_details['previous'])): ?>
		<div class="pagination-max"><a href="?<?php echo http_build_query(['max' => $this->getGet('max'), "table" => $this->getRequest('table'), 'current' => $page_details['previous'], 'search' => $this->getGet('search')]) ?>">&lt;</a></div>
		<?php endif ?>
		<?php foreach($page_details['range'] as $num): ?>
		<div class="pagination-max"><a href="?<?php echo http_build_query(['max' => $this->getGet('max'), "table" => $this->getRequest('table'), 'current' => $num, 'search' => $this->getGet('search')]) ?>"><?php echo $num ?></a></div>
		<?php endforeach ?>
		<?php if(!empty($page_details['next'])): ?>
		<div class="pagination-max"><a href="?<?php echo http_build_query(['max' => $this->getGet('max'), "table" => $this->getRequest('table'), 'current' => $page_details['next'], 'search' => $this->getGet('search')]) ?>">&gt;</a></div>
		<?php endif ?>
	</div>
	<div class="search-bar search span-3">
		<?php echo $Form->open(["method"=>'get','action'=>'?'.http_build_query(['max' => $this->getGet('max'), "table" => $this->getRequest('table'), 'search' => $this->getGet('search')]),'style' => 'width: 100%;']) ?>
			<?php echo $Form->fullhide(['name' => 'max', 'value' => $this->getGet('max')]) ?>
			<?php echo $Form->fullhide(['name' => 'table', 'value' => $this->getGet('table')]) ?>
			<div class="col-count-4">
				<div class="span-3">
					<?php echo $Form->text(['name' => 'search', 'value' => $this->getGet('search'), 'class'=>'nbr']) ?>
				</div>
				<div>
				<?php echo $Form->submit(['value' => 'Search', 'class'=>'medi-btn green']) ?>
				</div>
			</div>
		<?php echo $Form->close() ?>
	</div>
</div>
<?php //echo printpre($page_details); ?>

<?php
if(!empty($this->getRequest('create'))):
	echo $this->getPlugin('table_view', DS.'add.php');
elseif(is_numeric($this->getRequest('edit'))):
	$result	=	@$this->nQuery()->query("SELECT * FROM ".$this->getRequest('table')." WHERE ID = ?", [$this->getRequest('edit')])->getResults(1);

	if(empty($result)) {
		$this->toError('Row is invalid.'); ?>
		<?php
		echo $this->getPlugin('notifications');
		return false;
	}
   
   	$this->setNode('table_data', $result);
	echo $this->getPlugin('table_view', DS.'form.php');
	
	?>
	<script>
	$(function(){
		$('input[name="delete"]').on('change',function(e){
			var	getVal	=	$(this).is(':checked');
			var	uSher	=	(getVal)? confirm('Do you really want to delete?') : true;
			if(uSher) {
				$('#user-editor').find('input,select').prop('disabled',getVal);
				$(this).prop('disabled', false);
				$('#user-editor').find('input[type="submit"],input[name="action"],input[name="ID"],input[type=hidden]').prop('disabled',false);
			}
			else {
				$(this).prop("checked", false);
			}
		});
	});	
	</script>
<?php else: ?>
<?php
$cols	=	array_map(function($v){ return \Nubersoft\nApp::call()->getHelper('nRender')->colToTitle($v['Field']); }, @$this->nQuery()->query("describe ".$this->getRequest('table'))->getResults());

?>
	<div style="overflow: auto;">
		<table class="generic-table" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td><?php echo implode('</td>'.PHP_EOL.'<td>',array_merge($cols, ['&nbsp;'])) ?></td>
			</tr>
		<?php foreach($Pagination->getResults() as $row): ?>
			<tr onClick="window.location='?table=<?php echo $this->getRequest('table') ?>&edit=<?php echo $row["ID"] ?>'" class="table-body-row">
		<?php foreach($row as $key => $value): ?>
				<td>
					<?php echo $value ?>
				</td>
		<?php endforeach ?>

				<td><a href="?table=<?php echo $this->getRequest('table') ?>&edit=<?php echo $row["ID"] ?>" class="mini-btn dark">EDIT</a></td>
			</tr>
		<?php endforeach ?>
		</table>
	</div>
<?php endif;