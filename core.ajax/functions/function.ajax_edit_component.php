<?php
function ajax_edit_component()
	{
		ob_start();
		
		if(!is_admin()) {
?>
<div id="temp-load" style="z-index:1000;background-color: #FFF;position: absolute;top:0;right:0;bottom:0;left:0;">
	<div class="loading">
		<div style="display: inline-block; max-width: 600px;text-align: center; padding: 20px; margin: 30px auto;">
			<p style="font-size: 20px;">Whoops!</p>
			<p>You must be logged in as an Administrator to view this page.</p>
			<div id="close_editor" style="float: none; margin: 20px auto;" class="d_login_button">CANCEL</div>
		</div>
	</div>
</div>
<?php			exit;
			}

		// Load functions
		AutoloadFunction('get_header,get_form_layout,organize,FetchUniqueId,process_requests,decode_serial,nQuery');
		$query	=	nQuery();
		$_table	=	(!empty(nApp::getGet('table')))? Safe::decOpenSSL(urlencode($_GET['table'])):'components';
		$query	=	$query	->select(array("ID","unique_id","content"))
							->from($_table)
							->where(array("ID"=>$_REQUEST['ID']))
							->fetch();
?>
<style>
div.login_button,
div.login_button input	{
	margin: 0;
}
div.d_login_button {
	background-color: #FFF;
	padding: 8px;
	font-size: 14px;
	border: 1px solid #888;
	text-align: center;
	background-repeat:repeat-x;
	width: 100px;
	margin: 15px;
	text-shadow: 1px 1px 3px #FFFFFF;
	cursor: pointer;
	float: right;
}
div.d_login_button:hover {
	background-color: #CCC;
}
</style>
				<div style="position: relative; top: 0; right: 0; bottom: 0; left: 0;background-color: #FFF; text-align: center;" id="nbr_graph_wrap">
					<div style="padding: 40px; text-align: center;">
						
<?php		
			if($query != 0) {
				$result	=	$query[0];
?>					<div style="display: inline-block; margin: 0 auto;">
						<h2>Graphical Editor</h2>
						<center>
						<div style="width:auto; display: inline-block; margin: auto; position: relative;">
							<form action="<?php echo (!empty($_SERVER['HTTP_REFERER']))? Safe::encodeSingle($_SERVER['HTTP_REFERER']) : "/"; ?>" method="post" enctype="multipart/form-data" id="page_editor">
								<input type="hidden" name="ID" value="<?php if(!empty($result['ID'])) echo $result['ID']; ?>" />
								<input type="hidden" name="unique_id" value="<?php if(!empty($result['unique_id'])) echo $result['unique_id']; ?>" />
								<input type="hidden" name="requestTable" value="<?php echo fetch_table_id($_table,nQuery()); ?>" />
								<input type="hidden" name="filter_request" value="true" />
								<textarea id="component-wysiwyg" name="content" style="background-color: #FFF;"><?php if(!empty($result['content'])) echo Safe::decode($result['content']); ?></textarea>
								<input type="hidden" name="<?php $function = (!empty($result['ID']))? 'update': 'add'; ?>" value="<?php echo $function; ?>" />
								<table cellpadding="0" cellspacing="0" border="0" style="float: right;">
									<tr>
										<td>
											<div class="nbr_button"><input disabled="disabled" type="submit" name="<?php echo $function; ?>" value="<?php echo strtoupper($function); ?>" style="padding: 8px 16px; font-size: 16px;" /></div>
										</td>
										<td>
											<div id="close_editor" class="d_login_button" onClick="window.location='<?php echo (!empty($_SERVER['HTTP_REFERER']))? Safe::encodeSingle($_SERVER['HTTP_REFERER']) : "/"; ?>'">CANCEL</div>
										</td>
									</tr>
								</table>
							</form>
						</div>
						</center>
					</div>
	<?php		}
			else {
?>	<div id="temp-load" style="background-color: #FFF;">
		<div class="loading">
			<div style="display: inline-block; max-width: 600px;text-align: center; padding: 40px; margin: 60px auto;">
				<p style="font-size: 20px;">Whoops! My Bad!</p>
				<p>You must first add a component in order to edit it.</p>
				<div id="close_editor" style="float: none; margin: 20px auto;" class="d_login_button nCloser" data-closewhat="#nbr_graph_wrap">CANCEL</div>
			</div>
		</div>
	</div>
<?php 		}
?>
			</div>
		</div>
<script type="text/javascript" src="/js/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">

$(document).ready(function() {
	
	tinyMCE.init({
		// General options
	   // mode : "textareas",
		mode : "exact",
		elements : "component-wysiwyg",
		theme : "advanced",

		content_css : "/css/default.css",
		plugins : "autolink,lists,spellchecker,pagebreak,layer,table,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Skin options
		skin : "o2k7",
		skin_variant : "silver",

		// Example content CSS (should be your site CSS)
	//	content_css : "css/example.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "js/template_list.js",
		external_link_list_url : "js/link_list.js",
		external_image_list_url : "js/image_list.js",
		media_external_list_url : "js/media_list.js",

		// Replace values for the template plugin
		template_replace_values : {
				username : "Some User",
				staffid : "991234"
		}
	});
	// Enable editor
	$("#page_editor").find("input[type=submit]").prop("disabled",false);
});
</script>
<?php	$data	=	ob_get_contents();
		ob_end_clean();
		
		return $data;
	}