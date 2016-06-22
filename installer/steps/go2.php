<?php
include_once(__DIR__.'/../config.php');
if(!is_admin())
	die('<h2>You must be logged in as an administrator</h2>');
	
include_once(__DIR__.'/function.helpStep.php');
?>
		<div class="left-just">
			<input type="hidden" name="action" value="get_step" />
			<h1>Remote Credentials</h1>
			<p>Insert your nUberSoft credentials in order to fetch system updates automatically.</p>
		</div>
		<ul class="installer">
			<li>
				<label>nUberSoft Username</label>
				<input type="text" name="setup[n_username]" placeholder="Username" value="<?php echo (!empty($creds['api']['n_username']))? $creds['api']['n_username'] : getVal('n_username','session'); ?>" />
			</li>
			<li>
				<label>nUberSoft API Key</label>
				<input type="text" name="setup[n_apikey]" placeholder="API Key" value="<?php echo (!empty($creds['api']['n_apikey']))? $creds['api']['n_apikey'] : getVal('n_apikey','session'); ?>" />
			</li>
		</ul>
		<ul class="installer">
			<li>
				<label>nUberSoft PIN (Any 4 digits)</label>
				<input type="text" name="setup[n_pin]" placeholder="Database Name" value="<?php echo (!empty($creds['api']['n_pin']))? $creds['api']['n_pin'] : getVal('n_pin','session'); ?>" maxlength="4" />
			</li>
		</ul>
		<ul class="installer">
			<li>
				<div class="nbr_button"><input type="submit" name="next" data-nextstep="1" value="BACK" /></div>
				<div class="nbr_button"><input type="submit" name="next" data-nextstep="3" value="NEXT" /></div>
			</li>
		</ul>