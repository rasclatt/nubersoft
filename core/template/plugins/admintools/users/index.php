<?php
$Form	=	@$this->nForm();
?>
<?php echo $this->getPlugin('admintools', DS.'users'.DS.'interface.php') ?>
<h3>User Accounts</h3>

<?php
$Pagination	=	$this->getHelper('SearchEngine\View')->fetch([
	'columns' => [
		'first_name',
		'last_name',
		'email',
		'username'
	],
	'sort' => 'DESC'
	],
	function($nQuery, $Pagination, $REQ){

		if(!empty($REQ['search'])) {
			$bind		=	array_fill(0,count($Pagination->columns),'%'.$Pagination->dec(urldecode($REQ['search'])).'%');
			
			foreach($Pagination->columns as $col) {
				$where[]	=	$col." LIKE ?";
			}
			
			$where	=	implode(' ',array_merge([" WHERE "],[implode(" OR ", $where)]));
		}
		else
			$where	=	'';
		
		$sql	=	"SELECT
						COUNT(*) as count
					FROM
						users
					{$where}";
		return $Pagination->query($sql,(!empty($bind)? $bind : null))->getResults(1)['count'];
	},
	function($REQ, $Pagination, $page, $limit, $orderB, $orderH){
		if(!empty($REQ['search'])) {
			$bind		=	array_fill(0,count($Pagination->columns),'%'.$Pagination->dec(urldecode($REQ['search'])).'%');
			
			foreach($Pagination->columns as $col) {
				$where[]	=	$col." LIKE ?";
			}
			
			$where	=	implode(' ',array_merge([" WHERE "],[implode(" OR ", $where)]));
		}
		else
			$where	=	'';
		
		$sql	=	"SELECT
						*
					FROM
						users
					{$where}
					ORDER BY
						".$orderB." ".$orderH."
					LIMIT
						{$page}, {$limit}";
		
		$results	=	$Pagination->query($sql,(!empty($bind)? $bind : null))->getResults();
		
		return (!empty($results))? array_map(function($v){
			$v['name']		=	$v['first_name'].' '.$v['last_name'];
			$v['avatar']	=	(!empty($v['file_path'].' '.$v['file_name']))? '<img src="'.$v['file_path'].' '.$v['file_name'].'" class ="user-avatar" />' : '';
			return $v;
		},$results) : [];
	});

$page_details	=	$Pagination->getAllButResults();

?>
<div class="col-count-5" id="search-bar">
	<div class="col-count-12 lrg-10 med-6 sml-5 search-bar max-range">
		<?php foreach($page_details['max_range'] as $num): ?>
		<div class="pagination-max"><a href="?<?php echo http_build_query(['max' => $num, "table" => 'users', 'current' => $page_details['current'], 'search' => $this->getGet('search')]) ?>"><?php echo $num ?></a></div>
		<?php endforeach ?>
	</div>
	<div class="col-count-8 search-bar navigator">
		
		<?php if($page_details['previous'] !== 1 && !empty($page_details['previous'])): ?>
		<div class="pagination-max"><a href="?<?php echo http_build_query(['max' => $this->getGet('max'), "table" => 'users', 'current' => $page_details['previous'], 'search' => $this->getGet('search')]) ?>">&lt;</a></div>
		<?php endif ?>
		<?php foreach($page_details['range'] as $num): ?>
		<div class="pagination-max"><a href="?<?php echo http_build_query(['max' => $this->getGet('max'), "table" => 'users', 'current' => $num, 'search' => $this->getGet('search')]) ?>"><?php echo $num ?></a></div>
		<?php endforeach ?>
		<?php if(!empty($page_details['next'])): ?>
		<div class="pagination-max"><a href="?<?php echo http_build_query(['max' => $this->getGet('max'), "table" => 'users', 'current' => $page_details['next'], 'search' => $this->getGet('search')]) ?>">&gt;</a></div>
		<?php endif ?>
	</div>
	<div class="search-bar search span-3">
		<?php echo $Form->open(["method"=>'get','action'=>'?'.http_build_query(['max' => $this->getGet('max'), "table" => 'users', 'search' => $this->getGet('search')]),'style' => 'width: 100%;']) ?>
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
	echo $this->getPlugin('admintools', DS.'users'.DS.'add.php');

elseif(is_numeric($this->getRequest('edit'))):
	$user	=	$this->getHelper('nUser')->getUser($this->getRequest('edit'), 'ID');

	if(empty($user)) {
		$this->toError('User is invalid.'); ?>
		<?php
		echo $this->getPlugin('notifications');
		return false;
	}
   
   	$this->setNode('user_data', $user);
	echo $this->getPlugin('admintools', DS.'users'.DS.'form.php');
	
	?>
	<script>
	$(function(){
		$('input[name="delete"]').on('change',function(e){
			var	getVal	=	$(this).is(':checked');
			var	uSher	=	(getVal)? confirm('Do you really want to delete this user?') : true;
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

	<div class="user-table">
		<div class="col-count-7 table-row-container">
			<div class="table-header"><?php echo implode('</div>'.PHP_EOL.'<div class="table-header">',['ID', 'Username', 'Email', 'Name','Usergroup', 'Status','&nbsp;' ]) ?></div>
		</div>
	<?php foreach($Pagination->getResults() as $row): ?>
		<div class="col-count-7 table-row-container" onClick="window.location='?table=users&edit=<?php echo $row["ID"] ?>'">
	<?php foreach($row as $key => $value):
				if(!in_array($key, ['ID', 'username', 'email', 'name','usergroup', 'user_status' ]))
					continue;
			?>
			<div style="overflow: hidden;" class="table-row"><?php echo ($key == 'usergroup' && !is_numeric($value))? constant($value) : $value ?></div>
	<?php endforeach ?>
			
			<div class="table-row"><a href="?table=users&edit=<?php echo $row["ID"] ?>" class="mini-btn dark">EDIT</a></div>
		</div>
	<?php endforeach ?>
	</div>
<?php endif;