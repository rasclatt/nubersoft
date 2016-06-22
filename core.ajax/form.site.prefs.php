<?php
include_once(__DIR__.'/../config.php');
if(!is_admin())
	die('<h2 class="nbr_block_error">'.nApp::getAdminTxt().'</h2>');

AutoloadFunction('organize,get_directory_list');
// Get all prefs
$siteArr				=	nApp::getSitePrefs();
$headerArr				=	nApp::getHeader();
$footerArr				=	nApp::getFooter();
// Assign prefs
$site_vals['site']		=	$siteArr;
$site_vals['header']	=	$headerArr;
$site_vals['footer']	=	$footerArr;
$site_vals				=	Safe::to_object($site_vals);
// Get content from each
// Same as nApp::getSitePrefs()->content
$site					=	nApp::getSiteContent();
$header					=	nApp::getHeaderContent();
$footer					=	nApp::getFooterContent();
$nProcToken				=	nApp::nToken()->getSetToken('nProcessor',array('formsiteprefs',rand(1000,9999)),true);
// Sets and returns a token
nApp::saveSetting('nProcessor', $nProcToken);
?><div class="nbr_general_form" id="nbr_system_settings">
	<div class="nbr_general_cont">
		<div class="nbr_prefpane_content">
			<div class="nbr_hidewrap">
				<div id="nbr_prefpage_toolbar">
					<ul id="nbr_prefpane_hdbar">
						<li><img src="/core_images/core/gear.png" style="max-height: 40px;" /></li>
						<li>Site Preferences for: <?php echo $_SERVER['HTTP_HOST']; ?></li>
						<li>
							<ul class="nbr_prefpane_btn">
								<li class="nbr_reveal" data-reveal="nbr_site_pane" data-wrap="nbr_prefpanel_content" data-fx="slide">
									<div>Site Prefs</div>
								</li>
								<li class="nbr_reveal" data-reveal="nbr_head_pane" data-wrap="nbr_prefpanel_content" data-fx="slide">
									<div>Header Prefs</div>
								</li>
								<li class="nbr_reveal" data-reveal="nbr_foot_pane" data-wrap="nbr_prefpanel_content" data-fx="slide">
									<div>Footer Prefs</div>
								</li>
								<li class="nbr_closer_small" data-closewhat="nbr_system_setttings"></li>
							</ul>
						</li>
					</ul>
				</div>
				<div class="nbr_prefpanel_content" id="nbr_site_pane">
					<p style="font-size: 22px; color: #FFF; margin: 20px 0 0 0;">Site Preferences</p>
					<div class="nbr_general_form">
					<form id="uploadFAVICON" method="post" enctype="multipart/form-data" action="/core.ajax/ajax.dispatcher.php">
						<input type="hidden" name="token[ajax_edit_favicon]" value="<?php echo nApp::nToken()->setToken('ajax_edit_favicon'); ?>" />
						<input type="hidden" name="token[nProcessor]" value="<?php echo $nProcToken; ?>" />
						<input type="hidden" name="action" value="autoset" />
						<input type="hidden" name="use" value="ajax_edit_favicon" />
						<input type="hidden" name="nbr_dropspot" value="#fav_msg" />
						<input type="hidden" name="nbr_msg" value='<?php echo json_encode(array('success'=>"Icon uploaded!","fail"=>'Icon failed to upload.' ),JSON_FORCE_OBJECT); ?>' />
						<label>
							<div>FAVICON Upload</div>
							<div id="fav_msg"></div>
							<table cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td width="273" style="max-width: 200px; overflow: hidden;">
										<input type="file" name="upload" />
									</td>
									<td width="14" rowspan="4" style="min-width: 300px; vertical-align: top;">
										<div id="favIconList" data-action="autoset" data-returned="html" data-sendto="#favIconList" data-use="ajax_load_favicons"></div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="nbr_button"><input type="submit" value="UPLOAD" style="font-size: 14px; padding: 8px 16px; float: right;" /></div>
									</td>
								</tr>
							</table>
						</label>
					</form>
					<form id="uploadLogo" method="post" enctype="multipart/form-data" action="/core.ajax/ajax.dispatcher.php">
						<input type="hidden" name="token[ajax_edit_logo]" value="<?php AutoloadFunction("fetch_token"); echo fetch_token('ajax_edit_logo'); ?>" />
						<input type="hidden" name="action" value="autoset" />
						<input type="hidden" name="use" value="ajax_edit_logo" />
						<input type="hidden" name="nbr_dropspot" value="#logo_msg" />
						<input type="hidden" name="nbr_msg" value='<?php echo json_encode(array('success'=>"Logo uploaded! Reload page for change.","fail"=>'Logo failed to upload.' ),JSON_FORCE_OBJECT); ?>' />
						<label>
							<div>Company Logo Upload</div>
							<div id="logo_msg"></div>
							<table cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td width="273" style="max-width: 200px; overflow: hidden;">
										<input type="file" name="upload" />
									</td>
									<td width="14" rowspan="4" style="min-width: 300px; vertical-align: top;">
										<div id="logoList" data-action="autoset" data-returned="html" data-sendto="#logoList" data-use="ajax_load_logo"></div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="nbr_button"><input type="submit" value="UPLOAD" style="font-size: 14px; padding: 8px 16px; float: right;" /></div>
									</td>
								</tr>
							</table>
						</label>
					</form>
					</div>
					<form id="settings_site" method="post" action="<?php echo $_SERVER['HTTP_REFERER']; ?>">
						<input type="hidden" name="token[nProcessor]" value="<?php echo $nProcToken; ?>" />
						<input type="hidden" name="requestTable" value="system_settings" />
						<input type="hidden" name="page_element" value="settings_site" />
						<input type="hidden" name="command" value="settings" />
