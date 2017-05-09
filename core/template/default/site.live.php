<?php if(!function_exists("AutoloadFunction")) return; ?><!DOCTYPE html>
<head>
<meta charset="utf-8" />
<meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
<meta name="viewport" content="width=device-width">
<title>Site Offline</title>
<link rel="stylesheet" href="/css/default.css" />
<link rel="stylesheet" href="/css/admintools.css" />
<link href='https://fonts.googleapis.com/css?family=Carrois+Gothic' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Zeyada' rel='stylesheet' type='text/css'>
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
<link rel="SHORTCUT ICON" HREF="/favicon.png">
<?php
AutoloadFunction('default_jQuery,site_valid');
echo default_jQuery(true);
?>
<script src="/js/onthefly.js"></script>
<style>
*	{
	font-family: 'Carrois Gothic', sans-serif;
}
p, h1 {
	text-shadow: 1px 1px 2px #FFFFFF;
}
.cursive p {
	font-family: 'Zeyada', cursive;
	text-align: center;
	font-size: 20px;
}
h1 {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	text-align: center;
}
div.postit {
	width: 257px;
	height: 207px;
	padding: 100px 25px 0 25px;
	margin-top: 60px;
	margin-left: auto;
	margin-right: auto;
	background-image: url(/images/core/offline.png);
	background-position: center;
	background-repeat: no-repeat;
	text-shadow: 1px 1px 2px #FFFFFF;
}
.rotate {
	width: 100%;
	text-align: center;
	-webkit-transform: rotate(-5deg);
	-moz-transform: rotate(-5deg);
	-ms-transform: rotate(-5deg);
	-o-transform: rotate(-5deg);
}
#admin {
	cursor: pointer;
	text-align: center;
	font-size: 12px;
	margin-bottom: 10px;
	color: #888;
}
#admin_panel {
	display: none;
	text-align: center;
}
#admin_panel div.admin_panel_cont {
	display: inline-block;
	margin: 0 auto;
	padding: 20px;
	background-color: #EBEBEB;
	border: 10px solid #FFF;
	box-shadow: 1px 1px 8px rgba(0,0,0,0.5);
}
#admin_panel input {
	padding: 8px 10px;
	font-size: 18px;
	float: left;
	clear: left;
}
#admin_panel label {
	font-size: 12px;
	margin-bottom: 8px;
	margin-top: 15px;
	clear: left;
	float: left;
}
.inputWrap	{
	 display: inline-block !important;
	 padding: 0 !important;
	 margin: 0 !important;
	 border: none !important;
	 width: 100% !important;
	 box-shadow: none !important;
}
</style>
</head>
<body>
<div style="margin: 60px auto 60px auto; width: 500px; display: block;">
	<center>
		<h2 style=" font-family: arial, sans-serif; text-shadow: 3px 3px 8px #888888; text-align: center;"><?php echo site_url(); ?></h2>
		<div class="postit dragonit">
			<div class="rotate cursive">
				<h1>Stand by.</h1>
				<p><?php AutoloadFunction('get_errorpage_msg'); echo get_errorpage_msg(); ?></p>
			</div>
		</div>
	</center>
<?php 
	$resetApp		=	NuberEngine::callPlugin("core")->initApp("reset.password");
	// Set the default to remote login being false
	$_remote		=	false;
	$first_run		=	false;
	// See if the site has a valid database connection
	$siteEnabled	=	nApp::siteValid();
	// If site has a valid database
	if($siteEnabled) {
		$count		=	nQuery()->select("COUNT(*) as count")->from("users")->fetch();
		$first_run	=	($count[0]['count'] == 0);
		// Check to see that the site has users in the user table
		if($first_run)
			// If not, the set the remote login to true
			$_remote	=	true;
	}
	// If site does not have valid connection, the set the remote login to true
	else
		$_remote	=	true;
			
	if($resetApp->appValid())
		echo $resetApp->toPage();
	else {
		
		
		$_upass		=	(isset($_incidental['login']['mismatch']));

		if($_remote || nApp::adminRestrict()) {
?>	<div id="adminWrap">
		<div id="admin" onclick="ShowHide('#admin_panel','','fade')">
			Admin Login
		</div>
		<div id="admin_panel">
			<div class="admin_panel_cont">
				<?php

?>
                	<p><?php echo ($_remote)? 'Please put in your <span style="color: red;">nUberSoft</span>':'Put in your site'; ?> Username and Password</p>
<?php if($_upass) {
?>					<p><span style="color: red; font-style: oblique;">Wrong login information</span></p>
<?php } echo $first_run;
?>					<form id="<?php echo ($first_run)? 'first_run' : 'remote'; ?>" enctype="application/x-www-form-urlencoded" method="post">
<?php 				if($first_run) {
?>						<input type="hidden" name="token[first_run]" value="<?php echo nApp::setToken('first_run'); ?>" />
                    	<input type="hidden" id="action" name="action" value="first_run" />
<?php 				}
					elseif($_remote) {
?>						<input type="hidden" id="action" name="action" value="login_remote" />
						<label for="apikey">nUberSoft apikey</label>
						<input type="apike" id="apikey" name="apikey" value="" />
						<?php	$_domain	=	(isset($_SERVER['DOMAIN_NAME_REAL']))? $_SERVER['DOMAIN_NAME_REAL']: $_SERVER['HTTP_HOST']; ?>
                        <input type="hidden" id="domain" name="domain" value="<?php echo $_domain; ?>" />
<?php				}
					else {
?>						<input type="hidden" name="token[login]" value="<?php echo $token = nApp::nToken()->setToken('login'); ?>" />
                    	<input type="hidden" id="action" name="action" value="login" />
<?php 				}
?>						<div class="inputWrap">
							<label for="username">Username</label>
							<input type="text" id="username" name="username" />
							<label for="password">Password</label>
							<input type="password" id="password" name="password" />
						</div>
						<div class="inputWrap">
                            <span class="nbr_button"><input disabled="disabled" type="submit" name="login" value="Login" style="margin-right: 0px; margin-top: 10px;" /></span>
						</div>
                    </form>
					<p style="font-size: 12px; cursor: pointer;" class="ajaxDispatcher" data-returned="html" data-sendto="#adminWrap" data-action="iforgot" data-senddata='<?php echo json_encode(array("name"=>"command","value"=>"forgot_pass")); ?>' data-senddataas="json">Forgot password?</p>
			</div>
		</div>
<?php		}
		}
?>
	</div>
</div>
</body>
</html>