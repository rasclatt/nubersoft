<?php
if(!$this->isAdmin())
	return;

	$name	=	'runtime_'.date('YmdHis').md5(mt_rand()).'.txt';
	$report	=	file_put_contents($this->getCacheFolder($name),print_r($this->getDataNode('workflow_runtime'),1).PHP_EOL.PHP_EOL.PHP_EOL.'**************************************************************'.PHP_EOL.'**************** APPLICATION TRANSACTION LIST ****************'.PHP_EOL.'**************************************************************'.PHP_EOL.PHP_EOL.print_r($this->getDataNode('workflow_run'),1));
?>
<script>
$(document).ready(function() {
	var	classTime	=	false;
	var	loadTime	=	<?php echo number_format($this->getDataNode('workflow_runtime_total'),3) ?>;
	$('#nbr_load_time').html(loadTime+'&nbsp;<a href="<?php echo $this->localeURL($this->getPageURI('full_path').'?automate='.$this->getHelper('nToken')->nOnceClear('workflow_runtime_total')->nOnce('workflow_runtime_total',json_encode(array('action'=>'download','file'=>$this->getCacheFolder($name),'usergroup'=>'NBR_SUPERUSER')))->getToken()) ?>">secs</a>');
	if(loadTime > 2)
		classTime	=	'badtime';
	else if(loadTime <= 1)
		classTime	=	'goodtime';
	
	if(!empty(classTime))
		$('#nbr_load_time').addClass(classTime);
});
</script>