<?php
if(!$this->isAdmin()) {
	return;
}
# Set the deployment folder path
$dPath	=	NBR_CLIENT_SETTINGS.DS.'deploy';

if($this->getPost('action') == 'nbr_focus_remove_cache') {
	$files	=	$this->toArray($this->getPost('pref_file_remove'));
	$types	=	$this->toArray($this->getPost('types'));
	$depoy	=	$this->getPost('deploy');
	
	if(!empty($files)) {
		foreach($files as $file) {
			$file	=	base64_decode($file);
			$fPath	=	$this->getCacheFolder($file);
			if(is_file($fPath)) {
				if(unlink($fPath)) {
					$this->saveIncidental('nbr_focus_remove_cache',array('msg'=>'Removed: '.$file));
				}
			}
		}
	}
	elseif(!empty($types)) {
		$files	=	$this->getDirList(array('dir'=>$this->getCacheFolder(),'type'=>array_keys($types)),'host');
		if(!empty($files)) {
			foreach($files as $paths) {
				unlink($paths);
			}
		}
	}
	elseif($depoy) {
		if(is_dir($dPath)) {
			$this->getHelper('nFileHandler')->deleteContents($dPath);
			if(!is_dir($dPath)) {
				$this->isDir($dPath,array('secure'=>true,'make'=>true));
			}
			else
				$this->saveIncidental($this->getPost('action'),array('msg'=>'Directory was unable to be cleared.'));
		}
	}
}

$types['html']		=	'Layouts';
$types['php']		=	'Executables';
$types['json']		=	'Data sets';
$types['pref']		=	'General data';
?>
<div class="allblocks">
	<?php
	$cache	=	$this->getDirList($this->getCacheFolder(),'host');
	$new	=	array();
	foreach($cache as $key => $path) {
		if(is_file($path)) {
			$ext			=	pathinfo($path,PATHINFO_EXTENSION);
			if($ext != 'htaccess')
				$new[$ext][]	=	str_replace($this->getCacheFolder(),'',$path);
		}
	}
	
	if(is_dir($dPath)) {
		$installs	=	$this->getDirList($dPath,'host');
		if(!empty($installs)) {
	?>
	<ul class="nbr_standard cell" style="padding: 0; margin: 0;">
		<li style="padding: 0; margin: 0;">
			<h1 class="nbr_ux_element">Deployments List</h1>
		</li>
		<li style="padding: 0 10px; margin: 0px;">
			<div class="type_cache">
				<form method="post" action="<?php echo $this->siteUrl($this->getPageURI('full_path')) ?>">
					<input type="hidden" name="action" value="nbr_focus_remove_cache" />
					<input type="hidden" name="deploy" value="1" />
					<div class="nbr_button small" style="float: right;">
						<input type="submit" name="delete" value="DELETE DEPLOYMENTS" />
					</div>
				</form>
			</div>
		</li>
	</ul>
	<table class="nbr_ux_element">
	<?php 
	
			foreach($installs as $file) {
	?>
		<tr>
			<td><?php $fname = (is_file($file))? ucwords(str_replace('_',' ',pathinfo($file,PATHINFO_FILENAME))) : str_replace($dPath,'',$file); echo $fname ?> (<?php echo str_replace($dPath,'',$file) ?>)</td>
		</tr>
	<?php
			}
	?>
	</table>
	<?php
		}
	}
	
	if(!empty($new)) {
	?>
	<h1 class="nbr_ux_element">Delete files by tag</h1>
	<div class="type_cache">
		<form method="post" action="<?php echo $this->siteUrl($this->getPageURI('full_path')) ?>">
			<input type="hidden" name="action" value="nbr_focus_remove_cache" />
			<table class="nbr_cache_tags">
				<?php
				foreach(array_keys($new) as $types) {
				?>
				<tr class="click_row">
					<td><?php echo $types ?></td>
					<td><input type="checkbox" name="types[<?php echo $types ?>]" /></td>
				</tr>
		<?php
				}
		?>
			</table>
		<?php
			}
			?>
			<div class="nbr_button small" style="float: right;">
				<input type="submit" name="delete" value="DELETE BY TAG" />
			</div>
		</form>
	</div>
	
	<h1 class="nbr_ux_element">Delete files by name</h1>
	<a href="#" class="clear_search">Clear</a>
	<input type="search" class="searching" placeholder="Start to type to narrow down" />
	<form method="post" action="<?php echo $this->siteUrl($this->getPageURI('full_path')) ?>">
	<input type="hidden" name="action" value="nbr_focus_remove_cache" />
	<table cellpadding="0" cellspacing="0" border="0" id="cache_manager">
	<?php
	foreach($new as $group => $paths) {
	?>
		<tr>
			<th colspan="2"><?php echo $group; echo (isset($types[$group]))? " ({$types[$group]})" : '' ?></th>
		</tr>
		<?php foreach($paths as $path) { ?>
		<tr class="click_row">
			<td class="cache_searchable"><?php echo $path ?></td>
			<td><input type="checkbox" name="pref_file_remove[]" value="<?php echo base64_encode($path) ?>" /></td>
		</tr>
	<?php
		}
	}
	?>
	</table>
		<div style="position: fixed; bottom: 0; left: 0; right: 0; height: 120px; text-align: center; z-index: 10000000000; background-color: #333;">
			<div class="nbr_button">
				<input type="submit" name="delete" value="DELETE" />
			</div>
		</div>
	</form>
