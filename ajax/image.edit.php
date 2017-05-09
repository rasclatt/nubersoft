<?php
	
	include_once(__DIR__.'/../config.php');
	ini_set("display_errors",1);
	error_reporting(E_ALL);
	// Check Admin is set
	if(!is_admin())
		return;
		
	AutoloadFunction("is_ajax_request");
	
	if(empty(nApp::getGet('table')) && empty(nApp::getPost('requestTable')))
		return;
	elseif(!is_ajax_request())
		return;
	
	AutoloadFunction('nQuery');
	$nubquery	=	nQuery();

	if(isset($_POST['change_name']) || isset($_POST['file_name'])) {
			$_POST['file_name']	=	preg_replace("/[^0-9\.\_\-a-zA-Z]/","",$_POST['file_name']);
			$local				=	$_POST['file_path'];
			$fullpath			=	NBR_ROOT_DIR.$local;
			$nfullpath			=	$fullpath.$_POST['file_name'];
			if(!isset($_POST['change_name'])) {
					AutoloadFunction('get_directory_list');
					$valid		=	get_directory_list(array("dir"=>NBR_ROOT_DIR.$_POST['file_path']));
					if(in_array($nfullpath,$valid['host'])) { ?>
					<script>
					$("#change_name_f").css({"color":"red"});
					</script>
						<?php }
					else { ?>
					<script>
					$("#change_name_f").css({"color":"#000"});
					</script>
					<?php }
				}
			else {
				if(!empty($_POST['file_name'])) {
					$ImageFactory	=	new ImageFactory(ImageFactory::LARGE_INPUT);
					
					if(is_file($fullpath.$_POST['old_name']) && rename($fullpath.$_POST['old_name'],$fullpath.$_POST['file_name'])) {
						if(defined('NBR_THUMB_DIR')) {
							$thumbdir		=	NBR_THUMB_DIR."/".$_POST['requestTable']."/";
							$thumb 			=	$thumbdir.$_POST['old_name'];
							$ImageFactory	->SearchLocation(NBR_THUMB_DIR)	// Compile files in thumb dir
											->SearchFor($thumbdir)		// Add a searchable file
											->Thumbnailer($fullpath.$_POST['file_name'], 150,150,$thumbdir.$_POST['file_name']);
						}
							
						$nubquery	->update($_POST['requestTable'])
									->set(array("file_name"=>$_POST['file_name']))
									->where(array("ID"=>$_POST["ID"]))
									->write();
						
						echo json_encode(array("success"=>true,"error"=>""));
					}
					else
						echo json_encode(array("success"=>false,"error"=>"File does not exist"));
				}
				else
					echo json_encode(array("success"=>false,"error"=>'File can not be saved empty.'));
			}
			
			exit;
		}
	
	$table		=	(!empty($_GET['table']))? trim($_GET['table']) : trim($_GET['requestTable']);
	$file		=	$nubquery	->select(array("ID","unique_id","file_path","file_name"))
								->from($table)
								->where(array("ID"=>$_GET['id']))
								->fetch();
	
	if(isset($_GET['delete'])) {
			$filename	=	NBR_ROOT_DIR.$file[0]['file_path'].$file[0]['file_name'];
			if(is_file($filename)) {
				unlink($filename);
				
				$thumb	=	NBR_CLIENT_DIR.'/thumbs/'.$file[0]['file_name'];
				if(is_file($thumb))
					unlink($thumb);
				
				$nubquery	->update($table)
							->set(array("file_path"=>"","file_name"=>"","file_size"=>""))
							->where(array("ID"=>$_GET['id']))
							->write();
				
				if(!is_file($filename)) {
?>							File deleted.
							<div style="padding: 30px; text-align: center; display: inline-block; min-width: 500px; width: 100%; height: 300px;">
								<a class="div_button" href="<?php echo $_SERVER['HTTP_REFERER']; ?>">CLOSE</a>
							</div>
							<script>
								$(".closer").css({"display":"none"});
								$("#delete-image").css({"display":"none"});
							</script>
<?php 			}
			}
		
			exit;
		}
	
	AutoloadFunction("download_encode");