<?php if(!empty($site_vals->site->ID)) {
?>						<input type="hidden" name="ID" value="<?php echo $site_vals->site->ID; ?>" />
						<input type="hidden" name="unique_id" value="<?php echo $site_vals->site->unique_id; ?>" />
<?php					} 
						
						$dPrefs	=	get_directory_list(array("dir"=>__DIR__.'/form.site.prefs/'));
						
						foreach($dPrefs['host'] as $includes) {
							if(preg_match('/(\/site\.).*/',$includes))
								include($includes);
						}
?>

						<div class="nbr_div_button nbr_formadd" data-formadd="settings_site" data-formgroup="additional" style="font-size: 14px; padding: 8px 16px; float: right;">ADD CUSTOM SETTING</div>
						<?php
							if(!empty($site->custom)) { ?>
						<div style="display: inline-block; width: 100%;">
							<div style="padding: 10px; background-color: #555; margin: 20px 0;">
								<h2 style="color: #CCC;">Custom Settings</h2>
<?php								foreach($site->custom as $key => $vals) {
											$charLength	=	(strlen(Safe::decode($vals))+2);
?>
								<div class="form_custElemWrap">
									<div class="form_removethis" style="color: #CCC; background-color: #000; padding: 15px 8px; border-radius: 3px; font-size: 13px; display: inline-block; float: right;">DELETE</div>
									<span>
										<label style="margin: 0 0 10px 0; padding: 0; width: auto; border: none; float: left; display: inline-block;"><?php echo $custName = ucwords(str_replace("_"," ", $key)); ?>
											<div class="form-input">
												<input type="text" name="content[custom][<?php echo $key; ?>]" value="<?php echo $vals; ?>" placeholder="Custom Setting for <?php echo $custName; ?>" size="<?php echo ($charLength < 10)? 10: $charLength; ?>" style="width: auto;" />
											</div>
										</label>
									</span>
									<hr style="border: 1px dashed #CCC; width: 99%;" />
								</div>
<?php									}
?>
							</div>
						</div>
<?php							}
?>
						<div class="nbr_button">
							<input type="submit" name="update" value="SAVE" />
						</div>
					</form>
				</div>
				<div class="nbr_prefpanel_content" id="nbr_head_pane" style="display: none;">
					<p style="font-size: 22px; color: #FFF; margin: 20px 0 0 0;">Header Preferences</p>
					<form id="settings_head" method="post" action="<?php echo $_SERVER['HTTP_REFERER']; ?>">
						<input type="hidden" name="token[nProcessor]" value="<?php echo $nProcToken; ?>" />
						<input type="hidden" name="page_element" value="settings_head" />
						<input type="hidden" name="command" value="settings" />
						<?php if(isset($site_vals->header->ID)) { ?>
						<input type="hidden" name="ID" value="<?php echo $site_vals->header->ID; ?>" />
						<input type="hidden" name="unique_id" value="<?php echo $site_vals->header->unique_id; ?>" />
						<?php		} ?>
						<input type="hidden" name="requestTable" value="system_settings" />
						<label>	Style
							<div class="form-input">
								<textarea name="content[style]" placeholder="Styles will appear inside the <style> tag" class="textarea"><?php echo (isset($header->style))? $header->style:""; ?></textarea>
							</div>
						</label>
						<label>	Css
							<div class="form-input">
								<textarea name="content[css]" placeholder="Create style sheet links" class="textarea" ><?php echo (isset($header->css))? $header->css:""; ?></textarea>
							</div>
						</label>
						<label>	Javascript
							<div class="form-input">
								<textarea name="content[javascript]" placeholder="Create JavaScript" class="textarea" ><?php echo (isset($header->javascript))? $header->javascript:""; ?></textarea>
							</div>
						</label>
						<label>	Javascript Library Links
							<div class="form-input">
								<textarea name="content[javascript_lib]" placeholder="Create JavaScript Links" class="textarea" ><?php echo (isset($header->javascript_lib))? $header->javascript_lib:""; ?></textarea>
							</div>
						</label>
						<!--
						<label>	TinyMCE
							<div class="form-input">
								<textarea name="content[tinymce][value]" placeholder="Enable HTML Editor" class="textarea" ><?php echo (isset($header->tinymce->value))? $header->tinymce->value:""; ?></textarea>
							</div>
							<div class="form-input">
								<select name="content[tinymce][toggle]">
									<option value="off">OFF</option>
									<option value="on"<?php echo (isset($site->tinymce->toggle) && $site->tinymce->toggle == 'on')? ' selected="selected"':""; ?>>ON</option>
								</select>
							</div>
						</label>
						<label>	TinyMCE Trigger
							<div class="form-input">
								<input type="text" name="content[tinymce][hidden_task_trigger]" placeholder="Add at trigger word"  value="<?php echo (isset($header->tinymce->hidden_task_trigger))? $header->tinymce->hidden_task_trigger:""; ?>" />
							</div>
						</label>
						<label>	TinyMCE Task
							<div class="form-input">
								<input type="text" name="content[tinymce][hidden_task]" placeholder="Add at match word"  value="<?php echo (isset($header->tinymce->hidden_task))? $header->tinymce->hidden_task:""; ?>" />
							</div>
						</label>
						-->
						<label>	Favicons
							<div class="form-input">
								<textarea name="content[favicons]" placeholder="Link to favicon" id="faviconPath"><?php echo (isset($header->favicons))? $header->favicons:""; ?></textarea>
							</div>
						</label>
						<label>	Helpdesk 
							<div class="form-input">
								<input type="text" name="content[helpdesk]" value="" placeholder="content[helpdesk]" />
							</div>
						</label>
						<label>	Html
							<div class="form-input">
								<textarea name="content[html][value]" placeholder="Create HTML to replace tempate mast head" class="textarea" ><?php echo (isset($header->html->value))? $header->html->value:""; ?></textarea>
							</div>
							<div class="form-input">
								<select name="content[html][toggle]">
									<option value="off">OFF</option>
									<option value="on"<?php echo (isset($header->html->toggle) && $header->html->toggle == 'on')? ' selected="selected"':""; ?>>ON</option>
								</select>
							</div>
						</label>
						<div class="nbr_button">
							<input type="submit" name="update" value="SAVE" />
						</div>
					</form>
				</div>
			</div>
			<div class="nbr_prefpanel_content" id="nbr_foot_pane" style="display: none;">
				<p style="font-size: 22px; color: #FFF; margin: 20px 0 0 0;">Footer Preferences</p>
				<form id="setting_settings_foot" method="post" action="<?php echo $_SERVER['HTTP_REFERER']; ?>">
					<input type="hidden" name="token[nProcessor]" value="<?php echo $nProcToken; ?>" />
					<input type="hidden" name="page_element" value="settings_foot" />
					<input type="hidden" name="command" value="settings" />
					<?php if(isset($site_vals->footer->ID)) { ?>
					<input type="hidden" name="ID" value="<?php echo $site_vals->footer->ID; ?>" />
					<input type="hidden" name="unique_id" value="<?php echo $site_vals->footer->unique_id; ?>" />
					<?php		} ?>
					<input type="hidden" name="requestTable" value="system_settings" />
					<label>YouTube 
						<div class="form-input">
							<input type="text" name="content[youtube][value]" placeholder="YouTube Link" value="<?php echo (isset($footer->youtube->value))? $footer->youtube->value:""; ?>" />
							<select name="content[youtube][toggle]">
								<option value="off">OFF</option>
								<option value="on"<?php echo (isset($footer->youtube->toggle) && $footer->youtube->toggle == 'on')? ' selected="selected"':""; ?>>ON</option>
							</select>
						</div>
					</label>
					<label>Facebook
						<div class="form-input">
							<input type="text" name="content[facebook][value]" placeholder="Facebook Link" value="<?php echo (isset($footer->facebook->value))? $footer->facebook->value:""; ?>" />
							<select name="content[facebook][toggle]">
								<option value="off">OFF</option>
								<option value="on"<?php echo (isset($footer->facebook->toggle) && $footer->facebook->toggle == 'on')? ' selected="selected"':""; ?>>ON</option>
							</select>
						</div>
					</label>
					<label>Twitter
						<div class="form-input">
							<input type="text" name="content[twitter][value]" placeholder="Twitter Link" value="<?php echo (isset($footer->twitter->value))? $footer->twitter->value:""; ?>" />
							<select name="content[twitter][toggle]">
								<option value="off">OFF</option>
								<option value="on"<?php echo (isset($footer->twitter->toggle) && $footer->twitter->toggle == 'on')? ' selected="selected"':""; ?>>ON</option>
							</select>
						</div>
					</label>
					<label>Pinterest
						<div class="form-input">
							<input type="text" name="content[pinterest][value]" placeholder="Pinterest Link" value="<?php echo (isset($footer->pinterest->value))? $footer->pinterest->value:""; ?>" />
							<select name="content[pinterest][toggle]">
								<option value="off">OFF</option>
								<option value="on"<?php echo (isset($footer->pinterest->toggle) && $footer->pinterest->toggle == 'on')? ' selected="selected"':""; ?>>ON</option>
							</select>
						</div>
					</label>
					<label>Html
						<div class="form-input">
						<?php //echo printpre($footer); ?>
							<textarea name="content[html][value]" placeholder="Custom HTML" class="textarea" ><?php echo (!empty($footer->html->value))? $footer->html->value : ""; ?></textarea>
						</div>
						<div class="form-input">
							<select name="content[html][toggle]">
								<option value="off">OFF</option>
								<option value="on"<?php echo (isset($footer->html->toggle) && $footer->html->toggle == 'on')? ' selected="selected"':""; ?>>ON</option>
							</select>
						</div>
					</label>
					<div class="nbr_div_button nbr_formadd" data-formadd="setting_settings_foot" data-formgroup="footer" style="font-size: 14px; padding: 8px 16px; float: right;">ADD NEW SOCIAL MEDIA</div>
