<?php include(__DIR__.DS.'header.php'); ?>
	<div class="login_container">
		<div class="login_bkg" style="margin:30px auto; max-width: 500px; background-color: #EBEBEB;border: 5px solid #FFF; box-shadow: 0 0 10px #000; padding: 30px 30px 10px 30px;">
			<h3 class="nFont">You have no <code>registry.xml</code></h3>
			<p>This file is responsible for important core file autoloading to run your site. The registry file is located in the <code>[root]/client/settings/</code> folder. A default registry has been downloaded and saved to this location for you.</p>
			<p>You can edit the registry file to add or remove functionality to your web site.</p>
			<p>The registry also contains protection preferences for folders, so on login to the admin, click the <code>REBUILD HTACCES</code> & <code>REBUILD DEFINES</code> buttons and your security settings will build as well as some basic preferences.</p>
			<img src="/plugins/plugin.RefreshDefines/images/rebuild.png" style=" max-height: 80px;" /><img src="/plugins/plugin.SaveHtaccess/images/icn.png" style=" max-height: 80px;" />
			<div>
				<a class="nbr_button" href="/">CONTINUE</a>
			</div>
		</div>
	</div>
<?php include(__DIR__.DS.'footer.php'); ?>