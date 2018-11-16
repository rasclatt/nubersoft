<?php
$err	=	$this->getDataNode('update_error');
if(!empty($err)): ?>
<div class="nbr_error"><?php echo $err ?></div>
<?php endif ?>

<h1>Update system software</h1>
<a href="?action=update_system_software" class="medi-btn green">Update Nubersoft</a>
<div class="pad-top" id="update-log">
<?php
if($this->getGet('action') == 'update_system_software') {
	$from	=	'https://github.com/rasclatt/nUberSoft-Framework/archive/master.zip';
	$to		=	NBR_CLIENT_CACHE.DS.'installer'.DS.'master.zip';
	$dir	=	pathinfo($to, PATHINFO_DIRNAME);
	$downld	=	file_get_contents($from);
	$this->isDir($dir, true);

	if(empty($downld)) {
		echo 'Updater failed to retrieve.';
		return false;
	}

	file_put_contents($to, $downld);

	if(!is_file($to)) {
		echo 'Updater failed to download.';
		return false;
	}
	$extracto	=	$dir.DS.'extracted';
	$finalFrom	=	$extracto.DS.'nUberSoft-Framework-master';
	$Archive	=	new ZipArchive();
	$response	=	$Archive->open($to);
	if($response === true && $Archive->numFiles > 0) {
		$this->isDir($extracto, true);
		$Archive->extractTo($extracto);
		$Archive->close();

		if(!is_dir($finalFrom)) {
			echo 'Updater failed to extract.';
			return false;
		}

		foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($finalFrom, RecursiveDirectoryIterator::KEY_AS_PATHNAME | RecursiveDirectoryIterator::SKIP_DOTS)) as $key => $value) {
			$path		=	pathinfo($key);
			$copy_from	=	$key;
			$copy_to	=	$this->toSingleDs(NBR_ROOT_DIR.DS.str_replace($finalFrom, '', $path['dirname']));
			$this->isDir($copy_to, true);

			$copy_dest	=	str_replace(DS.DS, DS, $copy_to.DS.$path['basename']);

			if(copy($copy_from, $copy_dest)) {
				echo 'Copied file: '.$copy_dest.(!is_file($copy_dest)? ' <span style="color: green">(NEW)</span>' : '').'<br />';
			}
			else
				echo '<span style="color: red">Skipped file: '.$copy_dest.'</span><br />';

		}

		echo '<a href="'.$this->getHelper('nRouter')->getPage(1, 'is_admin')['full_path'].'?action=clear_cache" class="medi-btn green">Back to Admin</a>';

		if(is_file($flag = NBR_CORE.DS.'installer'.DS.'firstrun.flag'))
			unlink($flag);
	}
	else
		echo $Archive;
}
else
	echo 'Waiting...';
?>
</div>