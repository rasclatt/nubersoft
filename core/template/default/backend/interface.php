<?php
if(!$this->isAdmin())
	return false;
$table	=	$this->getRequest('table');
$layout	=	$this->getPlugin('admintools', DS.$table.DS.'index.php');

if($layout) { 
	echo $layout;
}
else{ ?>

<h2>Dashboard</h2>
<p>Welcome to your dashboard.</p>

<?php
	$plugs	=	realpath(pathinfo($this->getPlugin('admintools', DS.'users'.DS.'index.php', true), PATHINFO_DIRNAME).DS.'..'.DS);
	foreach(scandir($plugs) as $dir) {
		if(in_array($dir, ['.','..']))
			continue ?>

<div><a href="?table=<?php echo $dir ?>"><?php echo $this->colToTitle($dir) ?></a></div>

	<?php
	}
}