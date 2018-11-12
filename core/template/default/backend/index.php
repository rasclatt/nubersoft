<!doctype html>
<html>
<?php echo $this->getBackEnd('head.php') ?>
<body>
<div id="admin-interface">
	<div class="span-2">
		<?php echo $this->getPlugin('adminbar') ?>
	</div>
	<div id="admin-content-wrap" class="span-2">
		<div id="admin-sidebar">
			
			<?php echo $this->getPlugin('adminbar', DS.'sidebar.php') ?>
	
		</div>
		<div>
			<?php echo $this->getPlugin('notifications') ?>
			<div id="admin-content">
				<?php echo $this->getBackEnd('interface.php') ?>
			</div>
		</div>
	</div>
</div>
<div id="loadspot-modal"></div>
</body>
</html>