?>
<style>
.nbr_modal_wrap	{
	display: none;
	position: absolute;
	top: 0;
	bottom: 0;
	left: 0;
	right: 0;
	background-color: rgba(255,255,255,0.85);
	padding: 30px;
	z-index: 1000000;
}
.nbr_to_cell	{
	display: table-cell;
	vertical-align: middle;
	position: absolute;
	top: 0;
	bottom: 0;
	list-style: none;
}
.image_edit_form	{
	text-align: left;
}
.image_edit_form a.div_button:link,
.image_edit_form a.div_button:visited	{
	width: auto;
	display: inline-block;
}
.image_edit_form input[type='text'],
.image_edit_form input[type='password']	{
	max-width: 400px;
	width: auto;
	float: left;
	font-size: 14px;
	border-color: #CCC;
}
#change_name input[type='text']	{
	width:90%;
	margin: auto;
}
.nbr_thumb_container	{
	background-size: contain;
	background-repeat: no-repeat;
	background-color: #FFF;
	background-position: center;
	width: 200px;
	height: 200px;
	display: inline-block;
	margin: 0 auto;
}
.nbr_modal_wrap .nbr_login_window	{
	max-width: 800px;
}
.nbr_modal_wrap label	{
	float: left;
	text-shadow: none;
	color: #333;
}
div.div_button,
div.div_button:link,
div.div_button:visited	{
	display: inline-block;
	width: auto;
}
.nbr_login_window	{
	width: auto;
	display: inline-block;
	max-width: 600px;
	text-align: center;
	padding: 0px;
}
ul.image_edit_containers	{
	display: inline-block;
	float: left;
	list-style: none;
	padding: 0;
	margin: 0px;
}
ul.image_edit_containers li.iec	{
	display: table-cell;
	vertical-align: top;
	padding: 10px;
	margin: 0px;
}
ul.nbr_as_cell	{
	display: table;
	float: none;
	list-style: none;
	padding: 0px;
	margin: 0px;
}
ul.nbr_as_cell li	{
	display: table-cell;
	list-style: none;
	text-align: center;
}
.opastic,
.opastic input	{
	opacity: 0.5;
}
.nbr_success	{
	padding: 10px 15px;
	background-color: green;
	border: 2px solid #FFF;
	box-shadow: 1px 2px 6px rgba(0,0,0,0.6);
	display: inline-block;
	margin: auto;
	color: #FFF;
	font-size: 16px;
}
</style>
	<div class="nbr_modal_wrap">
		<div class="nbr_login_window">
			<div style=" padding: 20px;" class="nbr_general_form image_edit_form">
		
		<?php
		if($file != 0) {
				$fileEnc	=	download_encode(array("file_id"=>$file[0]['ID'],"table_id"=>$table));
				$url		=	$file[0]['file_path'].Safe::encode($file[0]['file_name']);
				$ext		=	get_file_extension($file[0]['file_name']);
				$matchFile	=	preg_match('/.mp4|.mpeg4|.ogg/i',".".ltrim($ext,"."));
				
?>				<div style="background-color: #FFF; padding: 15px 15px 0 15px; display: inline-block;">
					<div class="login_fields login_fields_mod">
						<ul class="image_edit_containers">
							<li class="iec" style="border: 1px solid #CCC;<?php echo ($matchFile)? " background-color: #000;":""; ?>">
<?php						if($matchFile) { ?>
							<video controls height="100">
								<source src="<?php echo site_url().$url; ?>" type="video/<?php echo str_replace(".","",$ext); ?>" style="width: 100px;">
							</video>
<?php						}
							else {
?>								<div class="nbr_thumb_container" style="background-image: url('<?php echo $url; ?>');"></div>
<?php 						}
?>							</li>
							<li class="iec">
								<table style="display: block;">
									<tr>
										<td>
											<label for="direct_path">Direct Path</label>
											<input id="direct_path" type="text" value="<?php echo $file[0]['file_path'].$file[0]['file_name']; ?>" onClick="this.select()" />
										</td>
									</tr>
									<tr>
										<td>
											<label for="enc_path">Encrypted Path</label>
											<input id="enc_path" type="text" value="download.php?file=<?php echo $fileEnc; ?>" onClick="this.select()" />
										</td>
									</tr>
									<tr>
										<td>
											<a class="div_button" href="/download.php?file=<?php echo $fileEnc; ?>">DOWNLOAD (<?php echo number_format((filesize(NBR_ROOT_DIR.$url)/1040),1); ?>Kb)</a>
											<div id="delete-image" class="div_button" onClick="AjaxFlex('.nbr_login_window','/ajax/image.edit.php?id=<?php echo $file[0]['ID']; ?>&table=<?php echo $table; ?>&delete=true')">DELETE</div>
										</td>
									</tr>
								</table>
							</li>
						</ul>
						
						<form id="change_name" method="post">
							<input type="hidden" name="change_name" value="1" />
							<div style="display: inline-block; width: 100%; text-align: center;margin-top: 10px;">
								<div id="change_name_drop">
									<input type="text" name="file_name" placeholder="Change name" value="<?php echo $file[0]['file_name']; ?>" id="change_name_f" style="margin: 0 auto; float: none; text-align: center;" />
									<input type="hidden" name="file_path" value="<?php echo $file[0]['file_path']; ?>" />
									<input type="hidden" name="old_name" value="<?php echo $file[0]['file_name']; ?>" />
									<input type="hidden" name="requestTable" value="<?php echo $table; ?>" />
									<input type="hidden" name="ID" value="<?php echo $_GET['id']; ?>" />
									
								</div>
								<div style="display: inline-block; margin: auto;">
									<ul class="nbr_as_cell">
										<li class="nbr_fade_out"><div class="div_button_input opastic"><input type="submit" value="SAVE" disabled /></div></li>
										<li><div class="closer close-button div_button" id="image_edit_cancel">CANCEL</div></li>
									</ul>
								</div>
							</div>
						</form>
					</div>
				</div>
				<?php
			} ?>
			</div>
	</div>
