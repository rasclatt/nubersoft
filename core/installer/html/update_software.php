<?php
$err	=	$this->getDataNode('update_error');
if(!empty($err)): ?>
<div class="nbr_error"><?php echo $err ?></div>
<?php endif ?>

<h1>Update system software</h1>
<a href="?action=update_system_software" class="medi-btn green">Update Nubersoft</a>

<?php
if($this->getGet('action') != 'update_system_software')
	return false;

$from	=	'https://github.com/rasclatt/nUberSoft-Framework/archive/master.zip';
$to		=	NBR_CLIENT_CACHE.DS.'installer'.DS.'master.zip';
$dir	=	pathinfo($to, PATHINFO_DIRNAME);
$downld	=	file_get_contents($from);
$this->isDir($dir, true);

if(empty($downld)) {
	echo 'Updater failed to download.';
	return false;
}
else {
	file_put_contents($to, $downld);
}
	

echo printpre();