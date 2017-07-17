<?php echo $this->getTemplateDoc('page.head.php') ?>
<!-- Start of the content -->
<body class="nbr">
	<!-- Modal window for basic returns -->
	<div id="loadspot_modal"></div>
	<!-- Content wrapper -->
	<div id="nbr_page">
		<!-- Admin Tool Top Bar -->
		<?php echo $this->useTemplatePlugin('admintools_top_bar','codetool.php').PHP_EOL ?>
		<!-- Body content -->
		<?php echo $this->getTemplateDoc('page.body.php').PHP_EOL ?>
		<!-- Foot content -->
		<?php echo $this->getTemplateDoc('page.foot.php').PHP_EOL ?>
	</div>
</body>
</html>
<?php
die(printpre($this->getDataNode()));