</div>
<script>
$(document).ready(function() {
	$('.click_row').on('click',function(e) {
		if($(e.target).attr('type') == 'checkbox')
			return;
		
		var findBox		=	$(this).find('input[type=checkbox]');
		var	isChecked	=	(!findBox.is(":checked"));
		findBox.prop("checked",isChecked);
	});
	
	$('.clear_search').on('click',function(e) {
		$('.searching').val('');
		$('.cache_searchable').parents('tr').show();
	});
	
	$('.searching').on('keyup',function(e) {
		var	sVal		=	$(this).val();
		var	cache_files	=	$('.cache_searchable');
		$.each(cache_files, function(k,v) {
			var	currElem	=	$(v);
			var currElemTxt	=	currElem.text();
			if(!currElemTxt.match(sVal)) {
				currElem.parents('tr').hide();
				console.log();
			}
			else
				currElem.parents('tr').show();
				
		});
	});
});
</script>
<style>
.nbr_cache_tags {
	background-color: #EBEBEB;
	display: inline-block;
}
.nbr_cache_tags tr {
	display: ininline-block;
	padding: 5px;
	border-radius: 3px;
	float: left;
	border: 1px solid;
	margin: 3px;
	background-color: #E4E8E1;
}
.nbr_cache_tags tr:hover {
	background-color: #69C;
	color: #FFF;
	text-shadow: 1px 1px 2px #000;
}
.nbr_cache_tags tr:hover,
.nbr_cache_tags tr input[type=checkbox]:hover,
.nbr_cache_tags tr td:hover {
	cursor: pointer;
}
.type_cache {
	margin-bottom: 30px;
	font-family: Arial, Helvetica, sans-serif;
}
.type_cache td {
	margin-bottom: 1px solid #CCC;
}
#cache_manager,
.nbr_deployed {
	width: 100%;
	margin-bottom: 100px;
}
#cache_manager th,
#cache_manager td,
.nbr_deployed th,
.nbr_deployed td	{
	text-align: left;
	font-family: Arial, Helvetica, sans-serif;
	padding: 5px;
	border-bottom: 1px solid #999;
}
#cache_manager th,
.nbr_deployed th {
	background-color: #333;
	color: #FFF;
	font-size: 22px;
}
#cache_manager tr:hover td,
.nbr_deployed tr:hover td {
	background-color: #EBEBEB;
}
</style>