<?php
$Crypt		=	$this->getHelper('nCrypt');
$compData	=	$this->getPluginContent('image_tools');
$filepath	=	str_replace('//','/',$compData['file_path'].'/'.$compData['file_name']);
$ID			=	$compData['ID'];
$download	=	$Crypt->encOpenSSLUrl(json_encode(['table' => $compData['table'], 'ID' => $compData['ID']]));

if(!empty($compData['thumb'])) :
?>
<div class="align-middle align-center" style="border: 1px solid #666; background-size: 20px; background-color: #FFF; background-image: url('<?php echo $this->localeUrl('/core/template/default/media/images/ui/transparent-grid.gif') ?>'); margin-top: 1em;">
	<img src="<?php echo $compData['thumb'] ?>" class="no-stretch" style="margin: 1em auto;" />
</div>
<?php endif ?>
<input type="text" onClick="this.select()" class="nbr" value="<?php echo $filepath ?>" />
<a href="?action=download_file&id=<?php echo $download ?>" class="mini-btn dark">Download</a>
<a href="?action=delete_file&id=<?php echo $download ?>" class="mini-btn dark nbr_confirm" data-instructions='{"msg":"Delete this file?","ok":"this::href"}'>Delete</a>