<?php						if(!empty($footer->social_media)) {
									$footer->social_media	=	Safe::to_array($footer->social_media);
?>
						<div style="display: inline-block; width: 100%;">
							<div style="padding: 10px; background-color: #555; margin: 20px 0;">
								<h2 style="color: #CCC;">Additional Social Media</h2>
<?php								foreach($footer->social_media as $key => $vals) {
?>
								<div class="form_custElemWrap">
									<div class="form_removethis" style="color: #CCC; background-color: #000; padding: 15px 8px; border-radius: 3px; font-size: 13px; display: inline-block; float: right;">DELETE</div>
									<span>
										<label style="margin: 0 0 10px 0; padding: 0; width: auto; border: none; float: left; display: inline-block;"><?php echo $custName = ucwords(str_replace("_"," ", $key)); ?></label>
											<div class="form-input">
												<input type="text" name="content[social_media][<?php echo $key; ?>][url]" value="<?php echo (!empty($vals['url']))? $vals['url'] : ""; ?>" placeholder="User URL for <?php echo $custName; ?>" size="30" style="width: auto;" />
												<input type="text" name="content[social_media][<?php echo $key; ?>][img]" value="<?php echo (!empty($vals['img']))? $vals['img'] : ""; ?>" placeholder="Image icon for <?php echo $custName; ?>" size="30" style="width: auto;" />
												<?php if(!empty($vals['img'])) { ?>
												<img src="<?php echo $vals['img']; ?>" style="max-height: 40px;" />
												<?php } ?>
											</div>
										
									</span>
									<hr style="border: 1px dashed #CCC; width: 99%;" />
								</div>
<?php									}
?>
							</div>
						</div>
<?php							}
?>
					<div class="nbr_button">
						<input type="submit" name="update" value="SAVE" />
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	$.ajaxSetup({ cache: false });
	var Autoload	=	Dispatcher.getInstance();
	var fUploader	=	Dispatcher.getInstance();
	var dispFile	=	"/core.ajax/ajax.dispatcher.php";
	
	fUploader.setUrl(dispFile);
	Autoload.setUrl(dispFile).ajax($('#favIconList')).ajax($('#logoList'));
	
	$("#uploadFAVICON").on('submit',function(e) {
		e.preventDefault();
		fUploader.initSuccess	=	function(doAct)
										{
											var imgPath	=	(doAct.path != undefined)? doAct.path : '';
											console.log(doAct);
											fUploader.ajax($('#favIconList'));
											if(imgPath != '')
												$("#faviconPath").val(imgPath);
										};
		fUploader.formData(this);
	});
	$("#uploadLogo").on('submit',function(e) {
		e.preventDefault();
		fUploader.initSuccess	=	function(doAct)
										{
											var imgPath	=	(doAct.path != undefined)? doAct.path : '/client_assets/images/logo/default.png';
											console.log(doAct);
											fUploader.ajax($('#logoList'));
											$("#companylogoPath").val(imgPath);
										};
		fUploader.formData(this);
	});
						
	$(this).on("click",".nbr_trigger",function(e) {
		var	$appeder	=	$("<div style=\"display: inline-block; width: 100%; float: left;\"><div class=\"nbr_loader\" id=\"qIconLoader\" style=\"height: 20px; width: 20px; margin: 0; display: inline-block; float: left;\"></div></div>");
		var	thisBtn		=	$(this);
		var thisAction	=	thisBtn.data('action');
		var thisId		=	thisBtn.data('sendto');
		$("#htHider").hide();
		
		$appeder.appendTo(thisBtn);
		
		if(thisAction == 'get_htaccess') {
				$.ajax({
						url: '/core.ajax/get.htaccess.php',
						type: 'post',
						data: { action: "get_htaccess" },
						success: function(response) {
								$(thisId).val(response);
								$appeder.remove();
								$("#htHider").show();
							}
				});
			}
		
		//console.log(e);	
	});
	
	
	$(".nbr_formadd").click(function() {
		var ThisButton	=	$(this);
		var FormName	=	ThisButton.data("formadd");
		var FormGrp		=	ThisButton.data("formgroup");
		
		var	Form		=	$("#"+FormName);
		
		if(FormName == 'settings_site') {	
			Form.append('<div class="new_formelem"><h3>New Element</h3><input type="text" placeholder="Name your new setting." maxlength="20" class="nameit" /><input type="text" name="content[placeholder][]" placeholder="Add a value for this setting." /></div>');
		}
		else if(FormName == 'setting_settings_foot') {
			Form.append('<div class="new_formelem"><h3>New Social Media</h3><input type="text" placeholder="Name your new link (ie. facebook)" maxlength="20" class="nameit" data-formmodel="soc_med" /><input type="text" name="content[placeholder][]" class="sm_url" placeholder="Type in your user URL for this listing" /><input type="text" name="content[placeholder][]" class="sm_img" placeholder="Type in the icon image URL for this listing" /></div>');
		}
	});
		
	// This is the function to add new settings in the preferences
	$(document).on('keyup',".nameit",function(e) {
		
		if(e.keyCode == 37 || e.keyCode == 38 || e.keyCode == 39 || e.keyCode == 40 || e.keyCode == 16)
			return false;
			
		var ThisButton	=	$(this);
		var SaveButton	=	ThisButton.next();
		var ThisNewName	=	ThisButton.val().replace(/[^0-9a-zA-Z\_]/g,"").toLowerCase();
		ThisButton.val(ThisNewName);
		var	ThisName;
		if(ThisButton.data("formmodel") != undefined) {
				ThisName	=	"content[social_media]["+ThisNewName+"]";
				SaveButton.prop("name",ThisName+'[url]');
				SaveButton.next().prop("name",ThisName+'[img]');
			}
		else {
				ThisName	=	"content[custom]["+ThisNewName+"]";
				SaveButton.prop("name",ThisName);
			}
		
	});
	
	$(".form_removethis").click(function() {
		$(this).parent().find("span").html('<div style="color: red; font-size: 16px;">Save to remove this setting. Reload settings to cancel.</div>');
		$(this).hide();
	});
	
	$(".nbr_hidewrap").fadeIn("slow");
	$("#nbr_system_settings").on("click",".nbr_closer_small", function(){
			$("#nbr_system_settings").fadeOut();
		});
});
</script>