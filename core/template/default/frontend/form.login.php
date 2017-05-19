<?php 
$resetApp		=	NuberEngine::callPlugin("core")->initApp("reset.password");
// Set the default to remote login being false
$_remote		=	false;
$first_run		=	false;
// See if the site has a valid database connection
$siteEnabled	=	$this->siteValid();
// If site has a valid database
if($siteEnabled) {
	$count		=	nquery()->select("COUNT(*) as count WHERE `usergroup` = ".NBR_SUPERUSER." OR `usergroup` = ".NBR_ADMIN)->from("users")->getResults();
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
	if($_remote || $this->adminRestrict()) {
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
?>						<input type="hidden" name="token[first_run]" value="<?php echo $this->setToken('first_run'); ?>" />
					<input type="hidden" id="action" name="action" value="first_run" />
<?php 				}
				elseif($_remote) {
?>						<input type="hidden" id="action" name="action" value="login_remote" />
					<label for="apikey">nUberSoft apikey</label>
					<input type="apike" id="apikey" name="apikey" value="" />
					<?php	$_domain	=	(!empty($this->getDataNode('_SERVER')->DOMAIN_NAME_REAL))? $this->getDataNode('_SERVER')->DOMAIN_NAME_REAL: $this->getDataNode('_SERVER')->HTTP_HOST; ?>
					<input type="hidden" id="domain" name="domain" value="<?php echo $_domain; ?>" /><?php echo $_domain; ?>
<?php				}
				else {
?>						<input type="hidden" name="token[login]" value="<?php echo $token = self::call('nToken')->setToken('login'); ?>" />
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