</div>
<script>
$(document).ready(function() {
	$("body,html").addClass("no-scroll");
	$(".nbr_modal_wrap").fadeIn('fast').addClass("nbr_to_cell");
	$("html, body").animate({ scrollTop: 0 }, 'fast');
		
	$(".closer").click(function() {
			$(".nbr_modal_wrap").fadeOut('fast');
			$("body,html").removeClass("no-scroll");
		});
		
	function ChangeImage(FormSet,Action)
		{
			var NameField	=	FormSet.find("input[name='file_name']").val();
			var PathField	=	FormSet.find("input[name='file_path']").val();
			var TableField	=	FormSet.find("input[name='requestTable']").val();
			var IdField		=	FormSet.find("input[name='ID']").val();
			
			$.ajax({
				url: '/ajax/image.edit.php',
				type: 'post',
				data: (Action == 'key')?  { file_name: NameField, file_path: PathField, requestTable: TableField, ID: IdField }:FormSet.serialize(),
				success: function(response) {
					console.log(response);
						var SuccessMsg	=	JSON.parse(response);
						if(SuccessMsg.success == true) {
								$("#change_name_drop").html('<div class="nbr_success">File name changed successfully.</div>');
								$("#direct_path").val("File name update.");
								var ThisButton	=	FormSet.find("input[type='submit']");
								
								$(".nbr_fade_out").hide();
								$("#image_edit_cancel").html("CLOSE");
							}
					}
			});
		}
		
	var FormSet	=	$("#change_name");
	var	InputNm	=	FormSet.find("input[name='file_name']");
	
	$("#change_name_f").keyup(function() {		
			var ThisButton	=	FormSet.find("input[type=submit]");
			ThisButton.removeAttr("disabled");
			ThisButton.parent().removeClass("opastic")
		});

	FormSet.submit(function() {
		var FileNameVal	=	InputNm.val().replace(/[^a-zA-Z0-9\.\-\_]/g,"");
		InputNm.val(FileNameVal);
		ChangeImage(FormSet,'submit');
		return false;
	});
});
</script>