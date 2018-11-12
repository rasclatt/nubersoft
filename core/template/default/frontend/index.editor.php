<!doctype html>
<html id="admintools">
<?php echo $this->getFrontEnd('head.php') ?>
<body class="edit-mode">
<?php echo $this->getPlugin('adminbar') ?>
<div class="col-count-3 offset content">
	<?php echo $this->getPlugin('editor', DS.'page_editor.php') ?>
	<?php echo $this->getPlugin('notifications') ?>
	<div class="col-2">
		<?php echo $this->getPlugin('editor') ?>
	</div>
</div>
</body>
</html>