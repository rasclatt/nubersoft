<?php echo $this->getTemplateDoc('page.head.php') ?>
<!-- Start of the content -->
<body>
	<!-- Modal window for basic returns -->
	<div id="loadspot_modal"></div>
	<!-- Content wrapper -->
	<div id="nbr_page">
<?php
		ob_start()
		?>
		<?php echo $this->useTemplatePlugin('admintools_top_bar','codetool.php').PHP_EOL ?>
		<!-- Body content -->
		<?php echo $this->getTemplateDoc('page.body.php').PHP_EOL ?>
		<!-- Foot content -->
		<?php echo $this->getTemplateDoc('page.foot.php').PHP_EOL ?>
		
		<?php
		$data	=	ob_get_contents();
		ob_end_clean();
		# Remove extra returns
		echo implode(PHP_EOL,array_filter(array_map(function($v){$x = trim($v); return (empty($x))? $x : $v; },explode(PHP_EOL,$data))));
		?>
		
	</div>
</body>
</html>