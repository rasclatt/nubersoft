<?php 
if($this->getPost('action') == 'nbr_install_database_credentials') {
	$workflow[]	=	array(
		'class'=>array(
			'name'=>'\Nubersoft\nObserverFirstRun',
			'method'=>'installDatabaseCredentials'
		)
	);
	
	$this->getHelper('nAutomator',$this)->doWorkflow($workflow);
	$this->getHelper('nRouter')->addRedirect('/');
}

echo $this->render(__DIR__.DS.'header.php') ?>
	<div style="display: inline-block; width: 100%; padding: 30px 0;">
		<div class="nbr_login_window">
			<img src="data:image/png;base64,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" style="max-height: 60px;" />
			<form method="post" action="#">
				<input type="hidden" name="action" value="nbr_install_database_credentials" />
				<div class="nbr_contain" style="text-align: left;">
					<label style="text-align: left; font-size: 13px; margin-top: 20px;">Database Host</label>
					<input type="text" name="database[host]" />
					<label style="text-align: left; font-size: 13px; margin-top: 20px;">Database Name</label>
					<input type="text" name="database[data]" />
					<label style="text-align: left; font-size: 13px; margin-top: 20px;">Database Username</label>
					<input type="text" name="database[user]" />
					<label style="text-align: left; font-size: 13px; margin-top: 20px;">Database Password</label>
					<input type="password" name="database[pass]" />
					<div class="nbr_button">
						<input type="submit" value="INSTALL" />
					</div>
				</div>
			</form>
		</div>
	</div>
<?php echo $this->render(__DIR__.DS.'footer.php') ?>