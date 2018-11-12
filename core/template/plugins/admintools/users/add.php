
<?php
$fields	=	$this->query("describe users")->getResults();

$this->setNode('user_data', array_combine(
	array_filter(
		array_map(function($v){
			return $v['Field'];
		},$fields)
	), array_fill(0,count($fields),''))
);
echo $this->getPlugin('admintools', DS.'users'.DS.'form.php');
?>
<script>
	$(function(){
		$('input[name="username"]').on('keyup change', function(){
			$('input[name="email"]').val($(this).val());
		});
	});
</script>