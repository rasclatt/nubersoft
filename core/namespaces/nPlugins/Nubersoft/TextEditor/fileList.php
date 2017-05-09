<?php
if(!$this->isAdmin())
	return;

$filesAllowed	=	$this->FileMaster->getFileTypes();
$docs			=	(!empty($filesAllowed['doc']))? $filesAllowed['doc'] : array();
$text			=	(!empty($filesAllowed['text']))? $filesAllowed['text'] : array();
$filter			=	array_merge($docs,$text);
$filterImg		=	(!empty($filesAllowed['img']))? array_keys($this->organizeByKey($filesAllowed['img'],'file_extension')) : array();
$allowed		=	array_keys($this->organizeByKey($filter,'file_extension'));
?>
<table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
<?php
if(!empty($dir)) {
	$settingsDir	=	$this->toSingleDs($this->stripRoot(NBR_CLIENT_DIR)).DS.'settings';
	foreach($dir as $folder => $files) {
		$afolder['local']		=	$this->stripRoot($folder);
		if(strpos($afolder['local'],$settingsDir) !== false)
			continue;
		elseif(empty($files))
			continue;
		
		$afolder['id']			=	str_replace(array(DS,"/",".","_"),"",$afolder['local']);
		$afolder['zippit']		=	$this->nDownloader->encode(array("table"=>"zip","ID" => urlencode($folder)),$this->getFileSalt());
		$afolder['contents']	=	$this->nDownloader->encode(array("table"=>"edit","ID" => urlencode($folder)),$this->getFileSalt());
?>	<tr>
		<td>
			<div class="topfolder text-editor-files nTrigger" data-instructions='{"FX":{"acton":["next::slideToggle"],"fx":["slideToggle"],"fxspeed":["fast"]}}'>
				<img src="<?php echo $this->siteUrl('/media/images/ui/folder.png'); ?>" style="max-height: 20px; float: left; display: inline-block;" /><?php echo $afolder['local']; ?>
			</div>
			<div class="text-editor-folders panelhide">
				<div class="text-hovershow">
					<span class="ajaxtrigger" data-gopage="edit.folderfiles" data-gopagekind="g" data-gopagesend="contents=<?php echo $afolder['contents']; ?>" data-freeze="html">EDIT FOLDER CONTENTS</span>&nbsp;&nbsp;|&nbsp;&nbsp;<span onClick="window.location='<?php echo $this->siteUrl('/?action=download&file='.$afolder['zippit']) ?>'">DOWNLOAD CONTENTS</span>
				</div>
<?php	foreach($files as $file) {
			if(is_dir($file))
				continue;
				
			$ext		=	pathinfo($file,PATHINFO_EXTENSION);
			$thumbnail	=	false;
			$readable	=	in_array($ext,$allowed);
					
			if(in_array($ext,$filterImg))
				$thumbnail	=	$this->FileMaster->getInfo($file)->thumbnailPreview();
?>
				<table cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td rowspan="2" style="width: 40px;">
<?php		if(!empty($thumbnail->layout))
				echo $thumbnail->layout;
			else {								
				$base_core		=	'/media/images/ui/';
				$def_core		=	$base_core.'doc.png';
				$def_core_dir	=	$base_core.'dir.png';
				$icn			=	$base_core.str_replace(".","",$ext).".png";
				$useIcon		=	(is_file(NBR_ROOT_DIR.$icn))? $icn : $def_core;
				
?>							<div style="background-image: url('<?php echo $this->siteUrl($useIcon) ?>'); background-repeat: no-repeat; background-size: contain; height: 35px; width: 35px; background-position: center;"></div>
<?php		}
?>
						</td>
						<td class="text-edit-subdir-base">
<?php 		if($readable) {
?>							<a href="?load_page=<?php echo urlencode($this->safe()->encode(base64_encode($file)))."&".$this->createQueryString(array("page_editor"),$this->toArray($this->getGet()),true); ?>"><?php echo basename($file); ?></a>
<?php 		}
			else {
?>							<p><a href="<?php echo $this->siteUrl(str_replace(NBR_ROOT_DIR,"",$file)) ?>" target="_blank"><?php echo basename($file); ?></a></p>
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