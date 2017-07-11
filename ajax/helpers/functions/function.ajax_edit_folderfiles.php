<?php
use \Nubersoft\nApp as nApp;
use \Nubersoft\nApp as Safe;

function ajax_edit_folderfiles()
	{
		ob_start();
		autoload_function('check_empty,code_markup,download_decode');
		if(nApp::getPost('text_editor')) {
			if(!check_empty($_POST,'remove_file') && !check_empty($_POST,'remove_folder')) {
?>			<div style="padding: 10px; border: 10px solid #EBEBEB; background-color: #FFF;">
				<h1>Whoops!</h1>
				<p>There are no files or folders to delete. Press the <?php echo code_markup("`ESC`"); ?> key to return.</p>
			</div>
<?php			$data	=	ob_get_contents();
				ob_end_clean();
				return $data;
			}
			
			if(isset($_POST['remove_folder'])) {
				$recurse	=	new recursiveDelete();
				foreach($_POST['remove_folder'] as $folder) {
					$folder	=	base64_decode(Safe::decode(urldecode($folder)));
							
					if(is_dir($folder)) {
						$recurse->delete($folder);
?>							<div style="padding: 10px; border: 1px solid green;">
								<?php echo (!is_dir($folder))? "DELETED: ".$folder."<br />" : "FAILED: ".$folder."<br />"; ?>
							</div>

<?php				}
				}
			}
			
			if(nApp::getPost('remove_file')) {
				foreach($_POST['remove_file'] as $folder) {
					$file	=	base64_decode(Safe::decode(urldecode($folder)));
					
					if(is_file($file)) {
?>
					<div style="padding: 10px; border: 1px solid green;">
						<?php echo (unlink($file))? "DELETED: ".$file."<br />" : "FAILED: ".$file."<br />"; ?>
					</div>

<?php				}
					else
						echo 'NOT FILE: '.$file."<br />";
				}
			}
				
			$data	=	ob_get_contents();
			ob_end_clean();
			return $data;
		}

	$filecheck	=	new FileMaster();
?>
<table id="nbr_textloader">
	<tr>
		<td style="z-index: 10000000;vertical-align: middle; background-color: rgba(255,255,255,0.8);position:absolute; top:0; bottom: 0; left: 0; right: 0; text-align: center;">
			<div style="margin: 5% auto; max-width: 1000px; display: inline-block; max-height: 80%; border: 1px solid #FFF; box-shadow: 1px 1px 8px rgba(0,0,0,0.5); border-radius: 3px; overflow: auto; background-color: #FFF;">
				<div id="success">
				<form action="" method="post" id="filedelete">
					<input type="hidden" name="text_editor" value="1" />
					<table cellpadding="0" cellspacing="0" border="0" style="padding: 20px;">
						<tr>
							<td colspan="3" style="padding: 10px; background-color: #EBEBEB; font-size: 12px; vertical-align: bottom;">
								<div style="float: right; display: inline-block;">Press the <?php echo code_markup("`ESC`"); ?> key to return</div>
								<label><span id="check_all">Check All</span>&nbsp;<input type="checkbox" class="checkAll" /></label>
							</td>
						</tr>
				<?php 
				// Fetch directory listing
				$dir_decrypt	=	explode("/",download_decode(urlencode($_GET['contents'])));
				$dir			=	trim(urldecode($dir_decrypt[0]));
				$allow_edits	=	(!empty($dir_decrypt[1]) && trim($dir_decrypt[1]) == 'edit');
				// Check that it's a directory
				if(is_dir($dir) && $allow_edits) {
						autoload_function('get_files_folders,get_file_extension');
						$directory		=	get_files_folders($dir);
						
						foreach($directory as $folder => $files) {
							$folder_id		=	str_replace(array("/","-","."),array("_","",""),str_replace(NBR_ROOT_DIR,"",$folder)).rand(100,999);?>
						<tr>
							<td style="background-color: #CCC;">
								<img src="<?php echo site_url(); ?>/images/ui/folder.png" style="max-width: 40px;" />
							</td>
							<td style="background-color: #CCC; padding: 5px;">
								<input class="checkItem" type="checkbox" name="remove_folder[]" value="<?php echo urlencode(Safe::encode(base64_encode($folder))); ?>" id="<?php echo $folder_id; ?>" />
							</td>
							<td style="background-color: #CCC; text-align: left;">
								<div style="padding: 5px 5px 5px 0;">
									<label style="font-size: 12px;" for="<?php echo $folder_id; ?>"><?php echo str_replace(NBR_ROOT_DIR,"",$folder); ?></label>
								</div>
							</td>
						</tr>
<?php 						foreach($files as $file) {
								$ext	=	get_file_extension($file);
								$id		=	str_replace(array("_","-","."),"",str_replace(NBR_ROOT_DIR,"",$file));
?>
						<tr>
							<td>
<?php							$img_file	=	$filecheck	->Initialize()
															->GetInfo($file)
															->ThumbnailPreview();
															
								if(isset($img_file->layout) && $img_file->layout)
									echo $img_file->layout;
								else {
									$imgRoot	=	'/images/ui';
									$defImg		=	'doc.png';
									
									if(is_file(NBR_ROOT_DIR."{$imgRoot}/{$ext}.png"))
										$defImg	=	"{$ext}.png";
?>
								<img src="<?php echo site_url()."{$imgRoot}/{$defImg}"; ?>" style="max-width: 40px;" />
<?php							}
?>							</td>
							<td style=" border-left: 1px solid #CCC;padding: 5px;">
								<input class="checkItem checkFile" id="<?php echo $id; ?>" type="checkbox" name="remove_file[]" value="<?php echo urlencode(Safe::encode(base64_encode($file))); ?>" data-parentfolder="<?php echo $folder_id; ?>" />
							</td>
							<td style="padding: 5px;">
								<label style="font-size: 12px;" for="<?php echo $id; ?>">
									<?php echo str_replace(NBR_ROOT_DIR,"",$file); ?>
								</label>
							</td>
						</tr>
						<?php 		}
						}?>
					</table>
					<div style="padding: 10px 20px; background-color: #CCC; margin: 0 auto; text-align: center; border: 1px solid #CCC;">
						<div class="login_button" style="display: inline-block; margin: 0 auto;"><input type="submit" name="submit" value="DELETE" disabled="disabled" /></div>
					</div>
					<?php
					} ?>
				</form>
				</div>
			</div>
		</td>
	</tr>
</table>
<style>
#nbr_textloader	{
	display: block;
}
</style>
<script>

$(document).ready(function() {
	// Create new instance
	fileEngine	=	new FilesFoldersEditor();
	// Remove the disabled function on the form
	fileEngine.remove_disabled;	
	// Assign a checkall check box trigger
	fileEngine.setCheckAll($('.checkAll')).checkAll($('.checkItem'),$('#check_all')).propigateCheck();
	// Listen for escape key and fade out wrapper
	fileEngine.keyUpListener($('#nbr_textloader'));
	// Delete file(s) via ajax
	fileEngine.formAjaxer($("#filedelete"),'/ajax/edit.folderfiles.php');
});
</script>
<?php	
		$data	=	ob_get_contents();
		ob_end_clean();
		return $data;
	}