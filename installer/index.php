<?php
include_once(__DIR__.'/config.php');
AutoloadFunction("site_url,render_element_css,render_element_js,default_jQuery,site_url");
$tCount	=	false;
// Already installed
$_installed	=	(is_file(NBR_ROOT_DIR.'/../client_assets/settings/dbcreds.php'));
if($_installed) {
	if(!is_admin())
		die('<h2>You must be logged in as an administrator</h2>');
	
	$tCount	=	nApp::getTables();
}
else {
	AutoloadFunction("is_loggedin");
	if(!is_loggedin()) {
		function autologin_user($array)
			{
				if(!is_array($array))
					return false;
					
				foreach($array as $key => $value)
					$_SESSION[$key]	=	$value;
			}
		
		autologin_user(array('first_name'=>'Guest','email'=>'unknown','usergroup'=>2,'username'=>'guest'));
	}
}
?><!DOCTYPE>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US" >
<head>
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
<title>nUberSoft Installer</title>
<meta name="description" content="nUberSoft Installer" />
<meta name="ROBOTS" content="INDEX, FOLLOW" />
<meta name="viewport" content="width=device-width">
<?php echo str_replace('/../','/',render_element_css(__DIR__."/../".basename(__DIR__).'/css/')).PHP_EOL; ?>
<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,700" rel="stylesheet" type="text/css">
<?php echo default_jQuery().PHP_EOL; ?>
<?php echo str_replace('/../','/',render_element_js(__DIR__."/../".basename(__DIR__).'/js/')).PHP_EOL; ?>
<style>
*,input,select,a	{
	font-family: 'Roboto', sans-serif !important;
	font-weight: 300 !important;
}
h1,h2,h3,h4	{
	font-weight: 700 !important;
}
div.formButton input {
	padding: 5px;
	border-radius: 5px;
	border: 1px solid #FFF;
}
div.instWrap {
	display: inline-block;
	padding: 30px;
	border: 10px solid #FFF;
	background-color: #EBEBEB;
	box-shadow: 1px 1px 8px rgba(0,0,0,0.5);
	margin: 60px 0;
}
a	{
	text-decoration: none;
}
label	{
	text-shadow: none !important;
	color: #333 !important;
}
.nbr_general_form,body	{
	text-align: center !important;
}
body	{
	background-color: #39302E;
}
ul.installer,
ul.installer_final	{
	list-style: none;
	padding: 0;
	margin: 0;
	display: table;
	width: 100%;
}
.installer_final td:first-child	{
	font-family: 'Roboto', sans-serif !important;
	font-weight: 700 !important;
}
.installer_final td	{
	border-bottom: 1px solid #CCC;
	padding: 5px;
}
ul.installer li,
.installer_final	{
	display: table-cell;
	text-align: left;
	width: 50%;
	text-align: left;
	padding: 10px 5px 5px 5px;
	vertical-align: top;
}
.installer_final {
	width: 94% !important;
	display: table-row !important;
}
ul.installer li input,
.installer_final input	{
	margin: 0 !important;
	border-radius: 4px !important;
	width: auto !important;
}
.nbr_login_window	{
	border: 1px solid !important;
	max-width: 500px !important;
	width: auto !important;
	display: block !important;
}
.active	{
	overflow: hidden;
	opacity: 0.5;
	cursor: not-allowed;
	background-color: #222;
	filter: blur(3px);
	-moz-filter: blur(3px);
	-o-filter: blur(3px);
	-webkit-filter: blur(3px);
}
.left-just	{
	text-align: left;
}
h1	{
	margin: 30px 0 0 0 !important;
}
p	{
	font-size: 16px !important;
	line-height: normal !important;
}
.invisisize	{
	display: none;
}
p.empty	{
	color: red;
}
#boto-status	{
	display: none;
}
</style>
<script>
var nInstaller	=	{
		
		response:	"",
		formInfo:	{},
		
		ajax: function(url,data,action,objStep)
			{	
				$.ajax({
						beforeSend: function() {
							$("body").addClass("active");	
						},
						url: url,
						type: 'post',
						data: { app: data, action: action },
						success: function(response) {
							nInstaller.response	=	response;
							nInstaller.processStep(objStep);
						}
				});
			},

		getStep: function(sVal)
			{
				this.ajax('core.ajax/ajax.dispatcher.php',{ step: sVal, setup: this.formInfo.serialize() },'get_step',sVal);
			},

		processStep: function(sVal)
			{
				this.makeForm($("#install"));
			},

		makeForm: function(obj)
			{
				obj.html(this.response);
				$("body").removeClass("active");
			},

		clearForm: function(sVal)
			{
				this.ajax('core.ajax/ajax.dispatcher.php',{ step: sVal },'clear_form',sVal);
			}
	}
	
$(document).ready(function() {
	$("#install").on('click','input[name=\'next\']',function(v) {
		v.preventDefault();
		
		var	getAction		=	$(this).data('action');
		var nextStep		=	$(this).data('nextstep');
		nInstaller.formInfo	=	$("#install");
		
		if(getAction != undefined) {
			console.log(getAction);
			if(getAction == 'clearall') {
				nInstaller.clearForm(nextStep);
				return false;
			}
		}
		
		nInstaller.getStep(nextStep);
	});
});
</script>
</head>
<body>
	<div id="tester"></div>
    <div id="fader">
        <div class="nbr_login_window" style="padding-bottom: 0;">
            <img src="<?php echo site_url(); ?>/installer/images/default.png" style="max-width: 250px;" />
			<div class="nbr_general_form">
				<form action="" method="post" id="install">
            <?php
			if($_installed) { ?>
					<div>
						<label>A database connection file has been located</label>
						<a class="nbr_button" href="<?php echo site_url(); ?>">Home</a>
					</div>
					<div class="nbr_button" id="step">
						<label>CLICK TO <?php echo ($_installed)? "RE" : ""; ?>INSTALL</label>
						<input type="submit" name="next" data-nextstep="1" value="<?php echo ($_installed)? "RE" : ""; ?>INSTALL" />	
					</div>
					<?php if(!$tCount) { ?>
					<input type="hidden" name="purge" value="1" />
					<?php } else { ?>
					<label>CLEAN INSTALL? <input type="checkbox" name="purge" value="1" /></label>
					<?php } ?>
			<?php }
            else { ?>
					<?php if(!$tCount) { ?>
					<input type="hidden" name="purge" value="1" />
					<?php } ?>
					<div class="nbr_button">
						<input type="submit" name="next" data-nextstep="1" value="<?php echo ($_installed)? "RE" : ""; ?>INSTALL" />
					</div>
			<?php } ?>
				</form>
				<div class="hidePostInstall" style="font-size: 16px; line-height: 22px; color: #333; padding: 20px 0;">If you wish to cancel, return to the begining of the installer and click "CLEAR ALL." Immediately close out.</div>
			</div>
        </div>
    </div>
</body>
</html>