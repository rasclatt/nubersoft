<?php
if(!function_exists("is_admin"))
	return;
	
if(!is_admin())
	return;

if(!empty($file_info)) {
?>
<style>
.te-brackets,
.te-ie,
.te-arr-wrp,
.te-arr,
.te-par,
.te-quote,
.te-str,
.te-semic,
.te-tag	{
	font-family: inherit;
}

.te-brackets	{
	color: blue;
}
.te-markup	{
	background-color: #E7E7E7;
	padding: 5px;
	margin-bottom: 3px;
	display: inline-block;
	font-family: Courier;
	font-size: 14px;
	width: auto;
	min-width: none;
}
.te-ie	{
	color: green;
}
.te-arr-wrp	{
	color: #3366FF;
}
.te-arr	{
	color: #666;
}
.te-par	{
	color: #000;
}
.te-quote,
.te-tag	{
	color: orange;
}
.te-str	{
	color: blue;
}
.te-semic	{
	color: #900;
}
</style>
		<div class="editor-desc-wrap">
			<table cellpadding="0" cellspacing="0" border="0">
				<?php
						if(!empty($file_info['title']) || !empty($file_info['description'])) { ?>
				<tr>
					<td class="editor-desc-h">
						<h2><?php
								if(!empty($file_info['title'])) {
										echo Safe::encodeSingle($file_info['title']);
										unset($file_info['title']);
									}
								else
									echo "Title Unavailable."; ?></h2>
					</td>
				</tr>
				<tr>
					<td class="editor-desc-p">
						<p><?php
						
								if(!empty($file_info['description'])) {
										$desc	=	Safe::decodeForm(str_replace("`","",preg_replace_callback('/\`([^\`]{1,})\`/','markup_temp',$file_info['description'])));
										echo '<div style=" line-height: 20px; font-size: 14px; max-width: 800px; margin-bottom: 20px;">'.nl2br(preg_replace('!//([^\*\r\n]{1,})!','<span style="color: #888;">$1</span>',$desc))."</div>";
										unset($file_info['description']);
									}
								else
									echo "Description unavailable."; ?></p>
					</td>
				</tr>
				<?php		}
						if(!empty($file_info)) {
								foreach($file_info as $key => $value) {
										$altdesc	=	trim(preg_replace_callback('/\`([^\`]{1,})\`/','markup_temp',Safe::encodeSingle($value))); ?>
				<tr>
					<td class="editor-alt-settings">
						<div style=" line-height: 20px; font-size: 14px; max-width: 800px; margin-bottom: 20px;">
							<h3><?php echo ucwords(str_replace("_"," ",$key)); ?></h3><?php echo nl2br(preg_replace('!//([^\*\r\n]{1,})!','<span style="color: #888;">//$1</span>',str_replace("`","",$altdesc))); ?>
						</div>
					</td>
				</tr>
								<?php	}
								} ?>
			</table>
		</div>
			<?php		} ?>
		<div class="fullscreen-wrap">
			<form method="post" action="" id="text-editor" class="dragonit">
				<div style="display: inline-block; clear: both; float: left; text-align: center; width: 100%; margin: 30px 0 0 0;">
					<div style="border-top: 1px solid #CCC; border-left: 1px solid #888; border-right: 1px solid #666; padding: 10px 20px; width: 80%; border-top-left-radius: 4px; border-top-right-radius: 4px; background-color: #EBEBEB; background: linear-gradient(#EBEBEB,#CCC); font-size: 16px; text-shadow: 1px 1px 2px #FFF; text-align: center; margin: 0 auto;"><b>VIEWING:</b> { <?php echo $viewing." (".number_format(str_pad(($filesize / 1024),3,0,STR_PAD_RIGHT),3)."Kb)"; ?> }<div class="text-editor-toggle">FULL SCREEN</div><?php if($filesize != 0) { ?><a class="editor-go-link" href="<?php echo $file_raw; ?>" target="_blank">GO</a><?php } ?></div>
				</div>
				<span class="nodrag nondrag non-drag">
				<input type="hidden" name="typewriter" value="true" />
				<input type="hidden" name="filename" value="<?php echo urlencode($filename); ?>" />
				<textarea name="content" class="textarea text-editor"<?php if(!isset($data)) echo  ' placeholder="File not found"'; ?> id="sendtotype"><?php if(isset($data)) echo Safe::encodeSingle($data); ?></textarea>
				<div style="display: inline-block; width: 100%; clear: both; float: left; text-align: center;">
					<?php 
					$filter[]	=	'api.php';
					$filter[]	=	'dbcreds.php';
					$filter[]	=	'config-client.php'; ?>

					<label style="color: #<?php echo (in_array($viewing,$filter))? "666":"FFF"; ?>; font-size: 14px;">
						<input type="checkbox" name="delete"<?php if(in_array($viewing,$filter) || preg_match('/function\.|class\./',$viewing)) { ?> disabled<?php } ?> /> DELETE?
					</label>
					<div class="nbr_button" style="margin: 20px auto; float: none; clear: none; display: inline-block;"><input disabled="disabled" type="submit" name="save_file" value="SAVE" /></div>
				</div>
				</span>
			</form>
		</div>
		<!--
		DROP SPOT FOR AJAX CODE
		-->
		<div id="show-code" style="position: absolute; min-width: auto; text-align: left; resize: both; overflow: auto; background-color: #EBEBEB; border-radius: 3px; border: 1px solid #CCC; cursor: move; box-shadow: 1px 1px 3px #333; margin: 10px; padding: 10px; font-size: 16px; color: #333; text-shadow: 1px 1px 2px #FFF;" class="dragonit">SCRIPT WINDOW::DRAG TO MOVE</div>