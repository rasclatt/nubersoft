<?php
include_once(__DIR__.'/../config.php');
if(!is_admin())
	die('<h2>You must be logged in as an administrator</h2>');

include_once(__DIR__.'/function.helpStep.php');
?>
		<div class="left-just">
			<input type="hidden" name="action" value="get_step" />
			<h1>Database Credentials</h1>
			<p>Fill out your database connection information. A MySQL database is required.</p>
		</div>
		<ul class="installer">
			<li>
				<label>Username</label>
				<input type="text" name="setup[username]" placeholder="MySQL Compatable Username" value="<?php echo (!empty($creds['db']['username']))? $creds['db']['username'] : getVal('username','post'); ?>" />
			</li>
			<li>
				<label>Password</label>
				<input type="text" name="setup[password]" placeholder="MySQL Compatable Password" value="<?php echo (!empty($creds['db']['password']))? $creds['db']['password'] : getVal('password','post'); ?>" />
			</li>
		</ul>
		<ul class="installer">
			<li>
				<label>Host</label>
				<input type="text" name="setup[host]" placeholder="Host Name" value="<?php echo (!empty($creds['db']['host']))? $creds['db']['host'] : getVal('host','post'); ?>" />
			</li>
			<li>
				<label>Database Name</label>
				<input type="text" name="setup[database]" placeholder="Database Name" value="<?php echo (!empty($creds['db']['database']))? $creds['db']['database'] : getVal('database','post'); ?>" />
			</li>
		</ul>
		<ul class="installer">
			<li>
				<div class="nbr_button"><input type="submit" name="next" data-nextstep="1" data-action="clearall" value="CLEAR ALL" /></div>
			</li>
			<li>
				<div class="nbr_button"><input type="submit" name="next" data-nextstep="2" value="NEXT" /></div>
			</li>
		</ul>
<script>
$(document).ready(function() {
	$("input").keyup(function(k) {
		if(k.keyCode == 37 || k.keyCode == 38 || k.keyCode == 39 || k.keyCode == 40 || k.keyCode == 16)
			return false;
			
		var thisIn	=	$(this);
		var thisVal	=	thisIn.val();
		var	strRep;
		switch(thisIn.attr("name")) {
			case('setup[username]'):
				strRep	=	thisVal.replace(/[\s]/g,"_");
				break;
			case('setup[password]'):
				strRep	=	thisVal.replace(/[\s]/g,"_");
				break;
			case('setup[database]'):
				strRep	=	thisVal.replace(/[^a-zA-Z0-9_.-]/g,"");
				break;
			case('setup[host]'):
				strRep	=	thisVal.replace(/[^a-zA-Z0-9_.-]/g,"");
				break;
		}
		
		thisIn.val(strRep);	
	});
});
</script>