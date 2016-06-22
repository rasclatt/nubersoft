<?php
include_once(__DIR__.'/../config.php');
if(!is_admin())
	die('<h2>You must be logged in as an administrator</h2>');

include_once(__DIR__.'/function.helpStep.php');
$creds			=	normalizeCreds($creds);
$allCreds		=	array_merge($creds['api'],$creds['db']);
?>		<div class="left-just">
			<input type="hidden" name="action" value="get_step" />
			<h1><?php echo (install_active())? 'Review' : 'Zis Vill Note Doo!'; ?></h1>
			<p><?php echo (install_active())? 'Please review all settings.' : 'You can not install without settings.'; ?></p>
		</div>
<?php
?>		<table class="installer_final" cellpadding="0" cellspacing="0" border="0">
<?php 	if(install_active()) {
			foreach($allCreds as $key => $value) {
?>			<tr>
				<td>
					<p<?php echo (empty($value))? ' class="empty"':''; ?>><?php echo str_replace("N_","nUberSoft ", ucwords($key)); ?></p>
				</td>
				<td>
					<p><?php echo $value = Safe::decOpenSSL($value,$_SESSION['install_key']); ?></p>
					<input type="hidden" name="setup[<?php echo $key; ?>]" value="<?php echo $value; ?>" />
				</td>
			</tr>
<?php		}
		}
?>		</table>
		<ul class="installer">
			<li>
				<div class="nbr_button"><input type="submit" name="next" data-nextstep="2" value="BACK" /></div>
<?php
	if(install_active()) {
			$isOn = (isValidConnect(parseCreds($creds['db'])))? "n" : "ff";
?>				
				<div class="nbr_button"><input type="submit" name="next" data-nextstep="4" value="Install App" /></div>
			<div id="boto-status">
				<div style="padding: 10px; text-align: center; background-color: #CCC; border-radius: 3px; float: right; display: inline-block; font-size: 12px;"><img src="images/db_o<?php echo $isOn; ?>.png" style="max-height: 54px; margin: 0 auto 5px auto;" /><br />DB STATUS</div>
			</div>
<?php	}
?>
			</li>
		</ul>
<script>
$(document).ready(function() {
	$("#boto-status").css({"display":"inline-block","float":"right"}).hide().delay(1000).fadeIn();
});
</script>