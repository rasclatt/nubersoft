<?php
/*Title: app_text_editor()*/
/*Description: This function is responsible for the creation of the `Text Editor` in the `AdminTools`. It does require `is_admin()` to activate. Settings can include `title`, which adds a custom header, `class` for wrapping the editor, and `default` which is the default file to load.*/
/*Example: 

`autoload_function('app_text_editor');
app_text_editor();`
*/
use \Nubersoft\nApp as nApp;

function app_text_editor($settings = false)
	{
		$nApp	=	nApp::call();
		if(!empty($_REQUEST['page_editor'])) {
			if(!$nApp->isAdmin())
				return;
		}
		
		$settings['title']		=	(!empty($settings['title']))? $settings['title']:"Text Editor";
		$settings['class']		=	(!empty($settings['class']))? ' class="'.$settings['class'].'"':"";
		$settings['default']	=	(!empty($settings['default']))? $settings['default']:$nApp->toSingleDs($nApp->getCacheFolder().DS.'config-client.php');
		$file					=	(!empty($_REQUEST['load_page']))? urldecode(nApp::call('Safe')->decode(base64_decode($_REQUEST['load_page']))):$settings['default'];
		$TextEditor				=	new \nPlugins\Nubersoft\TextEditor(); ?>

<div id="text-editor-wrapper"<?php echo $settings['class']; ?>>
<table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
	<tr>
		<td style="vertical-align: top; padding: 20px;">
			<h1><?php echo $settings['title']; ?></h1>
			<h2>Assets</h2>
			<div class="text-edit-folderspread text-edit-fs-client">
				<?php $TextEditor->FileList(); ?>
			</div>
			<h2>Functions</h2>
			<div class="text-edit-folderspread">
				<?php $TextEditor->FileList(NBR_FUNCTIONS); ?>
			</div>
			<h2>Classes</h2>
			<div class="text-edit-folderspread">
				<?php $TextEditor->FileList(NBR_CLASS_CORE); ?>
			</div>
			<?php if(isset($nApp->getDataNode('site')->temp_folder)) { ?>
			<h2>Temp (HIDDEN)</h2>
			<div class="text-edit-folderspread">
				<?php $TextEditor->FileList($nApp->getDataNode('site')->temp_folder); ?>
			</div>
			<?php }
			if(isset($nApp->getDataNode('site')->cache_folder)) { ?>
			<h2>Cache (HIDDEN)</h2>
			<div class="text-edit-folderspread">
				<?php $TextEditor->FileList($nApp->getDataNode('site')->cache_folder); ?>
			</div>
			<?php } ?>
		</td>
		<td style="vertical-align: top;">
		<?php $TextEditor->Form($file); ?>
		</td>
	</tr>
</table>
		
</div>
<script>
$(".text-editor-toggle").click(function() {
		$(".fullscreen-wrap").css({"position":"absolute","top":"0","right":"0","bottom":"0","left":"0","z-index":"10000000000"});
		$(".text-editor").css({"position":"relative","top":"0","right":"0","left":"0"});
		$(".text-editor-toggle").html('<span onClick="window.location=\'?<?php echo create_query_string(false,$_GET); ?>\'">CLOSE</span>');
	});
</script>
		<?php
	}