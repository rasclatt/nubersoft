<?php
if(!function_exists("AutoloadFunction"))
	return;

if(!is_admin())
	return;

AutoloadFunction('get_file_extension,get_file_types,download_encode');

$filesAllowed	=	get_file_types();
$docs			=	(!empty($filesAllowed['doc']))? $filesAllowed['doc'] : array();
$readFiles		=	(!empty($filesAllowed['readable']))? $filesAllowed['readable'] : array();
$filterImg		=	(!empty($filesAllowed['image']))? $filesAllowed['image'] : array();
$filter			=	array_merge($docs,$readFiles);
?>
<table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
<?php
if(!empty($dir)) {
	foreach($dir as $folder => $files) {
		$afolder['local']		=	str_replace(NBR_ROOT_DIR,"",$folder);
		$afolder['id']			=	str_replace(array(_DS_,"/",".","_"),"",$afolder['local']);
		$afolder['zippit']		=	download_encode(array("table_id"=>"zip","file_id" => urlencode($folder)),nApp::getFileSalt());
		$afolder['contents']	=	download_encode(array("table_id"=>"edit","file_id" => urlencode($folder)),nApp::getFileSalt());
?>	<tr>
		<td>
			<div class="topfolder text-editor-files btntrigger">
				<img src="<?php echo site_url(); ?>/core_images/ui/folder.png" style="max-height: 20px; float: left; display: inline-block;" /><?php echo $afolder['local']; ?>
			</div>
			<div class="text-editor-folders panelhide">
				<div class="text-hovershow">
					<span class="ajaxtrigger" data-gopage="edit.folderfiles" data-gopagekind="g" data-gopagesend="contents=<?php echo $afolder['contents']; ?>" data-freeze="html">EDIT FOLDER CONTENTS</span>&nbsp;&nbsp;|&nbsp;&nbsp;<span onClick="window.location='<?php echo site_url(); ?>/download.php?file=<?php echo $afolder['zippit']; ?>'">DOWNLOAD CONTENTS</span>
				</div>
<?php	foreach($files as $file) {
			if(is_dir($file))
				continue;
				
			$ext		=	get_file_extension(basename($file));
			$thumbnail	=	false;
			$readable	=	in_array($ext,$filter);
					
			if(in_array($ext,$filterImg))
				$thumbnail	=	$img_file->GetInfo($file)->ThumbnailPreview();
?>
				<table cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td rowspan="2" style="width: 40px;">
<?php		if(!empty($thumbnail->layout))
				echo $thumbnail->layout;
			else {								
				$base_core		=	'/core_images/ui/';
				$def_core		=	$base_core.'doc.png';
				$def_core_dir	=	$base_core.'dir.png';
				$icn			=	$base_core.str_replace(".","",$ext).".png";
				$useIcon		=	(is_file(NBR_ROOT_DIR.$icn))? $icn : $def_core;
				
?>							<div style="background-image: url('<?php echo site_url().$useIcon; ?>'); background-repeat: no-repeat; background-size: contain; height: 35px; width: 35px; background-position: center;"></div>
<?php		}
?>
						</td>
						<td class="text-edit-subdir-base">
<?php 		if($readable) {
?>							<a href="?load_page=<?php echo urlencode(Safe::encode(base64_encode($file)))."&".create_query_string(array("page_editor"),$_GET,true); ?>"><?php echo basename($file); ?></a>
<?php 		}
			else {
?>							<p><a href="<?php echo site_url().str_replace(NBR_ROOT_DIR,"",$file); ?>" target="_blank"><?php echo basename($file); ?></a></p>
<?php		}
			if(isset($thumbnail->dimensions) && $thumbnail->dimensions) {
?>							<p style="font-size: 12px; font-style: oblique; margin: 0;"><?php echo $thumbnail->dimensions[0]; ?>px / <?php echo $thumbnail->dimensions[1]; ?>px</p>
<?php 		}
?>							<p style="font-size: 12px; margin:0;"><?php echo number_format((filesize($file)/1024),2); ?>Kb</p>
						</td>
					</tr>
					<tr>
						<td class="text-edit-subdir">
<?php 		if($readable) {
?>							<p style="font-size: 10px;"><?php echo str_replace(NBR_ROOT_DIR,"",$file); ?></p>
<?php 		}
?>						</td>
					</tr>
				</table>
<?php 	}
?>			</div>
		</td>
	</tr>
	<?php 
	}
}
else {
?>
	<tr>
		<td style="padding: 10px;">
			Empty folder.
		</td>
	</tr>
<?php
}
?>
</table>