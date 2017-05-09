<?php
if(!function_exists("is_admin"))
	return;

if(!is_admin())
	return;

	function DisplayFiles($values,$columns,$dropdowns)
		{
			AutoloadFunction('form_field,get_file_extension');
			foreach($columns as $cols) {
					$settings['values']		=	(is_array($values))? $values:"";
					$settings['name']		=	$cols;
					$settings['type']		=	($cols == 'file')? 'file':"fullhide";
					$settings['dropdowns']	=	(isset($dropdowns[$cols]))? $dropdowns[$cols]:false;
					$settings['label']		=	($settings['type'] == 'text' || $settings['type'] == 'select' || $settings['type'] == 'radio' || $settings['type'] == 'checkbox')? ucwords(strtolower(str_replace("_"," ",$cols))):'';
						
					echo form_field($settings);
				}
		}
		
$nProcToken	=	nApp::nToken()->setMultiToken('nProcessor','imagebucket');

?>	<div style="padding: 30px; box-shadow: 1px 1px 10px rgba(0,0,0,0.6);">
		<div style="display: inline-block; border: 1px solid #CCC; padding: 10px; margin: 5px; text-align: center;">
			<form enctype="multipart/form-data" method="post">
				<input type="hidden" name="requestTable" value="image_bucket" />
				<input type="hidden" name="token[nProcessor]" value="<?php echo $nProcToken; ?>" />
				<input type="hidden" name="thumbnail" value="1" />
				<?php DisplayFiles($values,$columns,$dropdowns,$nuber); ?>
				<?php DisplayFiles($values,$columns,$dropdowns,$nuber); ?>
				
				<label>
					KEEP NAME? <input type="checkbox" name="keep_name" value="1" />
				</label>
				<div class="login_button" style="margin:10px auto 0 auto; display: inline-block;"><input disabled="disabled" type="submit" name="add" value="UPLOAD" /></div>
			</form>
		</div>
	</div>
	<div style="padding: 30px; box-shadow: 1px 1px 20px rgba(0,0,0,0.6); display: inline-block; float: left;">
		<?php
		if($values == 0) { ?>
		<h2>No images in your library.</h2>
		<?php }
		else {
				foreach($values as $row) { ?>
	<div style="display: inline-block; float: left; margin: 5px; border: 1px solid #CCC; border-radius: 4px; padding: 10px;">
		<form enctype="multipart/form-data" method="post">
				<input type="hidden" name="thumbnail" value="1" />
				<input type="hidden" name="token[nProcessor]" value="<?php echo $nProcToken; ?>" />
			<table>
				<tr>
					<td>
			<?php
			$file	=	$row['file_path'].$row['file_name'];
			$kind	=	get_file_extension($row['file_name']);
						
			if($kind == 'pdf' || $kind == 'psd') {
				if(!is_file($mkthum = NBR_CLIENT_DIR."/thumbs/image_bucket/".$row['file_name']) && class_exists("Imagick")) {
					$imagick = new Imagick();
					$imagick->readImage(NBR_ROOT_DIR.$row['file_path'].$row['file_name']);
					$imagick->writeImage($mkthum);
				}
			}
							
			if(is_file(NBR_ROOT_DIR.$file)) {
					$default_img	=	$file;
					
					if(defined("NBR_THUMB_DIR") && is_file(NBR_THUMB_DIR."/image_bucket/".$row['file_name']))
						$default_img	=	str_replace(NBR_ROOT_DIR,"",NBR_THUMB_DIR."/image_bucket/".$row['file_name']);
						
					if(preg_match('/.mp4|.mpeg4|.ogg/i',$row['file_name'],$ext)) { ?>
					<video controls height="100">
						<source src="<?php echo $row['file_path'].Safe::encode($row['file_name']); ?>" type="video/<?php echo str_replace(".","",$ext[0]); ?>">
					</video>
						<?php }
					elseif(preg_match('/.mov|.mpg|.mpeg|.avi|.m4v/i',$row['file_name'],$ext)) { ?>
							<embed src="<?php echo $row['file_path'].Safe::encode($row['file_name']); ?>"></embed><?php
						}
					else {
						 ?>
				<div style="background-image: url('<?php echo $default_img; ?>'); background-repeat: no-repeat; background-color: #333; box-shadow: inset 0 0 8px #000; background-size: contain; background-position: center; height: 100px; width: 100px;"></div>
					<?php } ?>
			<?php } ?>
					</td>
					<td style="vertical-align: top;">
						<?php 
						$file_icn	=	"/images/ui/".$kind.".png";
						$icn 		=	(is_file(NBR_ROOT_DIR.$file_icn))? true:false;	
						if($icn) { ?><img src="<?php echo $bgicn = $file_icn; ?>" style="max-height: 40px;" /><?php } ?>
					</td>
					<td style="padding: 5px; vertical-align: top;">
							<h3 style="font-size: 16px;">File Name</h3>
						<div style=" width: 150px; overflow: auto;">
							<p style="font-size: 12px; "><?php echo $row['file_name']; ?></p>
						</div>
					</td>
				</tr>
			</table>
			<div class="more-opts">
				<div class="more-opts-btn">MORE OPTIONS</div>
				<div class="more-opts-panel hidethis">
					<?php DisplayFiles($row,$columns,$dropdowns,$nuber); ?>
					<label>
						DELETE? <input type="checkbox" data-toggle="<?php echo "del-".$row['ID']; ?>" class="del-check" name="delete" />
					</label>
					<label>
						KEEP NAME? <input type="checkbox" name="keep_name" value="1" />
					</label>
					<div class="login_button" style="margin:10px auto 0 auto; display: inline-block;"><input disabled="disabled" type="submit" id="<?php echo "del-".$row['ID']; ?>" name="update" value="UPDATE" /></div>
				</div>
			</div>
		</form>
	</div>
		<?php }
		} ?>
	</div>
<script>
	$(".del-check").click(function() {
			var Checker	=	$(this);
			var DataGet	=	Checker.data('toggle');
			var	DataVal	=	Checker.is(":checked");
			
			if(DataVal == true)
				$("#"+DataGet).val("DELETE?");
			else
				$("#"+DataGet).val("UPDATE");
		});
</script>