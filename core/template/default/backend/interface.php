<?php
if(!$this->isAdmin())
	return false;
$table	=	$this->getRequest('table');
$layout	=	$this->getPlugin('admintools', DS.$table.DS.'index.php');

if($layout):
	echo $layout;
else: ?>

<h2>Dashboard</h2>
<p>Welcome to your dashboard.</p>
<div class="col-count-5 lrg-3 med-2 sml-1">
<?php
	
	foreach($this->getDataNode('plugins')['paths'] as $path) {
		if(!is_dir($path))
			continue;
		
		foreach(scandir($path) as $pdir) {
			if(in_array($pdir, ['.','..']))
				continue;
			
			if(!is_file($ui = $path.DS.$pdir.DS.'admin_ui.php'))
				continue;
		?>
	
	<div class="admin-plugin" id="admin-plugin-<?php echo $pdir ?>">
		<?php include($ui) ?>
	</div>
		
		<?php
		}
	}
	?>
</div>
<?php
	 
endif;